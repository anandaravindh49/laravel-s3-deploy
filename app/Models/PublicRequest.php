<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PublicRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'id_proof_type',
        'id_proof_number',
        'id_proof_image',
        'purpose_of_visit',
        'visit_date',
        'visit_time',
        'host_department',
        'host_person_name',
        'status',
        'approved_by',
        'rejection_reason',
        'approved_at',
        'additional_notes',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function approvedVisitor(): HasOne
    {
        return $this->hasOne(ApprovedVisitor::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('visit_date', $date);
    }

    // Methods
    public function canBeApproved(): bool
    {
        return $this->status === 'pending' && $this->visit_date >= now()->toDateString();
    }

    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }
}
