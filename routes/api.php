<?php

use App\Http\Controllers\Api\EvolutionWebhookController;
use App\Http\Controllers\WhatsappController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// WhatsApp Send Message API - Public endpoint with sender_key & sender_secret authentication
// Rate limit: 20 requests per minute per account
Route::post('/send', [WhatsappController::class, 'send'])->middleware('throttle:whatsapp-send');

// Evolution API Webhook
Route::post('/webhooks/evolution', [EvolutionWebhookController::class, 'handle'])->name('webhooks.evolution');
