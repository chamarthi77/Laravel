<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncidentIOIController extends Controller
{
    public function index(Request $request)
    {
        // ---------------------------------------------------------
        // 1. Read INTERNAL DB project_id (1,2,3,4)
        // ---------------------------------------------------------
        $projectId = (int) $request->query('project_id');

        if (!$projectId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'project_id is required',
            ], 400);
        }

        // ---------------------------------------------------------
        // 2. Validate project exists (DB ID)
        // ---------------------------------------------------------
        if (!DB::table('projects')->where('id', $projectId)->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Project not found',
            ], 404);
        }

        // ---------------------------------------------------------
        // 3. Cities (seeded / allowed)
        // ---------------------------------------------------------
        $cities = ["California", "Boston", "Chicago", "Houston", "Ohio"];

        $finalIncidents = [];

        // ---------------------------------------------------------
        // 4. Get EXACTLY up to 5 incidents per city
        //    using INTERNAL DB project_id
        // ---------------------------------------------------------
        foreach ($cities as $city) {
            $records = DB::table('incidents')
                ->where('project_id', $projectId) // ✅ DB ID ONLY
                ->where('city', $city)
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();

            foreach ($records as $item) {
                $finalIncidents[] = [
                    'id'          => $item->id,
                    'title'       => $item->title,
                    'description' => $item->description,
                    'status'      => $item->status ?? 'active',
                    'city'        => $item->city,

                    'lat'         => (float) $item->latitude,
                    'lon'         => (float) $item->longitude,
                    'latitude'    => (float) $item->latitude,
                    'longitude'   => (float) $item->longitude,

                    // ✅ DB project id
                    'project_id'  => $projectId,

                    'created_at'  => $item->created_at,
                ];
            }
        }

        // ---------------------------------------------------------
        // 5. Response
        // ---------------------------------------------------------
        return response()->json([
            'status' => 'success',
            'count'  => count($finalIncidents),
            'data'   => $finalIncidents,
        ]);
    }
}
