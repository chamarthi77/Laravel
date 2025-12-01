<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Cities + base coordinates
        $cities = [
            "California" => [38.63135041885934, -121.0858651423302],
            "Boston"     => [42.16671495801193, -71.22049727464025],
            "Chicago"    => [41.873082051552025, -87.6586204323278],
            "Houston"    => [29.6331478, -95.7010443],
            "Ohio"       => [39.42157675029737, -84.47636732023595],
        ];

        $titles = [
            "Fire outbreak near sector",
            "Gas leak detected in zone",
            "Power outage reported",
            "Flood warning issued",
            "Security breach alert",
        ];

        $descs = [
            "Team deployed for assessment.",
            "Public notified with precaution advisory.",
            "Minor disruption but under control.",
            "Authorities currently investigating.",
            "Situation stable, investigation ongoing.",
        ];

        // ⭐ GET ALL PROJECTS (internal id + external projectid)
        $projects = DB::table('projects')->get();

        foreach ($projects as $project) {

            $internalId = $project->id;           // ⭐ internal FK id → 1,2,3,4
            $externalId = $project->projectid;    // ⭐ external projectid → 101–104

            echo "➡ Seeding incidents for ProjectID: {$externalId} (Internal ID: {$internalId})\n";

            foreach ($cities as $city => $coords) {

                [$lat, $lng] = $coords;

                for ($i = 1; $i <= 5; $i++) {

                    $latOffset = (mt_rand(-250, 250)) / 1000;
                    $lngOffset = (mt_rand(-250, 250)) / 1000;

                    DB::table('incidents')->insert([
                        'title'       => $titles[array_rand($titles)] . " #" . rand(100, 999),
                        'description' => $descs[array_rand($descs)],

                        // ⭐ FIXED: USE INTERNAL ID (respects foreign key)
                        'project_id'  => $internalId,

                        'city'        => $city,
                        'latitude'    => $lat + $latOffset,
                        'longitude'   => $lng + $lngOffset,

                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ]);
                }
            }
        }

        echo "\n✅ IncidentSeeder Completed Successfully\n";
    }
}
