<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'leader_id'];

    // Team leader (User)
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    // Members of this team via pivot table team_user
    public function members()
    {
        return $this->belongsToMany(User::class, 'team_user')->withTimestamps();
    }

    // Assignments belonging to this team
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
