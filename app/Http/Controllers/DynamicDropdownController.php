<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\District;
use App\Models\Zone;
use App\Models\Homecell;

class DynamicDropdownController extends Controller
{
    /**
     * Get districts based on church
     */
    public function getDistricts($church_id)
    {
        if (!$church_id) {
            return response()->json([], 400);
        }

        $districts = District::where('church_id', $church_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($districts, 200);
    }

    /**
     * Get zones based on district
     */
    public function getZones($district_id)
    {
        if (!$district_id) {
            return response()->json([], 400);
        }

        $zones = Zone::where('district_id', $district_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($zones, 200);
    }

    /**
     * Get homecells based on zone
     */
    public function getHomecells($zone_id)
    {
        if (!$zone_id) {
            return response()->json([], 400);
        }

        $homecells = Homecell::where('zone_id', $zone_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($homecells, 200);
    }
}
