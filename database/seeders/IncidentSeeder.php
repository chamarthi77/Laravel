<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('incidents')->insert([
            // 🔹 Project 1
            ['title' => 'Fire near North Zone', 'description' => 'Warehouse fire, moderate intensity', 'project_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Gas Leak Reported', 'description' => 'Leak detected in industrial area', 'project_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Power Outage', 'description' => 'Several units affected by blackout', 'project_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Minor Explosion', 'description' => 'Small explosion at north substation', 'project_id' => 1, 'created_at' => $now, 'updated_at' => $now],

            // 🔹 Project 2
            ['title' => 'Flood Warning Issued', 'description' => 'Riverside flooding alert issued for lowlands', 'project_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Bridge Under Watch', 'description' => 'Structural monitoring ongoing due to rising water', 'project_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Evacuation Advisory', 'description' => 'Residents advised to move to higher ground', 'project_id' => 2, 'created_at' => $now, 'updated_at' => $now],

            // 🔹 Project 3
            ['title' => 'Earthquake Aftershock', 'description' => 'Minor aftershock detected 10km east', 'project_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Tremor Felt Downtown', 'description' => 'Light vibration felt, no structural damage', 'project_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Inspection Teams Deployed', 'description' => 'Crews dispatched for safety checks', 'project_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Ground Stabilization', 'description' => 'Work ongoing to reinforce affected zones', 'project_id' => 3, 'created_at' => $now, 'updated_at' => $now],

            // 🔹 Project 4
            ['title' => 'Hurricane Warning Issued', 'description' => 'Hurricane alert issued for coastal areas', 'project_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Shelters Activated', 'description' => 'Emergency shelters opened for evacuees', 'project_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            
        ]);
    }
}
