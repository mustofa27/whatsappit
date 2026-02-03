<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'whatsapp_account_id',
        'contact_number',
        'contact_name',
        'last_message_at',
        'unread_count',
        'is_archived',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Get the WhatsApp account that owns the conversation.
     */
    public function whatsappAccount(): BelongsTo
    {
        return $this->belongsTo(WhatsappAccount::class);
    }

    /**
     * Get all messages in this conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'contact_number', 'contact_number')
            ->where('whatsapp_account_id', $this->whatsapp_account_id)
            ->orderBy('created_at');
    }

    /**
     * Get the latest message in this conversation.
     */
    public function latestMessage(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'contact_number', 'contact_number')
            ->where('whatsapp_account_id', $this->whatsapp_account_id)
            ->latest();
    }

    /**
     * Get unread incoming messages.
     */
    public function unreadMessages(): HasMany
    {
        return $this->messages()
            ->where('direction', 'incoming')
            ->where('message_status', 'delivered');
    }

    /**
     * Mark conversation as read.
     */
    public function markAsRead(): void
    {
        $this->unreadMessages()->update(['status' => 'read']);
        $this->update(['unread_count' => 0]);
    }

    /**
     * Archive conversation.
     */
    public function archive(): void
    {
        $this->update(['is_archived' => true]);
    }

    /**
     * Unarchive conversation.
     */
    public function unarchive(): void
    {
        $this->update(['is_archived' => false]);
    }
}
