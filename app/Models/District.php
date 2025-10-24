<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'church_id'];

    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }
}
