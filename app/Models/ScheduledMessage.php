<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'whatsapp_account_id',
        'recipient_number',
        'message_content',
        'template_name',
        'template_params',
        'scheduled_at',
        'status',
        'retry_count',
        'max_retries',
        'error_message',
        'sent_at',
        'meta_message_id',
    ];

    protected $casts = [
        'template_params' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the WhatsApp account that owns the scheduled message.
     */
    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsappAccount::class);
    }

    /**
     * Scope to get pending messages ready to send.
     */
    public function scopeReadyToSend($query)
    {
        return $query->where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->orderBy('scheduled_at');
    }

    /**
     * Scope to get failed messages that can be retried.
     */
    public function scopeCanRetry($query)
    {
        return $query->where('status', 'failed')
            ->whereColumn('retry_count', '<', 'max_retries');
    }

    /**
     * Mark the message as sent.
     */
    public function markAsSent($metaMessageId)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'meta_message_id' => $metaMessageId,
            'error_message' => null,
        ]);
    }

    /**
     * Mark the message as failed.
     */
    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    /**
     * Mark the message as processing.
     */
    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
    }

    /**
     * Cancel the scheduled message.
     */
    public function cancel()
    {
        if ($this->status === 'pending') {
            $this->update(['status' => 'cancelled']);
            return true;
        }
        return false;
    }
}
