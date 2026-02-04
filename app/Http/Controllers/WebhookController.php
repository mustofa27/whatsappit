<?php

namespace App\Http\Controllers;

use App\Models\WhatsappAccount;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle webhook verification from Meta
     * GET /webhook/whatsapp?hub.mode=subscribe&hub.challenge=...&hub.verify_token=...
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        // Find account by verify token
        $account = WhatsappAccount::where('webhook_verify_token', $token)->first();

        if (!$account) {
            Log::warning('Webhook verification failed: Invalid token', [
                'token' => substr($token ?? '', 0, 10) . '...',
                'mode' => $mode,
            ]);
            return response('Unauthorized', 403);
        }

        if ($mode === 'subscribe') {
            Log::info('Webhook verified for account', [
                'account_id' => $account->id,
                'account_name' => $account->account_name,
            ]);
            return response($challenge, 200);
        }

        return response('Invalid mode', 400);
    }

    /**
     * Handle incoming webhooks from Meta
     * POST /webhook/whatsapp
     */
    public function handle(Request $request)
    {
        Log::info('Incoming webhook', [
            'body' => $request->all(),
        ]);

        $token = $request->query('hub_verify_token');
        
        // Find account by verify token
        $account = WhatsappAccount::where('webhook_verify_token', $token)->first();

        if (!$account) {
            Log::warning('Webhook rejected: Invalid token', [
                'token' => substr($token ?? '', 0, 10) . '...',
            ]);
            return response('Unauthorized', 403);
        }

        $data = $request->json('entry.0.changes.0.value', []);

        // Handle different types of messages
        if (isset($data['messages'])) {
            foreach ($data['messages'] as $message) {
                $this->processMessage($account, $message);
            }
        }

        // Handle status updates
        if (isset($data['statuses'])) {
            foreach ($data['statuses'] as $status) {
                $this->processStatus($account, $status);
            }
        }

        return response('ok', 200);
    }

    /**
     * Process incoming message from Meta
     */
    protected function processMessage(WhatsappAccount $account, array $message)
    {
        try {
            $fromNumber = $message['from'] ?? null;
            $messageId = $message['id'] ?? null;
            $timestamp = $message['timestamp'] ?? time();
            $type = $message['type'] ?? null;

            if (!$fromNumber || !$messageId) {
                Log::warning('Invalid message structure', ['message' => $message]);
                return;
            }

            // Extract message content based on type
            $content = null;
            $mediaUrl = null;

            if ($type === 'text' && isset($message['text'])) {
                $content = $message['text']['body'] ?? null;
            } elseif ($type === 'image' && isset($message['image'])) {
                $mediaUrl = $message['image']['link'] ?? null;
                $content = $message['image']['caption'] ?? '[Image]';
            } elseif ($type === 'document' && isset($message['document'])) {
                $mediaUrl = $message['document']['link'] ?? null;
                $content = $message['document']['filename'] ?? '[Document]';
            } elseif ($type === 'audio' && isset($message['audio'])) {
                $mediaUrl = $message['audio']['link'] ?? null;
                $content = '[Audio]';
            } elseif ($type === 'video' && isset($message['video'])) {
                $mediaUrl = $message['video']['link'] ?? null;
                $content = $message['video']['caption'] ?? '[Video]';
            } else {
                $content = "[$type]";
            }

            if (!$content) {
                Log::warning('No message content extracted', [
                    'type' => $type,
                    'message' => $message,
                ]);
                return;
            }

            // Create or update message
            WhatsappMessage::updateOrCreate(
                ['message_id' => $messageId],
                [
                    'whatsapp_account_id' => $account->id,
                    'from_number' => $fromNumber,
                    'message_type' => $type,
                    'content' => $content,
                    'media_url' => $mediaUrl,
                    'direction' => 'incoming',
                    'status' => 'received',
                    'timestamp' => $timestamp,
                ]
            );

            Log::info('Message processed', [
                'account_id' => $account->id,
                'message_id' => $messageId,
                'from' => $fromNumber,
                'type' => $type,
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing message', [
                'error' => $e->getMessage(),
                'message' => $message,
            ]);
        }
    }

    /**
     * Process message status update from Meta
     */
    protected function processStatus(WhatsappAccount $account, array $status)
    {
        try {
            $messageId = $status['id'] ?? null;
            $statusValue = $status['status'] ?? null;

            if (!$messageId || !$statusValue) {
                return;
            }

            $message = WhatsappMessage::where('message_id', $messageId)->first();

            if ($message) {
                $message->update(['status' => $statusValue]);
                
                Log::info('Message status updated', [
                    'message_id' => $messageId,
                    'status' => $statusValue,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error processing status', [
                'error' => $e->getMessage(),
                'status' => $status,
            ]);
        }
    }
}
