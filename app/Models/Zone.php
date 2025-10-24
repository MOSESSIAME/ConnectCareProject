<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zone extends Model
{
    use HasFactory;

    /**
     * Allow mass-assignment for all fields we persist.
     * (leader_id is optional; district_id is required in forms now)
     */
    protected $fillable = [
        'name',
        'leader_id',
        'district_id',
    ];

    /**
     * Zone → Leader (User)
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Zone → District  ✅ used by dynamic dropdowns (get-zones/{district_id})
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Zone → Homecells
     */
    public function homecells()
    {
        return $this->hasMany(Homecell::class);
    }

    /**
     * Zone → HomecellReports (through Homecells)
     */
    public function homecellReports()
    {
        return $this->hasManyThrough(HomecellReport::class, Homecell::class);
    }

    /**
     * Scope: filter zones by a district id.
     */
    public function scopeOfDistrict($query, $districtId)
    {
        return $query->where('district_id', $districtId);
    }
}
