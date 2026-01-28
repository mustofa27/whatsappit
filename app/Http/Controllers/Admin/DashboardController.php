<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappAccount;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_accounts' => WhatsappAccount::count(),
            'connected_accounts' => WhatsappAccount::where('status', 'connected')->count(),
            'total_messages' => WhatsappMessage::count(),
            'messages_today' => WhatsappMessage::whereDate('created_at', today())->count(),
            'sent_messages' => WhatsappMessage::where('status', 'sent')->count(),
            'failed_messages' => WhatsappMessage::where('status', 'failed')->count(),
        ];

        $recent_messages = WhatsappMessage::with('whatsappAccount')
            ->latest()
            ->take(10)
            ->get();

        $accounts = WhatsappAccount::with(['messages' => function($query) {
            $query->latest()->take(5);
        }])->get();

        return view('admin.dashboard-new', compact('stats', 'recent_messages', 'accounts'));
    }
}
