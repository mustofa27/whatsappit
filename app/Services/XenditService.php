<?php

namespace App\Services;

use App\Models\SubscriptionPayment;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.xendit.secret_key');
        $this->baseUrl = config('services.xendit.base_url', 'https://api.xendit.co');
    }

    /**
     * Create invoice for subscription payment
     */
    public function createInvoice(UserSubscription $subscription, array $options = []): array
    {
        $user = $subscription->user;
        $plan = $subscription->plan;
        
        $externalId = 'SUB-' . $subscription->id . '-' . time();
        $amount = $plan->price;

        $payload = [
            'external_id' => $externalId,
            'amount' => $amount,
            'payer_email' => $user->email,
            'description' => $plan->name . ' Subscription',
            'invoice_duration' => 86400, // 24 hours
            'currency' => 'IDR',
            'success_redirect_url' => $options['success_url'] ?? route('subscription.success'),
            'failure_redirect_url' => $options['failure_url'] ?? route('subscription.failed'),
            'customer' => [
                'given_names' => $user->name,
                'email' => $user->email,
            ],
            'items' => [
                [
                    'name' => $plan->name,
                    'quantity' => 1,
                    'price' => $amount,
                    'category' => 'subscription',
                ]
            ],
        ];

        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->post($this->baseUrl . '/v2/invoices', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Create payment record
                $payment = SubscriptionPayment::create([
                    'user_subscription_id' => $subscription->id,
                    'amount' => $amount,
                    'payment_gateway' => 'xendit',
                    'external_id' => $externalId,
                    'transaction_id' => $data['id'],
                    'status' => 'pending',
                    'checkout_url' => $data['invoice_url'],
                    'expires_at' => now()->addDay(),
                    'metadata' => $data,
                ]);

                return [
                    'success' => true,
                    'invoice_url' => $data['invoice_url'],
                    'invoice_id' => $data['id'],
                    'payment' => $payment,
                ];
            }

            Log::error('Xendit invoice creation failed', [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create invoice',
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Xendit API error', [
                'message' => $e->getMessage(),
                'subscription_id' => $subscription->id,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get invoice status from Xendit
     */
    public function getInvoice(string $invoiceId): ?array
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($this->baseUrl . '/v2/invoices/' . $invoiceId);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get invoice', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Verify webhook callback token
     */
    public function verifyWebhookToken(string $token): bool
    {
        $webhookToken = config('services.xendit.webhook_token');
        
        if (!$webhookToken) {
            return true; // Skip verification if not configured
        }

        return hash_equals($webhookToken, $token);
    }

    /**
     * Process webhook payment
     */
    public function processWebhookPayment(array $data): bool
    {
        try {
            $externalId = $data['external_id'] ?? null;
            $status = $data['status'] ?? null;
            $invoiceId = $data['id'] ?? null;

            if (!$externalId && !$invoiceId) {
                Log::warning('Invalid webhook data - no external_id or invoice id', ['data' => $data]);
                return false;
            }

            if (!$status) {
                Log::warning('Invalid webhook data - missing status', ['data' => $data]);
                return false;
            }

            // Try to find payment by external_id first
            $payment = SubscriptionPayment::where('external_id', $externalId)->first();

            // If not found, try by invoice_id (transaction_id)
            if (!$payment && $invoiceId) {
                $payment = SubscriptionPayment::where('transaction_id', $invoiceId)->first();
            }

            if (!$payment) {
                Log::warning('Payment not found', [
                    'external_id' => $externalId,
                    'invoice_id' => $invoiceId,
                ]);
                return false;
            }

            // Update payment status
            if ($status === 'PAID') {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'payment_method' => $data['payment_method'] ?? null,
                    'payment_channel' => $data['payment_channel'] ?? null,
                    'metadata' => $data,
                ]);

                // Activate subscription
                $subscription = $payment->subscription;
                $subscription->update([
                    'status' => 'active',
                    'started_at' => now(),
                    'expires_at' => now()->addMonth(),
                    'payment_method' => $data['payment_method'] ?? null,
                ]);

                Log::info('Subscription activated', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                ]);

                return true;
            } elseif ($status === 'EXPIRED') {
                $payment->update(['status' => 'expired']);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            return false;
        }
    }
}
