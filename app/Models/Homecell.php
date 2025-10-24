<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Homecell extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'zone_id', 'leader_id', 'provider_name', 'provider_phone'];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function reports()
    {
        return $this->hasMany(HomecellReport::class);
    }
}
