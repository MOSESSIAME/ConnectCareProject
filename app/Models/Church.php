<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Church extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location'];

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
