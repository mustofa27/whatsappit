<?php

namespace App\Http\Controllers;

use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsappConversationController extends Controller
{
    /**
     * Get all conversations for an account.
     */
    public function index(Request $request): JsonResponse
    {
        // This should be protected with proper authentication in production
        $request->validate([
            'account_id' => 'required|integer|exists:whatsapp_accounts,id',
            'archived' => 'boolean',
            'limit' => 'integer|min:1|max:100',
            'offset' => 'integer|min:0',
        ]);

        $accountId = $request->input('account_id');
        $archived = $request->boolean('archived', false);
        $limit = $request->integer('limit', 20);
        $offset = $request->integer('offset', 0);

        $query = WhatsappConversation::where('whatsapp_account_id', $accountId);

        if ($archived) {
            $query->where('is_archived', true);
        } else {
            $query->where('is_archived', false);
        }

        $total = $query->count();
        $conversations = $query
            ->orderByDesc('last_message_at')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(function ($conversation) {
                return [
                    'id' => $conversation->id,
                    'contact_number' => $conversation->contact_number,
                    'contact_name' => $conversation->contact_name,
                    'last_message_at' => $conversation->last_message_at,
                    'unread_count' => $conversation->unread_count,
                    'is_archived' => $conversation->is_archived,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $conversations,
            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
            ],
        ]);
    }

    /**
     * Get conversation details with messages.
     */
    public function show(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer|exists:whatsapp_accounts,id',
            'contact_number' => 'required|string',
            'limit' => 'integer|min:1|max:100',
            'offset' => 'integer|min:0',
        ]);

        $accountId = $request->input('account_id');
        $contactNumber = $request->input('contact_number');
        $limit = $request->integer('limit', 20);
        $offset = $request->integer('offset', 0);

        $conversation = WhatsappConversation::where('whatsapp_account_id', $accountId)
            ->where('contact_number', $contactNumber)
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found',
            ], 404);
        }

        // Get messages
        $totalMessages = WhatsappMessage::where('whatsapp_account_id', $accountId)
            ->where('contact_number', $contactNumber)
            ->count();

        $messages = WhatsappMessage::where('whatsapp_account_id', $accountId)
            ->where('contact_number', $contactNumber)
            ->orderByDesc('created_at')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'direction' => $message->direction,
                    'message' => $message->message,
                    'message_type' => $message->message_type,
                    'media_url' => $message->media_url,
                    'status' => $message->status,
                    'created_at' => $message->created_at,
                    'external_id' => $message->external_id,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'conversation' => [
                    'id' => $conversation->id,
                    'contact_number' => $conversation->contact_number,
                    'contact_name' => $conversation->contact_name,
                    'last_message_at' => $conversation->last_message_at,
                    'unread_count' => $conversation->unread_count,
                    'is_archived' => $conversation->is_archived,
                ],
                'messages' => $messages,
                'pagination' => [
                    'total' => $totalMessages,
                    'limit' => $limit,
                    'offset' => $offset,
                ],
            ],
        ]);
    }

    /**
     * Mark conversation as read.
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer|exists:whatsapp_accounts,id',
            'contact_number' => 'required|string',
        ]);

        $conversation = WhatsappConversation::where('whatsapp_account_id', $request->input('account_id'))
            ->where('contact_number', $request->input('contact_number'))
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found',
            ], 404);
        }

        $conversation->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Conversation marked as read',
        ]);
    }

    /**
     * Archive conversation.
     */
    public function archive(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer|exists:whatsapp_accounts,id',
            'contact_number' => 'required|string',
        ]);

        $conversation = WhatsappConversation::where('whatsapp_account_id', $request->input('account_id'))
            ->where('contact_number', $request->input('contact_number'))
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found',
            ], 404);
        }

        $conversation->archive();

        return response()->json([
            'success' => true,
            'message' => 'Conversation archived',
        ]);
    }

    /**
     * Unarchive conversation.
     */
    public function unarchive(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer|exists:whatsapp_accounts,id',
            'contact_number' => 'required|string',
        ]);

        $conversation = WhatsappConversation::where('whatsapp_account_id', $request->input('account_id'))
            ->where('contact_number', $request->input('contact_number'))
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found',
            ], 404);
        }

        $conversation->unarchive();

        return response()->json([
            'success' => true,
            'message' => 'Conversation unarchived',
        ]);
    }
}
