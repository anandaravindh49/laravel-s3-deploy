<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module',
        'action',
        'auditable_type',
        'auditable_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_data' => 'json',
        'new_data' => 'json',
    ];

    public $timestamps = false;

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Methods
    public function getChanges(): array
    {
        $changes = [];
        if ($this->old_data && $this->new_data) {
            foreach ($this->new_data as $key => $value) {
                if (($this->old_data[$key] ?? null) !== $value) {
                    $changes[$key] = [
                        'old' => $this->old_data[$key] ?? null,
                        'new' => $value,
                    ];
                }
            }
        }
        return $changes;
    }
}
