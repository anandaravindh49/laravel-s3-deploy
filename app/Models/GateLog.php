<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'approved_visitor_id',
        'device_id',
        'visitor_name',
        'badge_number',
        'event_type',
        'event_at',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'event_at' => 'datetime',
        'metadata' => 'json',
    ];

    // Relationships
    public function approvedVisitor(): BelongsTo
    {
        return $this->belongsTo(ApprovedVisitor::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    // Scopes
    public function scopeCheckIns($query)
    {
        return $query->where('event_type', 'checkin');
    }

    public function scopeCheckOuts($query)
    {
        return $query->where('event_type', 'checkout');
    }

    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('event_at', $date);
    }

    // Methods
    public function isCheckIn(): bool
    {
        return $this->event_type === 'checkin';
    }

    public function isCheckOut(): bool
    {
        return $this->event_type === 'checkout';
    }
}
