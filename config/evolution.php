<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Evolution API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Evolution API WhatsApp service integration
    |
    */

    'api_url' => env('EVOLUTION_API_URL', 'http://localhost:8080'),
    'api_key' => env('EVOLUTION_API_KEY', ''),
    'webhook_url' => env('EVOLUTION_WEBHOOK_URL', ''),
    
    // Instance settings
    'instance_prefix' => env('EVOLUTION_INSTANCE_PREFIX', 'wa_'),
    
    // Timeouts
    'timeout' => env('EVOLUTION_TIMEOUT', 30),
    'connect_timeout' => env('EVOLUTION_CONNECT_TIMEOUT', 10),
];
