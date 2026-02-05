<?php

namespace App\Services;

use App\Models\SubscriptionPayment;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaypoolService
{
    protected string $baseUrl;
    protected string $accessToken;

    public function __construct()
    {
        $this->baseUrl = config('services.paypool.base_url');
        $this->accessToken = config('services.paypool.access_token');
    }

    /**
     * Create payment via Paypool
     */
    public function createPayment(UserSubscription $subscription, array $options = []): array
    {
        $user = $subscription->user;
        $plan = $subscription->plan;
        
        $externalId = 'WAIT-SUB-' . $subscription->id . '-' . time();
        $amount = $plan->price;

        $payload = [
            'external_id' => $externalId,
            'amount' => $amount,
            'currency' => 'IDR',
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone ?? '',
            'description' => 'Subscription: ' . $plan->name,
            'metadata' => [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
            ],
            'success_redirect_url' => $options['success_url'] ?? route('subscription.success'),
            'failure_redirect_url' => $options['failure_url'] ?? route('subscription.failed'),
        ];

        try {
            $response = Http::withToken($this->accessToken)
                ->post($this->baseUrl . '/api/v1/payments/create', $payload);

            if ($response->status() === 201 && $response->json('success')) {
                $data = $response->json('data');
                
                // Create payment record
                $payment = SubscriptionPayment::create([
                    'user_subscription_id' => $subscription->id,
                    'amount' => $amount,
                    'payment_gateway' => 'paypool',
                    'external_id' => $externalId,
                    'transaction_id' => 'paypool-' . $data['payment_id'],
                    'status' => 'pending',
                    'checkout_url' => $data['invoice_url'],
                    'expires_at' => $data['expired_at'] ?? now()->addDay(),
                    'metadata' => $data,
                ]);

                Log::info('Payment created via Paypool', [
                    'external_id' => $externalId,
                    'payment_id' => $data['payment_id'],
                ]);

                return [
                    'success' => true,
                    'invoice_url' => $data['invoice_url'],
                    'payment_id' => $data['payment_id'],
                    'payment' => $payment,
                ];
            }

            Log::error('Paypool payment creation failed', [
                'response' => $response->json(),
                'status' => $response->status(),
                'external_id' => $externalId,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create payment',
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Paypool API error', [
                'message' => $e->getMessage(),
                'subscription_id' => $subscription->id,
                'external_id' => $externalId,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get payment status from Paypool
     */
    public function getPayment(string $externalId): ?array
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->get($this->baseUrl . '/api/v1/payments/' . $externalId);

            if ($response->successful() && $response->json('success')) {
                return $response->json('data');
            }

            Log::warning('Failed to get payment from Paypool', [
                'external_id' => $externalId,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get payment from Paypool', [
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Cancel payment in Paypool
     */
    public function cancelPayment(string $externalId): bool
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->post($this->baseUrl . '/api/v1/payments/' . $externalId . '/cancel');

            if ($response->successful() && $response->json('success')) {
                Log::info('Payment cancelled via Paypool', ['external_id' => $externalId]);
                return true;
            }

            Log::warning('Failed to cancel payment via Paypool', [
                'external_id' => $externalId,
                'status' => $response->status(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to cancel payment via Paypool', [
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Verify webhook payload (validate external_id and status)
     */
    public function verifyWebhookPayload(array $payload): bool
    {
        // Check required fields
        if (!isset($payload['event']) || !isset($payload['payment'])) {
            Log::warning('Invalid Paypool webhook payload structure', ['payload' => $payload]);
            return false;
        }

        if (!isset($payload['payment']['external_id']) || !isset($payload['payment']['status'])) {
            Log::warning('Missing required payment fields in webhook', ['payload' => $payload]);
            return false;
        }

        return true;
    }

    /**
     * Process webhook payment update from Paypool
     */
    public function processWebhookPayment(array $payload): bool
    {
        if (!$this->verifyWebhookPayload($payload)) {
            return false;
        }

        $payment = $payload['payment'];
        $externalId = $payment['external_id'];
        $status = $payment['status'];

        try {
            // Find the subscription payment record
            $subscriptionPayment = SubscriptionPayment::where('external_id', $externalId)->first();

            if (!$subscriptionPayment) {
                Log::warning('Subscription payment not found for webhook', ['external_id' => $externalId]);
                return true; // Return true to acknowledge webhook
            }

            // Update payment record
            $subscriptionPayment->update([
                'status' => $status,
                'metadata' => array_merge($subscriptionPayment->metadata ?? [], $payment),
            ]);

            // Handle status change
            if ($status === 'paid') {
                $this->activateSubscription($subscriptionPayment);
            } elseif (in_array($status, ['expired', 'failed'])) {
                $this->handleFailedPayment($subscriptionPayment);
            }

            Log::info('Webhook payment processed', [
                'external_id' => $externalId,
                'status' => $status,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error processing webhook payment', [
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Activate subscription after successful payment
     */
    protected function activateSubscription(SubscriptionPayment $payment): void
    {
        $subscription = $payment->subscription;

        if ($subscription) {
            $subscription->update([
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => now()->addMonth(),
            ]);

            Log::info('Subscription activated', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
            ]);
        }
    }

    /**
     * Handle failed or expired payment
     */
    protected function handleFailedPayment(SubscriptionPayment $payment): void
    {
        $subscription = $payment->subscription;

        if ($subscription) {
            $subscription->update(['status' => 'failed']);

            Log::warning('Subscription payment failed', [
                'subscription_id' => $subscription->id,
                'reason' => $payment->status,
            ]);
        }
    }
}
