<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'name',
        'sender_key',
        'sender_secret',
        'status',
        'provider',
        'phone_number_id',
        'waba_id',
        'access_token',
        'is_verified',
        'verification_code',
        'verification_code_sent_at',
        'external_id',
        'qr_code',
        'session_data',
        'last_connected_at',
    ];

    protected $casts = [
        'last_connected_at' => 'datetime',
        'verification_code_sent_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    protected $hidden = [
        'sender_secret',
        'session_data',
    ];

    /**
     * Get the user that owns the WhatsApp account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the messages for the WhatsApp account.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class);
    }

    /**
     * Check if the account is connected.
     */
    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    /**
     * Mark the account as connected.
     */
    public function markAsConnected(): void
    {
        $this->update([
            'status' => 'connected',
            'qr_code' => null,
            'last_connected_at' => now(),
        ]);
    }

    /**
     * Mark the account as disconnected.
     */
    public function markAsDisconnected(): void
    {
        $this->update([
            'status' => 'disconnected',
        ]);
    }
}
