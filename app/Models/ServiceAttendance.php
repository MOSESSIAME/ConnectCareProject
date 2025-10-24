<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'males',
        'females',
        'children',
        'first_timers',
        'new_converts',
        'offering',
        'notes'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
