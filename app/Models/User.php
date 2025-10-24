<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- Roles ---
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // --- Assignments ---
    public function assignmentsGiven()
    {
        return $this->hasMany(Assignment::class, 'assigned_by');
    }

    public function assignmentsReceived()
    {
        return $this->hasMany(Assignment::class, 'assigned_to');
    }

    // --- Leadership over zones / homecells (your existing)
    public function zoneLed()
    {
        return $this->hasOne(Zone::class, 'leader_id');
    }

    public function homecellLed()
    {
        return $this->hasOne(Homecell::class, 'leader_id');
    }

    // --- Teams ---
    // Teams this user is a member of
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user')->withTimestamps();
    }

    // Team this user leads (if any)
    public function leadsTeam()
    {
        return $this->hasOne(Team::class, 'leader_id');
    }

    // Convenience: assignments for teams I lead
    public function teamAssignmentsLed()
    {
        return $this->hasManyThrough(
            Assignment::class,
            Team::class,
            'leader_id',  // Team.leader_id -> this user
            'team_id',    // Assignment.team_id -> Team.id
            'id',         // User.id
            'id'          // Team.id
        );
    }
}
