<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'name',
        'location',
        'device_type',
        'status',
        'ip_address',
        'firmware_version',
        'last_sync_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
        'last_sync_at' => 'datetime',
    ];

    // Relationships
    public function logs(): HasMany
    {
        return $this->hasMany(DeviceLog::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(DeviceUser::class);
    }

    public function gateLogs(): HasMany
    {
        return $this->hasMany(GateLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Methods
    public function isOnline(): bool
    {
        return $this->last_sync_at && $this->last_sync_at->diffInMinutes(now()) < 5;
    }

    public function getApprovedUsersList()
    {
        return $this->users()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now()->toDateString());
            })
            ->get();
    }
}
