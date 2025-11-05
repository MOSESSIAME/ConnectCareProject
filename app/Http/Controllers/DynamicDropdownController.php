<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Homecell;
use App\Models\Zone;
use Illuminate\Http\JsonResponse;

class DynamicDropdownController extends Controller
{
    /**
     * GET /get-districts/{church_id}
     * Return districts for a church as [{id,name}]
     */
    public function getDistricts($church_id): JsonResponse
    {
        if (!is_numeric($church_id)) {
            return response()->json(['data' => []], 400);
        }

        $districts = District::where('church_id', (int) $church_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Front-end can read response.data or the array directly
        return response()->json(['data' => $districts], 200);
    }

    /**
     * GET /get-zones/{district_id}
     * Return zones for a district as [{id,name}]
     */
    public function getZones($district_id): JsonResponse
    {
        if (!is_numeric($district_id)) {
            return response()->json(['data' => []], 400);
        }

        $zones = Zone::where('district_id', (int) $district_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['data' => $zones], 200);
    }

    /**
     * GET /get-homecells/{zone_id}
     * Return homecells for a zone as [{id,name}]
     */
    public function getHomecells($zone_id): JsonResponse
    {
        if (!is_numeric($zone_id)) {
            return response()->json(['data' => []], 400);
        }

        $homecells = Homecell::where('zone_id', (int) $zone_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['data' => $homecells], 200);
    }
}
