<?php

namespace App\Services;

use App\Models\WhatsappAccount;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaWhatsappService
{
    protected $accessToken;
    protected $apiVersion;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiVersion = config('services.meta_whatsapp.api_version', 'v21.0');
        $this->baseUrl = "https://graph.facebook.com/{$this->apiVersion}";
    }

    /**
     * Request phone number verification code
     */
    public function requestVerificationCode(WhatsappAccount $account): array
    {
        try {
            // In Meta Cloud API, phone verification is done through Meta Business Suite
            // This is a placeholder - actual verification is done via Meta Dashboard
            
            // Generate and save verification code for testing
            $code = rand(100000, 999999);
            
            $account->update([
                'verification_code' => $code,
                'verification_code_sent_at' => now(),
            ]);

            Log::info('Verification code generated', [
                'account_id' => $account->id,
                'code' => $code, // Remove in production
            ]);

            return [
                'success' => true,
                'message' => 'Verification code sent to ' . $account->phone_number,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to request verification code', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify phone number with code
     */
    public function verifyPhoneNumber(WhatsappAccount $account, string $code): bool
    {
        try {
            // Check if code matches and not expired (5 minutes)
            $inputCode = trim($code);
            $storedCode = $account->verification_code !== null
                ? (string) $account->verification_code
                : null;

            if ($storedCode === null || $storedCode !== $inputCode) {
                throw new \Exception('Invalid verification code');
            }

            if (!$account->verification_code_sent_at || $account->verification_code_sent_at->addMinutes(5)->isPast()) {
                throw new \Exception('Verification code expired');
            }

            // Mark as verified
            $account->update([
                'is_verified' => true,
                'status' => 'connected',
                'verification_code' => null,
                'verification_code_sent_at' => null,
                'last_connected_at' => now(),
            ]);

            Log::info('Phone number verified', [
                'account_id' => $account->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Verification failed', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Send WhatsApp message via Meta Cloud API
     */
    public function sendMessage(WhatsappAccount $account, WhatsappMessage $message): void
    {
        try {
            if (!$account->is_verified) {
                throw new \Exception('Account not verified');
            }

            $phoneNumberId = $account->phone_number_id;
            $accessToken = $account->access_token;

            if (!$phoneNumberId || !$accessToken) {
                throw new \Exception('Missing Meta credentials');
            }

            // Prepare message data
            $messageData = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $this->formatPhoneNumber($message->recipient_number),
            ];

            $templateName = config('services.meta_whatsapp.default_template_name');
            $templateLanguage = config('services.meta_whatsapp.default_template_language', 'en_US');
            $templateParams = (int) config('services.meta_whatsapp.default_template_params', 0);

            // Check if this is a new conversation (requires template)
            // Use template if configured, otherwise fallback to text/media
            if ($templateName) {
                $messageData['type'] = 'template';
                $messageData['template'] = [
                    'name' => $templateName,
                    'language' => [
                        'code' => $templateLanguage,
                    ],
                ];

                if ($templateParams > 0 && $message->message) {
                    $messageData['template']['components'] = [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => $message->message,
                                ],
                            ],
                        ],
                    ];
                }
            } elseif ($message->media_url) {
                // Send media message
                $messageData['type'] = $message->media_type ?? 'image';
                $messageData[$messageData['type']] = [
                    'link' => $message->media_url,
                ];
                
                if ($message->message) {
                    $messageData[$messageData['type']]['caption'] = $message->message;
                }
            } else {
                // Send text message
                $messageData['type'] = 'text';
                $messageData['text'] = [
                    'preview_url' => false,
                    'body' => $message->message,
                ];
            }

            // Send request to Meta API
            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/{$phoneNumberId}/messages", $messageData);

            if (!$response->successful()) {
                throw new \Exception('Meta API error: ' . $response->body());
            }

            $result = $response->json();

            // Update message status
            $message->update([
                'status' => 'sent',
                'sent_at' => now(),
                'external_id' => $result['messages'][0]['id'] ?? null,
            ]);

            Log::info('Meta WhatsApp message sent', [
                'message_id' => $message->id,
                'wamid' => $result['messages'][0]['id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send Meta WhatsApp message', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);

            $message->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Format phone number for Meta API
     */
    protected function formatPhoneNumber(string $number): string
    {
        // Remove all non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Ensure it starts with country code
        if (!str_starts_with($number, '62')) {
            $number = '62' . ltrim($number, '0');
        }
        
        return $number;
    }

    /**
     * Handle webhook from Meta
     */
    public function handleWebhook(array $data): void
    {
        Log::info('Meta webhook received', $data);

        // Handle different webhook events
        if (isset($data['entry'])) {
            foreach ($data['entry'] as $entry) {
                if (isset($entry['changes'])) {
                    foreach ($entry['changes'] as $change) {
                        if ($change['field'] === 'messages') {
                            $this->handleMessageWebhook($change['value']);
                        }
                    }
                }
            }
        }
    }

    /**
     * Handle message webhook
     */
    protected function handleMessageWebhook(array $value): void
    {
        // Handle status updates
        if (isset($value['statuses'])) {
            foreach ($value['statuses'] as $status) {
                $message = WhatsappMessage::where('external_id', $status['id'])->first();
                
                if ($message) {
                    $newStatus = match($status['status']) {
                        'delivered' => 'sent',
                        'read' => 'sent',
                        'failed' => 'failed',
                        default => $message->status,
                    };
                    
                    $message->update(['status' => $newStatus]);
                }
            }
        }

        // Handle incoming messages (optional)
        if (isset($value['messages'])) {
            Log::info('Incoming message received', $value['messages']);
        }
    }

    /**
     * Disconnect account
     */
    public function disconnect(WhatsappAccount $account): void
    {
        $account->update([
            'status' => 'disconnected',
            'is_verified' => false,
            'last_connected_at' => null,
        ]);

        Log::info('Account disconnected', [
            'account_id' => $account->id,
        ]);
    }
}
