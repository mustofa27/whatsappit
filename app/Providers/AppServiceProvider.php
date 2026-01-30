<?php

namespace App\Providers;

use App\Services\MetaWhatsappService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MetaWhatsappService::class, function ($app) {
            return new MetaWhatsappService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // WhatsApp Send Message Rate Limiting
        // 20 requests per minute per sender_key to prevent spam
        RateLimiter::for('whatsapp-send', function ($request) {
            $senderKey = $request->input('sender_key', 'anonymous');
            
            return Limit::perMinute(20)
                ->by($senderKey)
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many messages sent. Please wait a moment before sending again.',
                        'error' => 'Rate limit exceeded. Maximum 20 messages per minute.',
                    ], 429);
                });
        });
    }
}
