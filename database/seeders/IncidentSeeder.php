<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ---------------------------
        // 📍 Chicago Base Coordinates
        // ---------------------------
        $baseLat = 41.8781;
        $baseLng = -87.6298;

        // Generate small random variation around Chicago
        $randomOffset = function ($range = 0.08) {
            return (rand(-1000, 1000) / 1000) * $range;
        };

        // ---------------------------------------------------
        // Insert first 14 sample incidents (Project 1–4)
        // ---------------------------------------------------
        DB::table('incidents')->insert([
            // 🔹 Project 1
            [
                'title' => 'Fire near North Zone',
                'description' => 'Warehouse fire, moderate intensity',
                'project_id' => 1,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Gas Leak Reported',
                'description' => 'Leak detected in industrial area',
                'project_id' => 1,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Power Outage',
                'description' => 'Several units affected by blackout',
                'project_id' => 1,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Minor Explosion',
                'description' => 'Small explosion at north substation',
                'project_id' => 1,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            // 🔹 Project 2
            [
                'title' => 'Flood Warning Issued',
                'description' => 'Riverside flooding alert issued for lowlands',
                'project_id' => 2,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Bridge Under Watch',
                'description' => 'Structural monitoring ongoing due to rising water',
                'project_id' => 2,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Evacuation Advisory',
                'description' => 'Residents advised to move to higher ground',
                'project_id' => 2,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            // 🔹 Project 3
            [
                'title' => 'Earthquake Aftershock',
                'description' => 'Minor aftershock detected 10km east',
                'project_id' => 3,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Tremor Felt Downtown',
                'description' => 'Light vibration felt, no structural damage',
                'project_id' => 3,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Inspection Teams Deployed',
                'description' => 'Crews dispatched for safety checks',
                'project_id' => 3,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Ground Stabilization',
                'description' => 'Work ongoing to reinforce affected zones',
                'project_id' => 3,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],

            // 🔹 Project 4
            [
                'title' => 'Hurricane Warning Issued',
                'description' => 'Hurricane alert issued for coastal areas',
                'project_id' => 4,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Shelters Activated',
                'description' => 'Emergency shelters opened for evacuees',
                'project_id' => 4,
                'latitude' => $baseLat + $randomOffset(),
                'longitude' => $baseLng + $randomOffset(),
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // ----------------------------------------------
        // Bulk generate 50 random incidents per project
        // ----------------------------------------------
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

            for ($i = 1; $i <= 50; $i++) {
                $records[] = [
                    'title'       => $baseTitles[array_rand($baseTitles)] . " #" . rand(100, 999),
                    'description' => $baseDescriptions[array_rand($baseDescriptions)],
                    'project_id'  => $projectId,
                    'latitude'    => $baseLat + $randomOffset(),
                    'longitude'   => $baseLng + $randomOffset(),
                    'created_at'  => $now->copy()->subMinutes(rand(1, 5000)),
                    'updated_at'  => $now,
                ];
            }

            DB::table('incidents')->insert($records);

            echo "✔ Inserted 50 incidents with coordinates for Project ID: $projectId\n";
        }
    }
}
