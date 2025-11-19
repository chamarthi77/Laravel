<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncidentIOIController extends Controller
{
    public function index(Request $request)
    {
        // ===========================
        // 1. Read Query Parameters
        // ===========================
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $radius = $request->query('radius', 5000); // default 25 miles
        $dateRange = $request->query('date_range'); // e.g., 7d, 30d, "2025-01-01,2025-01-10"
        $filter = $request->query('filter'); // static filter from Home button
        $search = $request->query('search'); // full text search

        // ===========================
        // 2. Base query
        // ===========================
        $query = DB::table('incidents');

        // ===========================
        // 3. Geospatial filter (Haversine)
        // ===========================
        if ($lat && $lng) {
            $query->select('*', DB::raw("
                (3959 * acos(
                    cos(radians($lat)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) - radians($lng)) +
                    sin(radians($lat)) *
                    sin(radians(latitude))
                )) AS distance
            "))
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
        }

        // ===========================
        // 4. Date Range Filter
        // ===========================

        if ($dateRange) {

            // Format "7d" → last 7 days
            if (preg_match('/^(\d+)d$/', $dateRange, $match)) {
                $days = intval($match[1]);
                $start = Carbon::now()->subDays($days);
                $query->where('created_at', '>=', $start);
            }

            // Format ISO start,end → "2025-01-01,2025-01-10"
            if (strpos($dateRange, ',') !== false) {
                [$start, $end] = explode(',', $dateRange);
                $query->whereBetween('created_at', [
                    Carbon::parse($start),
                    Carbon::parse($end),
                ]);
            }
        }

        // ===========================
        // 5. Static filter from Home button
        // ===========================
        if ($filter) {
            $query->where('category', $filter);
        }

        // ===========================
        // 6. Full-text search
        // ===========================
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                  ->orWhere('description', 'LIKE', "%$search%")
                  ->orWhere('location', 'LIKE', "%$search%");
            });
        }

        // ===========================
        // 7. Return response
        // ===========================
        return response()->json([
            'status' => 'success',
            'count' => $query->count(),
            'data' => $query->get()
        ]);
    }
}
