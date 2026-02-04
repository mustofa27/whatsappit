<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'whatsapp_account_id',
        'name',
        'category',
        'language',
        'header_content',
        'header_type',
        'body_content',
        'footer_content',
        'buttons',
        'variables',
        'status',
        'meta_template_id',
        'usage_count',
        'last_used_at',
    ];

    protected $casts = [
        'buttons' => 'array',
        'variables' => 'array',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the WhatsApp account that owns the template.
     */
    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsappAccount::class);
    }

    /**
     * Scope to get templates by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get approved templates.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Extract variable placeholders from content.
     */
    public function extractVariables()
    {
        $content = $this->header_content . ' ' . $this->body_content . ' ' . $this->footer_content;
        preg_match_all('/\{\{(\d+)\}\}/', $content, $matches);
        
        $variables = [];
        if (!empty($matches[1])) {
            foreach (array_unique($matches[1]) as $index) {
                $variables[] = [
                    'index' => (int)$index,
                    'placeholder' => '{{' . $index . '}}',
                ];
            }
        }
        
        return collect($variables)->sortBy('index')->values()->all();
    }

    /**
     * Get preview with sample data.
     */
    public function getPreview(array $sampleData = [])
    {
        $preview = [
            'header' => $this->header_content,
            'body' => $this->body_content,
            'footer' => $this->footer_content,
        ];

        // Replace variables with sample data
        foreach ($preview as $key => $content) {
            if ($content) {
                foreach ($sampleData as $index => $value) {
                    $content = str_replace('{{' . $index . '}}', $value, $content);
                }
                $preview[$key] = $content;
            }
        }

        return $preview;
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get category label.
     */
    public function getCategoryLabelAttribute()
    {
        return match($this->category) {
            'MARKETING' => 'Marketing',
            'UTILITY' => 'Utility',
            'AUTHENTICATION' => 'Authentication',
            default => ucfirst(strtolower($this->category)),
        };
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-secondary',
            'pending' => 'bg-warning text-dark',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
