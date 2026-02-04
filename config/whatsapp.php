<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting to prevent spam and reduce ban risk
    |
    */
    
    'rate_limit' => [
        // Maximum messages per minute per account
        'max_per_minute' => env('WHATSAPP_MAX_PER_MINUTE', 20),
        
        // Maximum messages per hour per account
        'max_per_hour' => env('WHATSAPP_MAX_PER_HOUR', 100),
        
        // Maximum messages per day per account
        'max_per_day' => env('WHATSAPP_MAX_PER_DAY', 500),
        
        // Minimum delay between messages (seconds)
        'min_delay' => env('WHATSAPP_MIN_DELAY', 2),
        
        // Maximum delay between messages (seconds)
        'max_delay' => env('WHATSAPP_MAX_DELAY', 4),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Safety Features
    |--------------------------------------------------------------------------
    */
    
    'safety' => [
        // Enable human-like delays
        'enable_delays' => env('WHATSAPP_ENABLE_DELAYS', true),
        
        // Log all sent messages
        'log_messages' => env('WHATSAPP_LOG_MESSAGES', true),
        
        // Alert when approaching limits
        'alert_threshold' => 80, // percentage
    ],

    /*
    |--------------------------------------------------------------------------
    | Cost Tracking (Estimates)
    |--------------------------------------------------------------------------
    |
    | Used in Analytics to estimate costs per outgoing message.
    |
    */

    'cost' => [
        'per_message_idr' => env('WHATSAPP_COST_PER_MESSAGE_IDR', 500),
    ],
];
