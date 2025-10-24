<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUpHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'follow_up_histories'; // ✅ explicitly define table name

    protected $fillable = [
        'assignment_id',
        'notes',
        'method',
        'outcome',
        'status',
    ];

    /**
     * A follow-up belongs to a specific assignment.
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Shortcut relationship — access member through assignment.
     */
    public function member()
    {
        return $this->hasOneThrough(
            Member::class,
            Assignment::class,
            'id',          // Foreign key on assignments table
            'id',          // Foreign key on members table
            'assignment_id',
            'member_id'
        );
    }
}
