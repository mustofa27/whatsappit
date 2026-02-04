<?php

namespace App\Listeners;

use App\Events\IncomingWhatsappMessage;
use App\Models\WhatsappConversation;
use App\Models\WhatsappContact;

class UpdateConversationOnIncomingMessage
{
    /**
     * Handle the event.
     */
    public function handle(IncomingWhatsappMessage $event): void
    {
        $message = $event->message;

        // Ensure contact exists for this incoming message
        $contact = WhatsappContact::updateOrCreate(
            [
                'whatsapp_account_id' => $message->whatsapp_account_id,
                'contact_number' => $message->contact_number,
            ],
            [
                'name' => null,
            ]
        );

        // Find or create conversation
        $conversation = WhatsappConversation::firstOrCreate(
            [
                'whatsapp_account_id' => $message->whatsapp_account_id,
                'contact_number' => $message->contact_number,
            ],
            [
                'contact_name' => $contact->name,
                'last_message_at' => $message->received_at,
                'unread_count' => 0,
            ]
        );

        // Update conversation
        $conversation->update([
            'last_message_at' => $message->received_at,
            'unread_count' => $conversation->unread_count + 1,
            'is_archived' => false,
            'contact_name' => $contact->name ?? $conversation->contact_name,
        ]);
    }
}
