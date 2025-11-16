<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovedVisitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_request_id',
        'visitor_name',
        'visitor_phone',
        'id_proof_type',
        'id_proof_number',
        'id_proof_image',
        'purpose_of_visit',
        'visit_date',
        'visit_time',
        'host_department',
        'host_person_name',
        'badge_number',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
        'visit_date' => 'date',
    ];

    // Relationships
    public function publicRequest(): BelongsTo
    {
        return $this->belongsTo(PublicRequest::class);
    }

    public function gateLogs(): HasMany
    {
        return $this->hasMany(GateLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('visit_date', '>=', now()->toDateString());
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('visit_date', $date);
    }

    // Methods
    public function hasCheckedIn(): bool
    {
        return $this->gateLogs()
            ->where('event_type', 'checkin')
            ->exists();
    }

    public function hasCheckedOut(): bool
    {
        return $this->gateLogs()
            ->where('event_type', 'checkout')
            ->exists();
    }

    public function getLastCheckIn()
    {
        return $this->gateLogs()
            ->where('event_type', 'checkin')
            ->latest('event_at')
            ->first();
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }
}
