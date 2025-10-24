<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'assigned_by',
        'assigned_to',
        'status',
        'team_id', // ✅ Added to allow linking assignments to teams
    ];

    /*
    |--------------------------------------------------------------------------
    | 🔗 Relationships
    |--------------------------------------------------------------------------
    */

    // Each assignment is linked to one church member (first-timer, convert, etc.)
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // The user who assigned the follow-up
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // The user responsible for doing the follow-up
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ✅ NEW: Each assignment belongs to a team
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // Each assignment can have many follow-up history entries
    public function histories()
    {
        return $this->hasMany(FollowUpHistory::class);
    }

    /*
    |--------------------------------------------------------------------------
    | 🧠 Helper Methods (Optional but Useful)
    |--------------------------------------------------------------------------
    */

    // Check if assignment is completed
    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }

    // Check if assignment is overdue
    public function isOverdue(): bool
    {
        return $this->status === 'Overdue';
    }
}
