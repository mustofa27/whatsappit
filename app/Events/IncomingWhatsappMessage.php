<?php

namespace App\Events;

use App\Models\WhatsappMessage;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncomingWhatsappMessage
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WhatsappMessage $message
    ) {}
}
