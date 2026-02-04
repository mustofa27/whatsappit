<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_owner_id',
        'user_id',
        'role',
        'status',
        'invite_token',
        'invite_expires_at',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'invite_expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the team owner (the user who invited this member)
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_owner_id');
    }

    /**
     * Get the team member user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if invitation is still valid
     */
    public function isInvitationValid(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        if (!$this->invite_expires_at) {
            return false;
        }

        return $this->invite_expires_at->isFuture();
    }

    /**
     * Mark invitation as accepted
     */
    public function accept(): void
    {
        $this->update([
            'status' => 'active',
            'accepted_at' => now(),
        ]);
    }

    /**
     * Mark invitation as rejected
     */
    public function reject(): void
    {
        $this->update([
            'status' => 'inactive',
            'rejected_at' => now(),
        ]);
    }

    /**
     * Check if member is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if member is operator
     */
    public function isOperator(): bool
    {
        return in_array($this->role, ['admin', 'operator']);
    }
}
