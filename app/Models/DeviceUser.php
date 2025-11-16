<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'external_user_id',
        'name',
        'id_proof',
        'access_level',
        'status',
        'valid_from',
        'valid_until',
        'synced_at',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'synced_at' => 'datetime',
    ];

    // Relationships
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now()->toDateString());
            });
    }

    public function scopeByDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    // Methods
    public function isValid(): bool
    {
        return $this->status === 'active'
            && (!$this->valid_from || $this->valid_from <= now()->toDateString())
            && (!$this->valid_until || $this->valid_until >= now()->toDateString());
    }
}
