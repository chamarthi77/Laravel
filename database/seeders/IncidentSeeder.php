<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Get all existing project IDs
        $projects = DB::table('projects')->pluck('id')->toArray();

        if (empty($projects)) {
            echo "⚠️ No projects found. Seeder skipped.\n";
            return;
        }

        $baseTitles = [
            'Fire outbreak near sector',
            'Gas leak detected in zone',
            'Power outage reported at block',
            'Bridge under structural watch',
            'Flood warning for low area',
            'Evacuation advisory issued',
            'Hurricane alert sounding',
            'Aftershock detected at region',
            'Chemical spill incident',
            'Emergency medical aid required',
            'Small explosion in facility',
            'Security breach alert',
            'Road blockage due to debris',
            'Heatwave stress alert',
            'Storm damage reported',
            'Transformer malfunction event',
        ];

        $baseDescriptions = [
            'Team deployed for assessment.',
            'Public notified and precautions issued.',
            'Minor disruption but under control.',
            'Authorities currently investigating.',
            'Evacuation measures activated.',
            'Monitoring the situation closely.',
            'Response units mobilized.',
            'Impact minimal, no casualties reported.',
            'Situation stable, investigation ongoing.',
            'On-site team providing support.',
        ];

        foreach ($projects as $projectId) {
            $records = [];

            // Generate 50 incidents per project
            for ($i = 1; $i <= 50; $i++) {
                $title = $baseTitles[array_rand($baseTitles)] . " #" . rand(100, 999);
                $description = $baseDescriptions[array_rand($baseDescriptions)];

                $records[] = [
                    'title'       => $title,
                    'description' => $description,
                    'project_id'  => $projectId,
                    'created_at'  => $now->copy()->subMinutes(rand(1, 5000)),
                    'updated_at'  => $now,
                ];
            }

            DB::table('incidents')->insert($records);

            echo "✔ Inserted 50 incidents for Project ID: $projectId\n";
        }
    }
}
