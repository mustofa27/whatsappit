<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduledMessage;
use App\Models\WhatsappAccount;
use App\Jobs\SendScheduledWhatsappMessage;
use Illuminate\Http\Request;

class ScheduledMessageController extends Controller
{
    /**
     * Display a listing of scheduled messages.
     */
    public function index(Request $request)
    {
        $query = ScheduledMessage::with('whatsappAccount');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by account
        if ($request->filled('account_id')) {
            $query->where('whatsapp_account_id', $request->account_id);
        }

        // Search by recipient
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('recipient_number', 'like', '%' . $request->search . '%')
                  ->orWhere('message_content', 'like', '%' . $request->search . '%');
            });
        }

        $scheduledMessages = $query->orderBy('scheduled_at', 'desc')->paginate(20);
        $accounts = WhatsappAccount::where('user_id', auth()->id())->get();

        return view('admin.scheduled-messages.index', compact('scheduledMessages', 'accounts'));
    }

    /**
     * Show the form for creating a new scheduled message.
     */
    public function create()
    {
        $accounts = WhatsappAccount::where('user_id', auth()->id())->get();
        return view('admin.scheduled-messages.create', compact('accounts'));
    }

    /**
     * Store a newly created scheduled message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_account_id' => 'required|exists:whatsapp_accounts,id',
            'recipient_number' => 'required|string',
            'message_content' => 'required|string',
            'scheduled_at' => 'required|date|after:now',
            'max_retries' => 'nullable|integer|min:0|max:5',
        ]);

        $validated['max_retries'] = $validated['max_retries'] ?? 3;

        $scheduledMessage = ScheduledMessage::create($validated);

        return redirect()
            ->route('admin.scheduled-messages.index')
            ->with('success', 'Message scheduled successfully for ' . $scheduledMessage->scheduled_at->format('Y-m-d H:i'));
    }

    /**
     * Display the specified scheduled message.
     */
    public function show(ScheduledMessage $scheduledMessage)
    {
        $scheduledMessage->load('whatsappAccount');
        return view('admin.scheduled-messages.show', compact('scheduledMessage'));
    }

    /**
     * Show the form for editing the scheduled message.
     */
    public function edit(ScheduledMessage $scheduledMessage)
    {
        if (!in_array($scheduledMessage->status, ['pending', 'failed'])) {
            return redirect()
                ->route('admin.scheduled-messages.index')
                ->with('error', 'Only pending or failed messages can be edited.');
        }

        $accounts = WhatsappAccount::where('user_id', auth()->id())->get();
        return view('admin.scheduled-messages.edit', compact('scheduledMessage', 'accounts'));
    }

    /**
     * Update the specified scheduled message.
     */
    public function update(Request $request, ScheduledMessage $scheduledMessage)
    {
        if (!in_array($scheduledMessage->status, ['pending', 'failed'])) {
            return redirect()
                ->route('admin.scheduled-messages.index')
                ->with('error', 'Only pending or failed messages can be updated.');
        }

        $validated = $request->validate([
            'whatsapp_account_id' => 'required|exists:whatsapp_accounts,id',
            'recipient_number' => 'required|string',
            'message_content' => 'required|string',
            'scheduled_at' => 'required|date|after:now',
            'max_retries' => 'nullable|integer|min:0|max:5',
        ]);

        // Reset status to pending if editing failed message
        if ($scheduledMessage->status === 'failed') {
            $validated['status'] = 'pending';
            $validated['retry_count'] = 0;
            $validated['error_message'] = null;
        }

        $scheduledMessage->update($validated);

        return redirect()
            ->route('admin.scheduled-messages.index')
            ->with('success', 'Scheduled message updated successfully.');
    }

    /**
     * Cancel the scheduled message.
     */
    public function cancel(ScheduledMessage $scheduledMessage)
    {
        if ($scheduledMessage->cancel()) {
            return redirect()
                ->route('admin.scheduled-messages.index')
                ->with('success', 'Message cancelled successfully.');
        }

        return redirect()
            ->route('admin.scheduled-messages.index')
            ->with('error', 'Only pending messages can be cancelled.');
    }

    /**
     * Retry a failed message.
     */
    public function retry(ScheduledMessage $scheduledMessage)
    {
        if ($scheduledMessage->status !== 'failed') {
            return redirect()
                ->route('admin.scheduled-messages.index')
                ->with('error', 'Only failed messages can be retried.');
        }

        // Reset to pending and dispatch job
        $scheduledMessage->update([
            'status' => 'pending',
            'scheduled_at' => now(),
            'error_message' => null,
        ]);

        SendScheduledWhatsappMessage::dispatch($scheduledMessage);

        return redirect()
            ->route('admin.scheduled-messages.index')
            ->with('success', 'Message retry dispatched to queue.');
    }

    /**
     * Delete the scheduled message.
     */
    public function destroy(ScheduledMessage $scheduledMessage)
    {
        if ($scheduledMessage->status === 'processing') {
            return redirect()
                ->route('admin.scheduled-messages.index')
                ->with('error', 'Cannot delete message while processing.');
        }

        $scheduledMessage->delete();

        return redirect()
            ->route('admin.scheduled-messages.index')
            ->with('success', 'Scheduled message deleted successfully.');
    }

    /**
     * Bulk operations.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:cancel,delete,retry',
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:scheduled_messages,id',
        ]);

        $messages = ScheduledMessage::whereIn('id', $request->message_ids)->get();
        $count = 0;

        foreach ($messages as $message) {
            if ($request->action === 'cancel' && $message->cancel()) {
                $count++;
            } elseif ($request->action === 'delete' && $message->status !== 'processing') {
                $message->delete();
                $count++;
            } elseif ($request->action === 'retry' && $message->status === 'failed') {
                $message->update([
                    'status' => 'pending',
                    'scheduled_at' => now(),
                    'error_message' => null,
                ]);
                SendScheduledWhatsappMessage::dispatch($message);
                $count++;
            }
        }

        return redirect()
            ->route('admin.scheduled-messages.index')
            ->with('success', "{$count} messages {$request->action}ed successfully.");
    }
}
