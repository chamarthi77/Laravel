<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        // ----------------------------------------------
        // 1. Read EXTERNAL project_id (example: 101,102)
        // ----------------------------------------------
        $externalProjectId = $request->query('project_id');
        $page = (int) $request->query('page', 1);
        $limit = (int) $request->query('limit', 10);

        if (!$externalProjectId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Missing project_id'
            ], 400);
        }

        // ------------------------------------------------------
        // 2. Lookup PROJECT using EXTERNAL projectid (IOI style)
        //    DO NOT use internal id here
        // ------------------------------------------------------
        $project = DB::table('projects')
            ->where('projectid', $externalProjectId)  // external!
            ->first();

        if (!$project) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Project not found'
            ], 404);
        }

        // internal id used inside incidents table
        $internalId = $project->id;

        // ------------------------------------------------------
        // 3. Base incident query
        // ------------------------------------------------------
        $query = DB::table('incidents')
            ->select([
                'id',
                'title as name',
                'title',
                'description',
                'status',
                DB::raw('city as city'),
                DB::raw('latitude as latitude'),
                DB::raw('longitude as longitude'),
                DB::raw('created_at as created'),
                'created_at',
                DB::raw('"System" as createdBy'),
                DB::raw('0 as distance'),
            ])
            ->where('project_id', $internalId)
            ->orderBy('id', 'asc');

        // ------------------------------------------------------
        // 4. Pagination math
        // ------------------------------------------------------
        $total = $query->count();
        $offset = ($page - 1) * $limit;

        $records = $query
            ->offset($offset)
            ->limit($limit)
            ->get();

        // ------------------------------------------------------
        // 5. Response
        // ------------------------------------------------------
        return response()->json([
            'status'          => 'success',
            'external_project_id' => $externalProjectId,
            'internal_project_id' => $internalId,
            'page'            => $page,
            'limit'           => $limit,
            'total'           => $total,
            'last_page'       => ceil($total / $limit),
            'data'            => $records,
        ], 200);
    }
}