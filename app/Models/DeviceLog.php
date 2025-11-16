<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'event_type',
        'log_data',
        'status',
        'error_message',
        'logged_at',
    ];

    protected $casts = [
        'log_data' => 'json',
        'logged_at' => 'datetime',
    ];

    // Relationships
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    // Scopes
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('logged_at', 'desc');
    }
}
