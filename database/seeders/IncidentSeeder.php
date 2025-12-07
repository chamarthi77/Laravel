<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // -----------------------------------------
        // Cities + base coordinates
        // -----------------------------------------
        $cities = [
            "California" => [38.63135041885934, -121.0858651423302],
            "Boston"     => [42.16671495801193, -71.22049727464025],
            "Chicago"    => [41.873082051552025, -87.6586204323278],
            "Houston"    => [29.6331478, -95.7010443],
            "Ohio"       => [39.42157675029737, -84.47636732023595],
        ];

        // -----------------------------------------
        // Merge of YOUR titles + MAIN repo titles
        // -----------------------------------------
        $titles = [
            "Fire outbreak near sector",
            "Gas leak detected in zone",
            "Power outage reported",
            "Flood warning issued",
            "Security breach alert",
            "Bridge under structural watch",
            "Evacuation advisory issued",
            "Hurricane alert sounding",
            "Aftershock detected at region",
            "Chemical spill incident",
            "Emergency medical aid required",
            "Small explosion in facility",
            "Road blockage due to debris",
            "Storm damage reported",
            "Transformer malfunction event",
        ];

        // -----------------------------------------
        // Merge of YOUR descriptions + MAIN ones
        // -----------------------------------------
        $descs = [
            "Team deployed for assessment.",
            "Public notified with precaution advisory.",
            "Minor disruption but under control.",
            "Authorities currently investigating.",
            "Situation stable, investigation ongoing.",
            "Evacuation measures activated.",
            "Monitoring the situation closely.",
            "Response units mobilized.",
            "Impact minimal, no casualties reported.",
            "On-site team providing support.",
        ];

        // -----------------------------------------
        // Status values with weighted distribution
        // 'active' = 40%, 'pending' = 30%, 'resolved' = 20%, 'closed' = 10%
        // -----------------------------------------
        $statuses = [
            'active', 'active', 'active', 'active',     // 40%
            'pending', 'pending', 'pending',            // 30%
            'resolved', 'resolved',                     // 20%
            'closed',                                   // 10%
        ];

        // -----------------------------------------
        // GET all projects (internal ID + external ID)
        // -----------------------------------------
        $projects = DB::table('projects')->get();

        if ($projects->isEmpty()) {
            echo "⚠️ No projects found — skipping seeder.\n";
            return;
        }

        foreach ($projects as $project) {

            $internalId = $project->id;         // FK safe
            $externalId = $project->projectid;  // 101–104

            echo "➡ Seeding 25 incidents for ProjectID: {$externalId} (Internal ID: {$internalId})\n";

            foreach ($cities as $city => $coords) {

                [$lat, $lng] = $coords;

                // 5 incidents per city
                for ($i = 1; $i <= 5; $i++) {

                    $latOffset = mt_rand(-250, 250) / 1000;
                    $lngOffset = mt_rand(-250, 250) / 1000;

                    DB::table('incidents')->insert([
                        'title'       => $titles[array_rand($titles)] . " #" . rand(100, 999),
                        'description' => $descs[array_rand($descs)],
                        
                        // Status - randomly assigned with weighted distribution
                        'status'      => $statuses[array_rand($statuses)],

                        // INTERNAL ID → FK safe
                        'project_id'  => $internalId,

                        'city'        => $city,
                        'latitude'    => $lat + $latOffset,
                        'longitude'   => $lng + $lngOffset,

                        // Random historic timestamp (from main repo version)
                        'created_at'  => $now->copy()->subMinutes(rand(10, 5000)),
                        'updated_at'  => $now,
                    ]);
                }
            }
        }

        echo "\n✅ IncidentSeeder completed successfully.\n";
    }
}