<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncidentController extends Controller
{
    // ======================================================
    // CREATE INCIDENT (DB project_id ONLY)
    // ======================================================
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'   => 'required|integer|exists:projects,id', // ✅ DB ID
            'org_id'       => 'required|integer',
            'community_id' => 'required|integer',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'status'       => 'required|in:active,pending,resolved,closed',
            'city'         => 'required|string|max:100',
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
        ]);

        $incidentId = DB::table('incidents')->insertGetId([
            'project_id'   => $data['project_id'], // ✅ DIRECT DB ID
            'org_id'       => $data['org_id'],
            'community_id' => $data['community_id'],
            'title'        => $data['title'],
            'description'  => $data['description'],
            'status'       => $data['status'],
            'city'         => $data['city'],
            'latitude'     => $data['latitude'],
            'longitude'    => $data['longitude'],
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'id'     => $incidentId,
        ], 201);
    }

    // ======================================================
    // DELETE INCIDENT
    // ======================================================
    public function destroy($id)
    {
        DB::table('incidents')->where('id', $id)->delete();

        return response()->json([
            'ok' => true,
            'deleted_id' => $id,
        ]);
    }

    // ======================================================
    // LIST INCIDENTS (DB project_id ONLY)
    // ======================================================
    public function index(Request $request)
    {
        $projectId = (int) $request->query('project_id'); // ✅ DB ID
        $page  = (int) $request->query('page', 1);
        $limit = (int) $request->query('limit', 10);

        if (!$projectId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing project_id',
            ], 400);
        }

        // Validate project exists (DB id)
        if (!DB::table('projects')->where('id', $projectId)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found',
            ], 404);
        }

        // Base query
        $query = DB::table('incidents')
            ->select([
                'id',
                'title as name',
                'title',
                'description',
                'status',
                'city',
                'latitude',
                'longitude',
                'created_at as created',
                'created_at',
                DB::raw('"System" as createdBy'),
                DB::raw('0 as distance'),
            ])
            ->where('project_id', $projectId)
            ->orderBy('id', 'asc');

        $total  = $query->count();
        $offset = ($page - 1) * $limit;

        $records = $query
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'status'    => 'success',
            'project_id'=> $projectId,
            'page'      => $page,
            'limit'     => $limit,
            'total'     => $total,
            'last_page' => ceil($total / $limit),
            'data'      => $records,
        ]);
    }
}
