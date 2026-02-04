<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'features',
        'max_accounts',
        'max_users',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'integer',
        'max_accounts' => 'integer',
        'max_users' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to get only active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get limits display
     */
    public function getLimitsAttribute()
    {
        $limits = [];
        
        if ($this->max_accounts) {
            $limits['accounts'] = $this->max_accounts;
        } else {
            $limits['accounts'] = 'unlimited';
        }
        
        if ($this->max_users) {
            $limits['users'] = $this->max_users;
        } else {
            $limits['users'] = 'unlimited';
        }
        
        return $limits;
    }
}
