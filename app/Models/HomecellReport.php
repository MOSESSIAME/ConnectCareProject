<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomecellReport extends Model
{
    use HasFactory;

    // Adjust this to match your table columns
    protected $fillable = [
        'church_id',
        'district_id',
        'zone_id',
        'homecell_id',
        'males',
        'females',
        // 'children', // <-- keep only if you actually use it
        'first_timers',
        'new_converts',
        'testimonies',
        'submitted_by', // or 'user_id' if that’s your column
    ];

    /** Direct relations (require corresponding *_id columns on this table) */
    public function church()
    {
        return $this->belongsTo(Church::class, 'church_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function homecell()
    {
        return $this->belongsTo(Homecell::class, 'homecell_id');
    }

    public function submittedBy()
    {
        // change 'submitted_by' to 'user_id' if that's your FK name
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
