<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Project;


class ProjectController extends Controller
{
    public function index(Request $request)
{
    $user = auth()->user();

    $projects = Project::query()
        ->select([
            'id',          // ✅ DB primary key
            'projectid',   // legacy external id
            'name',
        ])
        ->get()
        ->map(function ($project) {
            return [
 'id' => $project->id,
 'projectid' => $project->projectid,   // legacy
                'name' => $project->name,
                'permission' => 'admin', // or compute dynamically
            ];
        });

    return response()->json([
        'status' => 'success',
        'projectcount' => $projects->count(),
        'data' => $projects,
    ]);
}

}
