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
        // 1. Read EXTERNAL project_id (example: 101,102,103,104)
        // ---------------------------------------------------------
        $externalProjectId = $request->query('project_id');

        if (!$externalProjectId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'project_id is required'
            ], 400);
        }

        // ---------------------------------------------------------
        // 2. Find project using EXTERNAL projectid
        //    projectid = 101/102/103/104
        //    id        = 1 / 2 / 3 / 4 (internal)
        // ---------------------------------------------------------
        $project = DB::table('projects')
            ->where('projectid', $externalProjectId)
            ->first();

        if (!$project) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Project not found'
            ], 404);
        }

        // ⭐ INTERNAL ID USED IN incidents.project_id
        $internalId = $project->id;

        // ---------------------------------------------------------
        // 3. Cities used in seeder
        // ---------------------------------------------------------
        $cities = ["California", "Boston", "Chicago", "Houston", "Ohio"];

        $finalIncidents = [];

        // ---------------------------------------------------------
        // 4. Get EXACTLY 5 incidents per city
        //    For the INTERNAL project_id (1,2,3,4)
        // ---------------------------------------------------------
        foreach ($cities as $city) {

            $records = DB::table('incidents')
                ->where('project_id', $internalId)  // ✔ ALWAYS INTERNAL ID
                ->where('city', $city)
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

                    // Send EXTERNAL id back to Flutter
                    'project_id'  => $externalProjectId,

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
            'data'   => $finalIncidents
        ]);
    }
}