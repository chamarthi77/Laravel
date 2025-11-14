<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // ✅ Existing core seeders
        $this->call([
            PermissionsSeeder::class,
            RoleSeeder::class,
            ProjectSeeder::class, // 👈 our new one
            IncidentSeeder::class,
        ]);

        if (config('app.env') !== 'production') {
            // ✅ Ensure Demo Project exists (idempotent)
            DB::table('projects')->updateOrInsert(
                ['projectid' => '999'],
                [
                    'name'        => 'Demo Project',
                    'permission'  => 'read',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );

            // ✅ Ensure Super Admin exists (idempotent)
            DB::table('users')->updateOrInsert(
                ['email' => 'super@example.com'],
                [
                    'firebase_uid' => 'local-dev-super',
                    'name'         => 'Local Super Admin',
                    'role'         => 'SUPER_ADMIN',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]
            );
        }
    }
}
