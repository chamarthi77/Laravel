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

        if (!$projectId) {
            return response()->json(['status' => 'error', 'message' => 'Missing project_id'], 400);
        }

        // 🔹 First check if this is internal ID or external projectid
        $project = DB::table('projects')
            ->where('id', $projectId)
            ->orWhere('projectid', $projectId)
            ->first();

        if (!$project) {
            return response()->json(['status' => 'error', 'message' => 'Project not found'], 404);
        }

        // 🔹 Always use internal ID for incidents
        $incidents = DB::table('incidents')
            ->where('project_id', $project->id)
            ->get();

        return response()->json([
            'status' => 'success',
            'project_id' => $project->id,
            'count' => $incidents->count(),
            'data' => $incidents->values(),
        ]);
    }
}
