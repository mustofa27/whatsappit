<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'whatsapp_account_id',
        'contact_number',
        'name',
        'email',
        'address',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function whatsappAccount(): BelongsTo
    {
        return $this->belongsTo(WhatsappAccount::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'contact_number', 'contact_number')
            ->where('whatsapp_account_id', $this->whatsapp_account_id);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(WhatsappConversation::class, 'contact_number', 'contact_number')
            ->where('whatsapp_account_id', $this->whatsapp_account_id);
    }
}
