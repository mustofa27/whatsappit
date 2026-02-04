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
            'headers' => $request->headers->all(),
        ]);

        // Verify webhook token (optional but recommended)
        $webhookToken = $request->header('x-callback-token');
        
        if ($webhookToken && !$this->xendit->verifyWebhookToken($webhookToken)) {
            Log::warning('Invalid Xendit webhook token', [
                'provided_token' => $webhookToken,
            ]);
            return response()->json(['status' => 'unauthorized'], 403);
        }

        // Validate required fields
        if (!$request->has('external_id') || !$request->has('status')) {
            Log::error('Missing required webhook fields', [
                'data' => $request->all(),
            ]);
            return response()->json(['status' => 'missing_fields'], 400);
        }

        // Process the webhook
        $success = $this->xendit->processWebhookPayment($request->all());

        if ($success) {
            Log::info('Webhook processed successfully', [
                'external_id' => $request->input('external_id'),
            ]);
            return response()->json(['status' => 'success']);
        }

        Log::error('Webhook processing failed', [
            'external_id' => $request->input('external_id'),
            'status' => $request->input('status'),
        ]);
        return response()->json(['status' => 'failed'], 400);
    }
}
