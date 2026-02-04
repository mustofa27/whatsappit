<?php

namespace App\Http\Controllers\Admin;

use App\Models\WhatsappAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WebhookSetupController extends Controller
{
    /**
     * Show webhook setup guide for an account
     */
    public function show(WhatsappAccount $account)
    {
        // Ensure user owns this account or is admin
        $user = auth()->user();
        if (!$user || ($account->user_id !== $user->id && !$user->is_admin)) {
            abort(403, 'Unauthorized');
        }
        
        $webhookUrl = route('webhook.meta', [], true);
        $verifyToken = $account->webhook_verify_token;
        
        return view('admin.accounts.webhook-setup', compact('account', 'webhookUrl', 'verifyToken'));
    }

    /**
     * Regenerate webhook verify token for an account
     */
    public function regenerateToken(WhatsappAccount $account)
    {
        // Ensure user owns this account
        if ($account->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }
        
        $account->update([
            'webhook_verify_token' => 'wh_' . bin2hex(random_bytes(32))
        ]);
        
        return redirect()->route('admin.accounts.webhook-setup', $account)
            ->with('success', 'Webhook verify token regenerated successfully. Make sure to update it in Meta Business Manager.');
    }

    /**
     * Test webhook connectivity
     */
    public function test(WhatsappAccount $account)
    {
        // Ensure user owns this account
        if ($account->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }
        
        // Return test response that Meta would receive
        return response()->json([
            'status' => 'configured',
            'webhook_url' => route('webhook.whatsapp', [], true),
            'verify_token' => 'configured',
            'account_id' => $account->id,
            'phone_number_id' => $account->phone_number_id,
        ]);
    }
}
