<?php

use App\Http\Controllers\WhatsappController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// WhatsApp Send Message API - Public endpoint with sender_key & sender_secret authentication
// Rate limit: 20 requests per minute per account
Route::post('/send', [WhatsappController::class, 'send'])->middleware('throttle:whatsapp-send');

// Meta WhatsApp Webhook
Route::get('/webhooks/meta', function (Request $request) {
    // Verify webhook from Meta
    $mode = $request->query('hub_mode');
    $token = $request->query('hub_verify_token');
    $challenge = $request->query('hub_challenge');
    
    if ($mode === 'subscribe' && $token === config('services.meta_whatsapp.verify_token')) {
        return response($challenge, 200);
    }
    
    return response('Forbidden', 403);
});

Route::post('/webhooks/meta', function (Request $request) {
    // Handle Meta webhook events
    app(\App\Services\MetaWhatsappService::class)->handleWebhook($request->all());
    return response()->json(['status' => 'ok']);
});

