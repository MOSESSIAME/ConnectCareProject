<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'type',
        'address',
        'from_other_church',
        'note',
        'foundation_class_completed',
        'service_unit_id',
        'homecell_id',
    ];

    public function serviceUnit()
    {
        return $this->belongsTo(ServiceUnit::class);
    }

    public function homecell()
    {
        return $this->belongsTo(Homecell::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function communications()
    {
        return $this->hasMany(Communication::class);
    }
}
