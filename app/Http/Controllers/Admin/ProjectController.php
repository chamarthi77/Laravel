<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(Request $request)
{
    $user = auth()->user();

    switch ($user->role) {
        case 'SUPER_ADMIN':
            $projects = Project::all();
            break;
        case 'COMMUNITY_ADMIN':
            $projects = Project::where('community_id', $user->community_id)->get();
            break;
        case 'ORG_MANAGER':
            $projects = Project::where('organization_id', $user->organization_id)->get();
            break;
        default:
            $projects = Project::where('id', $user->project_id)->get();
            break;
    }

    return response()->json(['data' => $projects]);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190', 'unique:projects,name'],
            'code' => ['required', 'string', 'max:50', 'unique:projects,code'],
            'description' => ['nullable', 'string'],
        ]);
        $project = Project::create($data);
        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        return response()->json($project, 200);
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:190', Rule::unique('projects', 'name')->ignore($project->id)],
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('projects', 'code')->ignore($project->id)],
            'description' => ['nullable', 'string'],
        ]);
        $project->update($data);
        return response()->json($project, 200);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['deleted' => true], 200);
    }
}

