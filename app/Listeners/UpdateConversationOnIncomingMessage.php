<?php

namespace App\Listeners;

use App\Events\IncomingWhatsappMessage;
use App\Models\WhatsappConversation;

class UpdateConversationOnIncomingMessage
{
    /**
     * Handle the event.
     */
    public function handle(IncomingWhatsappMessage $event): void
    {
        $message = $event->message;

        // Find or create conversation
        $conversation = WhatsappConversation::firstOrCreate(
            [
                'whatsapp_account_id' => $message->whatsapp_account_id,
                'contact_number' => $message->contact_number,
            ],
            [
                'contact_name' => null,
                'last_message_at' => $message->received_at,
                'unread_count' => 0,
            ]
        );

        // Update conversation
        $conversation->update([
            'last_message_at' => $message->received_at,
            'unread_count' => $conversation->unread_count + 1,
            'is_archived' => false,
        ]);
    }
}
