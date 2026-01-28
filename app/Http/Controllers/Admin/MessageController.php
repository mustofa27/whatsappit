<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = WhatsappMessage::with('whatsappAccount');

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by account
        if ($request->has('account_id') && $request->account_id != '') {
            $query->where('whatsapp_account_id', $request->account_id);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('recipient_number', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $messages = $query->latest()->paginate(50);
        $accounts = \App\Models\WhatsappAccount::all();

        return view('admin.messages.index-new', compact('messages', 'accounts'));
    }

    public function show(WhatsappMessage $message)
    {
        $message->load('whatsappAccount');
        return view('admin.messages.show-new', compact('message'));
    }
}
