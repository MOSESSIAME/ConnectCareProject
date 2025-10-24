<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Communication extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'channel',          // 'sms' | 'whatsapp' | 'email'
        'audience',         // 'all' | 'first_timers' | 'new_converts' | 'members' | 'by_zone' | 'by_district' | 'by_homecell'
        'filters',          // json: {zone_id,district_id,homecell_id}
        'status',           // 'draft' | 'queued' | 'sent' | 'failed'
        'scheduled_at',     // nullable datetime
        'sent_at',          // nullable datetime
        'created_by',       // user_id
    ];

    protected $casts = [
        'filters'      => 'array',
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
