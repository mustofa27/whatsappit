<?php

namespace App\Http\Controllers;

use App\Services\PaypoolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaypoolWebhookController extends Controller
{
    protected PaypoolService $paypool;

    public function __construct(PaypoolService $paypool)
    {
        $this->paypool = $paypool;
    }

    /**
     * Handle Paypool webhook callbacks
     */
    public function handle(Request $request)
    {
        // Log incoming webhook
        Log::info('Paypool webhook received', [
            'event' => $request->input('event'),
            'external_id' => $request->input('payment.external_id'),
            'status' => $request->input('payment.status'),
        ]);

        // Get webhook payload
        $payload = $request->all();

        // Verify and process the webhook
        $success = $this->paypool->processWebhookPayment($payload);

        if ($success) {
            Log::info('Paypool webhook processed successfully', [
                'external_id' => $request->input('payment.external_id'),
                'event' => $request->input('event'),
            ]);
            return response()->json(['status' => 'success'], 200);
        }

        Log::error('Paypool webhook processing failed', [
            'external_id' => $request->input('payment.external_id'),
            'event' => $request->input('event'),
        ]);
        return response()->json(['status' => 'failed'], 400);
    }
}
