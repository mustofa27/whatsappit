<?php

use App\Http\Controllers\Api\EvolutionWebhookController;
use App\Http\Controllers\WhatsappController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// WhatsApp Send Message API - Public endpoint with sender_key & sender_secret authentication
Route::post('/send', [WhatsappController::class, 'send']);

// Evolution API Webhook
Route::post('/webhooks/evolution', [EvolutionWebhookController::class, 'handle'])->name('webhooks.evolution');
