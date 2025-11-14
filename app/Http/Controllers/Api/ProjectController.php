<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Fetch projects with both internal (id) and display (projectid) fields
        $projects = DB::table('projects')->select('id', 'projectid', 'name', 'permission')->get();

        // ✅ Transform response to match what Flutter expects
        $formattedProjects = $projects->map(function ($proj) {
            return [
                'projectid'   => $proj->projectid ?? (string) $proj->id, // show 101 etc, or fallback to DB id
                'permission'  => $proj->permission ?? 'read',
                'name'        => $proj->name,
            ];
        });

        return response()->json([
            'status'        => 'success',
            'projectcount'  => $formattedProjects->count(),
            'data'          => $formattedProjects,
        ]);
    }
}
