<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUpHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'follow_up_histories';

    protected $fillable = [
        'assignment_id',
        'notes',
        'method',
        'outcome',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /** ✅ Fixed dropdown lists */
    public const METHODS = [
        'Call',
        'Visit',
        'SMS',
        'WhatsApp',
        'Other',
    ];

    public const OUTCOMES = [
        'Reached',
        'No Answer',
        'Busy',
        'Switched Off',
        'Wrong Number',
        'Left Message',
        'Rescheduled',
        'Visited',
        'Prayed With',
        'Declined',
        'Other',
    ];

    public const STATUSES = [
        'Pending',
        'Completed',
    ];

    /**
     * Relationship: belongs to Assignment
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Accessor: $followUpHistory->member
     * Returns the related Member through Assignment
     */
    public function getMemberAttribute()
    {
        return $this->assignment?->member;
    }

    /**
     * Scope: eager-load related assignment + member
     */
    public function scopeWithMember($query)
    {
        return $query->with(['assignment.member']);
    }
}
