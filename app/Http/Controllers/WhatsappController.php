<?php

namespace App\Http\Controllers;

use App\Models\WhatsappAccount;
use App\Models\WhatsappMessage;
use App\Services\MetaWhatsappService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WhatsappController extends Controller
{
    protected MetaWhatsappService $whatsappService;

    public function __construct(MetaWhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Send a WhatsApp message.
     * Public endpoint authenticated with sender_key and sender_secret.
     */
    public function send(Request $request): JsonResponse
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'sender_key' => 'required|string',
            'sender_secret' => 'required|string',
            'to' => 'required|string',
            'message' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:5120', // max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $hasTemplate = (bool) config('services.meta_whatsapp.default_template_name');
        $templateParams = (int) config('services.meta_whatsapp.default_template_params', 0);

        if (!$request->hasFile('image') && !$hasTemplate && !$request->filled('message')) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'message' => ['The message field is required when image is not present.'],
                ],
            ], 422);
        }

        if ($hasTemplate && $templateParams > 0 && !$request->filled('message')) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'message' => ['The message field is required for template parameters.'],
                ],
            ], 422);
        }

        // Authenticate sender using sender_key and sender_secret
        $account = WhatsappAccount::where('sender_key', $request->sender_key)
            ->where('sender_secret', $request->sender_secret)
            ->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid sender credentials',
            ], 401);
        }

        // Check if account is verified
        if (!$account->is_verified) {
            return response()->json([
                'success' => false,
                'message' => 'WhatsApp account is not verified. Please verify your account first.',
            ], 400);
        }

        // Handle image upload if present
        $mediaUrl = null;
        $mediaType = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('whatsapp-media', 'public');
            $mediaUrl = asset('storage/' . $path);
            $mediaType = 'image';
        }

        // Create message record
        $message = WhatsappMessage::create([
            'whatsapp_account_id' => $account->id,
            'direction' => 'outgoing',
            'contact_number' => $request->to,
            'sender_number' => $account->phone_number,
            'receiver_number' => $request->to,
            'message' => $request->message ?? '',
            'message_type' => $mediaType ? 'image' : 'text',
            'media_url' => $mediaUrl,
            'media_type' => $mediaType,
            'status' => 'pending',
        ]);

        // Send message
        try {
            // Add 2-3 second delay to simulate human behavior and prevent spam detection
            $lastMessage = WhatsappMessage::where('whatsapp_account_id', $account->id)
                ->where('created_at', '>', now()->subSeconds(5))
                ->latest()
                ->first();
                
            if ($lastMessage) {
                // Add random delay between 2-4 seconds
                sleep(rand(2, 4));
            }
            
            $this->whatsappService->sendMessage($account, $message);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => [
                    'message_id' => $message->id,
                    'to' => $message->contact_number,
                    'status' => $message->status,
                    'sent_at' => $message->sent_at,
                ],
            ], 200);
        } catch (\Exception $e) {
            $message->markAsFailed($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
