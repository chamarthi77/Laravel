<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;

class AdminIncidentController extends Controller
{
    public function store(Request $request)

    {

      \Log::info('CREATE INCIDENT PAYLOAD', [
        'payload' => $request->all(),
        'project_id_type' => gettype($request->project_id),
    ]);
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,pending,resolved,closed',
            'project_id'  => 'required|integer|exists:projects,id',
            'city'        => 'nullable|string|max:255',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'location'    => 'nullable|string|max:255',
        ]);

        $incident = Incident::create($validated);

        return response()->json([
            'ok'   => true,
            'data' => $incident,
        ], 201);
    }
    public function destroy(Incident $incident)
{
    $incident->delete();

    return response()->json([
        'success' => true,
        'deleted_id' => $incident->id,
    ]);
}


}
