<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappAccount;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use App\Services\MetaWhatsappService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ConversationController extends Controller
{
    protected MetaWhatsappService $whatsappService;

    public function __construct(MetaWhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Display all conversations
     */
    public function index(Request $request): View
    {
        $currentUser = auth()->user();
        $owner = $currentUser->getEffectiveOwner();
        
        // Get the first account belonging to the owner
        $account = WhatsappAccount::where('user_id', $owner->id)->first();

        if (!$account) {
            abort(404, 'No WhatsApp account configured');
        }

        $activeTab = $request->query('tab', 'conversations');

        // Get ALL conversations (both archived and non-archived)
        $conversations = WhatsappConversation::where('whatsapp_account_id', $account->id)
            ->orderByDesc('last_message_at')
            ->get()
            ->unique('contact_number')
            ->values()
            ->map(function ($conv) {
                return [
                    'id' => $conv->id,
                    'contact_number' => $conv->contact_number,
                    'contact_name' => $conv->contact_name,
                    'last_message_at' => $conv->last_message_at,
                    'unread_count' => $conv->unread_count,
                    'is_archived' => $conv->is_archived,
                ];
            })
            ->toArray();

        // Default values for conversation view
        $selectedContact = $request->query('contact');
        $selectedConversation = null;
        $messages = [];

        if ($activeTab === 'conversations' && $selectedContact) {
            $selectedConversation = WhatsappConversation::where('whatsapp_account_id', $account->id)
                ->where('contact_number', $selectedContact)
                ->first();

            if ($selectedConversation) {
                $selectedConversation = [
                    'id' => $selectedConversation->id,
                    'contact_number' => $selectedConversation->contact_number,
                    'contact_name' => $selectedConversation->contact_name,
                    'last_message_at' => $selectedConversation->last_message_at,
                    'unread_count' => $selectedConversation->unread_count,
                    'is_archived' => $selectedConversation->is_archived,
                ];

                $messages = WhatsappMessage::where('whatsapp_account_id', $account->id)
                    ->where('contact_number', $selectedContact)
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->map(function ($msg) {
                        return [
                            'id' => $msg->id,
                            'direction' => $msg->direction,
                            'message' => $msg->message,
                            'message_type' => $msg->message_type,
                            'media_url' => $msg->media_url,
                            'status' => $msg->status,
                            'created_at' => $msg->created_at,
                        ];
                    })
                    ->toArray();
            }
        }

        // Message Log data
        $messageLog = null;
        $accounts = null;
        $filters = [
            'status' => $request->query('status'),
            'account_id' => $request->query('account_id'),
            'search' => $request->query('search'),
        ];

        if ($activeTab === 'log') {
            $query = WhatsappMessage::with('whatsappAccount');

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['account_id'])) {
                $query->where('whatsapp_account_id', $filters['account_id']);
            }

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('contact_number', 'like', '%' . $search . '%')
                        ->orWhere('receiver_number', 'like', '%' . $search . '%')
                        ->orWhere('sender_number', 'like', '%' . $search . '%')
                        ->orWhere('message', 'like', '%' . $search . '%');
                });
            }

            $messageLog = $query->latest()->paginate(50)->appends($request->query());
            $accounts = WhatsappAccount::all();
        }

        return view('admin.conversations.index', [
            'account' => $account,
            'conversations' => $conversations,
            'selected_contact' => $selectedContact,
            'selected_conversation' => $selectedConversation,
            'messages' => $messages,
            'active_tab' => $activeTab,
            'message_log' => $messageLog,
            'accounts' => $accounts,
            'filters' => $filters,
        ]);
    }

    /**
     * Show single conversation
     */
    public function show(string $contactNumber): RedirectResponse
    {
        return redirect()->route('admin.conversations.index', ['contact' => $contactNumber]);
    }

    /**
     * Mark conversation as read
     */
    public function markAsRead(string $contactNumber): RedirectResponse
    {
        $account = WhatsappAccount::first();
        // Mark all incoming delivered messages as read for this contact
        WhatsappMessage::where('whatsapp_account_id', $account->id)
            ->where('contact_number', $contactNumber)
            ->where('direction', 'incoming')
            ->where('status', 'delivered')
            ->update(['status' => 'read']);

        // Reset unread_count for all conversations with this contact (handles duplicates)
        WhatsappConversation::where('whatsapp_account_id', $account->id)
            ->where('contact_number', $contactNumber)
            ->update(['unread_count' => 0]);

        return redirect()->route('admin.conversations.index', ['contact' => $contactNumber])
            ->with('success', 'Conversation marked as read');
    }

    /**
     * Archive conversation
     */
    public function archive(string $contactNumber): RedirectResponse
    {
        $account = WhatsappAccount::first();
        $conversation = WhatsappConversation::where('whatsapp_account_id', $account->id)
            ->where('contact_number', $contactNumber)
            ->first();

        if ($conversation) {
            $conversation->archive();
        }

        return redirect()->route('admin.conversations.index')
            ->with('success', 'Conversation archived');
    }

    /**
     * Unarchive conversation
     */
    public function unarchive(string $contactNumber): RedirectResponse
    {
        $account = WhatsappAccount::first();
        $conversation = WhatsappConversation::where('whatsapp_account_id', $account->id)
            ->where('contact_number', $contactNumber)
            ->first();

        if ($conversation) {
            $conversation->unarchive();
        }

        return redirect()->route('admin.conversations.index', ['contact' => $contactNumber])
            ->with('success', 'Conversation unarchived');
    }

    /**
     * Send message from admin
     */
    public function send(Request $request, string $contactNumber): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|min:1|max:1000',
        ]);

        try {
            $account = WhatsappAccount::first();

            // Create message record
            $message = WhatsappMessage::create([
                'whatsapp_account_id' => $account->id,
                'direction' => 'outgoing',
                'contact_number' => $contactNumber,
                'sender_number' => $account->phone_number,
                'receiver_number' => $contactNumber,
                'message' => $request->input('message'),
                'message_type' => 'text',
                'status' => 'pending',
            ]);

            // Send via Meta API
            $this->whatsappService->sendMessage($account, $message);

            return redirect()->route('admin.conversations.index', ['contact' => $contactNumber])
                ->with('success', 'Message sent successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.conversations.index', ['contact' => $contactNumber])
                ->with('error', 'Failed to send message: ' . $e->getMessage());
        }
    }
}
