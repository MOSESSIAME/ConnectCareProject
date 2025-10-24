<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // A service unit can have many members
    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
