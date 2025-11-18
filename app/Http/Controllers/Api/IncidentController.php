<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->query('project_id');
        $page      = (int) $request->query('page', 1);
        $limit     = (int) $request->query('limit', 10);

        if (!$projectId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing project_id'
            ], 400);
        }

        // Resolve internal/external project id
        $project = DB::table('projects')
            ->where('id', $projectId)
            ->orWhere('projectid', $projectId)
            ->first();

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found'
            ], 404);
        }

        // Base query
$query = DB::table('incidents')
    ->select([
        'id',
        'title as name',
        'description',
        'project_id',
        DB::raw('created_at as created'),
        DB::raw('"System" as createdBy'),
        DB::raw('0 as distance'),
    ])
    ->where('project_id', $project->id)
    ->orderBy('id', 'asc');



        // Total count
        $total = $query->count();

        // Pagination math
        $offset = ($page - 1) * $limit;

        // Fetch paginated data
        $records = $query->offset($offset)->limit($limit)->get();

        return response()->json([
            'status'     => 'success',
            'project_id' => $project->id,
            'page'       => $page,
            'limit'      => $limit,
            'total'      => $total,
            'last_page'  => ceil($total / $limit),
            'data'       => $records,
        ]);
    }
}
