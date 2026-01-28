<?php

namespace App\Services;

use App\Models\WhatsappAccount;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected $apiUrl;
    protected $apiKey;
    protected $instancePrefix;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('evolution.api_url'), '/');
        $this->apiKey = config('evolution.api_key');
        $this->instancePrefix = config('evolution.instance_prefix');
    }

    /**
     * Get instance name for account
     */
    protected function getInstanceName(WhatsappAccount $account): string
    {
        return $this->instancePrefix . $account->id;
    }

    /**
     * Make HTTP request to Evolution API
     */
    protected function makeRequest(string $method, string $endpoint, array $data = [])
    {
        $url = $this->apiUrl . $endpoint;
        
        $headers = [
            'apikey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout(config('evolution.timeout'))
                ->{strtolower($method)}($url, $data);

            if (!$response->successful()) {
                Log::error('Evolution API error', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Evolution API error: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Evolution API request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create and initialize a WhatsApp instance
     */
    public function initialize(WhatsappAccount $account): array
    {
        try {
            $instanceName = $this->getInstanceName($account);

            // Create instance
            $response = $this->makeRequest('POST', '/instance/create', [
                'instanceName' => $instanceName,
                'qrcode' => true,
                'integration' => 'WHATSAPP-BAILEYS',
            ]);

            Log::info('WhatsApp instance created', [
                'account_id' => $account->id,
                'instance' => $instanceName,
            ]);

            // Connect instance
            $connectResponse = $this->makeRequest('POST', "/instance/connect/{$instanceName}");

            // Update account status
            $account->update([
                'status' => 'connecting',
                'session_data' => json_encode([
                    'instance_name' => $instanceName,
                    'created_at' => now(),
                ]),
            ]);

            return [
                'instance' => $instanceName,
                'qrcode' => $connectResponse['qrcode'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to initialize WhatsApp account', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            
            $account->update(['status' => 'failed']);
            throw $e;
        }
    }

    /**
     * Get QR code for instance
     */
    public function getQRCode(WhatsappAccount $account): ?string
    {
        try {
            $instanceName = $this->getInstanceName($account);
            
            $response = $this->makeRequest('GET', "/instance/connect/{$instanceName}");
            
            return $response['qrcode']['base64'] ?? null;
        } catch (\Exception $e) {
            Log::error('Failed to get QR code', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Check instance connection status
     */
    public function checkStatus(WhatsappAccount $account): array
    {
        try {
            $instanceName = $this->getInstanceName($account);
            
            $response = $this->makeRequest('GET', "/instance/connectionState/{$instanceName}");
            
            $state = $response['state'] ?? 'close';
            $isConnected = $state === 'open';

            // Update account status
            if ($isConnected && $account->status !== 'connected') {
                $account->update([
                    'status' => 'connected',
                    'last_connected_at' => now(),
                ]);
            } elseif (!$isConnected && $account->status === 'connected') {
                $account->update(['status' => 'disconnected']);
            }

            return [
                'state' => $state,
                'connected' => $isConnected,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to check status', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'state' => 'error',
                'connected' => false,
            ];
        }
    }

    /**
     * Send a WhatsApp message
     */
    public function sendMessage(WhatsappAccount $account, WhatsappMessage $message): void
    {
        try {
            $instanceName = $this->getInstanceName($account);
            
            // Prepare message data
            $data = [
                'number' => $this->formatPhoneNumber($message->recipient_number),
            ];

            // Check if it's a text or media message
            if ($message->media_url) {
                // Send media message
                $data['mediatype'] = $message->media_type ?? 'image';
                $data['media'] = $message->media_url;
                if ($message->message) {
                    $data['caption'] = $message->message;
                }
                
                $endpoint = "/message/sendMedia/{$instanceName}";
            } else {
                // Send text message
                $data['text'] = $message->message;
                $endpoint = "/message/sendText/{$instanceName}";
            }

            $response = $this->makeRequest('POST', $endpoint, $data);

            // Update message status
            $message->markAsSent();
            $message->update(['sent_at' => now()]);

            Log::info('WhatsApp message sent', [
                'message_id' => $message->id,
                'recipient' => $message->recipient_number,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp message', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
            
            $message->markAsFailed();
            $message->update(['error_message' => $e->getMessage()]);
            
            throw $e;
        }
    }

    /**
     * Disconnect WhatsApp instance
     */
    public function disconnect(WhatsappAccount $account): void
    {
        try {
            $instanceName = $this->getInstanceName($account);
            
            $this->makeRequest('DELETE', "/instance/logout/{$instanceName}");
            
            $account->update([
                'status' => 'disconnected',
                'qr_code' => null,
            ]);

            Log::info('WhatsApp instance disconnected', [
                'account_id' => $account->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to disconnect WhatsApp instance', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Delete WhatsApp instance
     */
    public function deleteInstance(WhatsappAccount $account): void
    {
        try {
            $instanceName = $this->getInstanceName($account);
            
            $this->makeRequest('DELETE', "/instance/delete/{$instanceName}");

            Log::info('WhatsApp instance deleted', [
                'account_id' => $account->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete WhatsApp instance', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Format phone number for WhatsApp
     */
    protected function formatPhoneNumber(string $number): string
    {
        // Remove all non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Add @s.whatsapp.net if not present
        if (!str_contains($number, '@')) {
            $number = $number . '@s.whatsapp.net';
        }
        
        return $number;
    }

    /**
     * Handle webhook from Evolution API
     */
    public function handleWebhook(array $data): void
    {
        try {
            $event = $data['event'] ?? null;
            $instanceName = $data['instance'] ?? null;

            if (!$instanceName) {
                Log::warning('Webhook received without instance name', ['data' => $data]);
                return;
            }

            // Extract account ID from instance name
            $accountId = (int) str_replace($this->instancePrefix, '', $instanceName);
            $account = WhatsappAccount::find($accountId);

            if (!$account) {
                Log::warning('Webhook for unknown account', ['instance' => $instanceName]);
                return;
            }

            Log::info('Webhook received', [
                'event' => $event,
                'account_id' => $account->id,
                'data' => $data,
            ]);

            // Handle different webhook events
            switch ($event) {
                case 'connection.update':
                    $this->handleConnectionUpdate($account, $data);
                    break;
                    
                case 'messages.upsert':
                    $this->handleMessageUpdate($account, $data);
                    break;
                    
                case 'qrcode.updated':
                    $this->handleQRCodeUpdate($account, $data);
                    break;
                    
                default:
                    Log::info('Unhandled webhook event', ['event' => $event]);
            }
        } catch (\Exception $e) {
            Log::error('Webhook handling failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
        }
    }

    /**
     * Handle connection status update
     */
    protected function handleConnectionUpdate(WhatsappAccount $account, array $data): void
    {
        $state = $data['data']['state'] ?? null;

        if ($state === 'open') {
            $account->update([
                'status' => 'connected',
                'last_connected_at' => now(),
                'qr_code' => null,
            ]);
            Log::info('Account connected', ['account_id' => $account->id]);
        } elseif ($state === 'close') {
            $account->update(['status' => 'disconnected']);
            Log::info('Account disconnected', ['account_id' => $account->id]);
        }
    }

    /**
     * Handle message status update
     */
    protected function handleMessageUpdate(WhatsappAccount $account, array $data): void
    {
        // Handle message delivery status updates
        $messageData = $data['data'] ?? [];
        
        // You can update message status based on webhook data
        Log::info('Message update received', [
            'account_id' => $account->id,
            'message_data' => $messageData,
        ]);
    }

    /**
     * Handle QR code update
     */
    protected function handleQRCodeUpdate(WhatsappAccount $account, array $data): void
    {
        $qrCode = $data['data']['qrcode'] ?? null;
        
        if ($qrCode) {
            $account->update(['qr_code' => $qrCode]);
            Log::info('QR code updated', ['account_id' => $account->id]);
        }else {
                $this->sendTextMessage($account, $message, $recipientNumber);
            }
    }
}
