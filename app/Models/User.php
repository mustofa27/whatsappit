<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function currentPlan()
    {
        $subscription = $this->activeSubscription;
        return $subscription ? $subscription->plan : null;
    }

    public function whatsappAccounts()
    {
        return $this->hasMany(WhatsappAccount::class);
    }

    /**
     * Get the maximum number of WhatsApp accounts allowed by current subscription
     */
    public function getMaxWhatsappAccounts(): int
    {
        $plan = $this->currentPlan();
        
        // Default to 1 account if no active subscription
        if (!$plan) {
            return 1;
        }

        return $plan->max_accounts ?? 1;
    }

    /**
     * Get current count of WhatsApp accounts
     */
    public function getWhatsappAccountCount(): int
    {
        return $this->whatsappAccounts()->count();
    }

    /**
     * Check if user can create more WhatsApp accounts
     */
    public function canCreateWhatsappAccount(): bool
    {
        return $this->getWhatsappAccountCount() < $this->getMaxWhatsappAccounts();
    }

    /**
     * Get remaining account slots
     */
    public function getRemainingAccountSlots(): int
    {
        return max(0, $this->getMaxWhatsappAccounts() - $this->getWhatsappAccountCount());
    }

        /**
         * Get the maximum number of team members allowed by current subscription
         */
        public function getMaxTeamMembers(): int
        {
            $plan = $this->currentPlan();
        
            // Default to 1 (just the owner) if no active subscription
            if (!$plan) {
                return 1;
            }

            return $plan->max_users ?? 1;
        }

        /**
         * Get current count of active team members (excluding owner)
         */
        public function getTeamMemberCount(): int
        {
            return $this->teamMembers()
                ->where('status', 'active')
                ->count();
        }

        /**
         * Check if user can add more team members
         */
        public function canAddTeamMember(): bool
        {
            // Count includes invited (pending) members too
            $totalInvited = $this->teamMembers()
                ->whereIn('status', ['active', 'pending'])
                ->count();
        
            return $totalInvited < ($this->getMaxTeamMembers() - 1);
        }

        /**
         * Get remaining team member slots
         */
        public function getRemainingTeamSlots(): int
        {
            $max = $this->getMaxTeamMembers() - 1; // -1 because owner takes one slot
            $invited = $this->teamMembers()
                ->whereIn('status', ['active', 'pending'])
                ->count();
        
            return max(0, $max - $invited);
        }

        /**
         * Get all team members (active and pending)
         */
        public function teamMembers()
        {
            return $this->hasMany(TeamMember::class, 'team_owner_id');
        }

        /**
         * Get active team members
         */
        public function activeTeamMembers()
        {
            return $this->teamMembers()
                ->where('status', 'active')
                ->with('user');
        }

        /**
         * Get pending invitations
         */
        public function pendingInvitations()
        {
            return $this->teamMembers()
                ->where('status', 'pending')
                ->with('user');
        }

        /**
         * Get teams this user is member of
         */
        public function memberOfTeams()
        {
            return $this->hasMany(TeamMember::class, 'user_id');
        }

        /**
         * Get the effective account owner for this user
         * If user is a team member, returns the team owner
         * Otherwise returns the user themselves
         */
        public function getEffectiveOwner(): User
        {
            // Check if user is an active team member
            $teamMembership = $this->memberOfTeams()
                ->where('status', 'active')
                ->with('owner')
                ->first();

            if ($teamMembership && $teamMembership->owner->hasActiveSubscription()) {
                return $teamMembership->owner;
            }

            // Return self if no active team membership or owner has no subscription
            return $this;
        }
}