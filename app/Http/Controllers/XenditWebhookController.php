<?php

namespace App\Http\Controllers;

use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    protected XenditService $xendit;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
    }

    /**
     * Handle Xendit webhook callbacks
     */
    public function handle(Request $request)
    {
        // Log incoming webhook
        Log::info('Xendit webhook received', [
            'data' => $request->all(),
        ]);

        // Verify webhook token (optional but recommended)
        $webhookToken = $request->header('x-callback-token');
        
        if ($webhookToken && !$this->xendit->verifyWebhookToken($webhookToken)) {
            Log::warning('Invalid Xendit webhook token');
            return response('Unauthorized', 403);
        }

        // Process the webhook
        $success = $this->xendit->processWebhookPayment($request->all());

        if ($success) {
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'failed'], 400);
    }
}
