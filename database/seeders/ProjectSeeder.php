<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('projects')->insert([
            ['id' => 1, 'projectid' => '101', 'name' => 'Fire Response Demo', 'permission' => 'read', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'projectid' => '102', 'name' => 'Flood Relief Simulation', 'permission' => 'write', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'projectid' => '103', 'name' => 'Earthquake Drill', 'permission' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'projectid' => '104', 'name' => 'Hurricane Recovery', 'permission' => 'read', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
