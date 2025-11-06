<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(PermissionsSeeder::class);
        $this->call(RoleSeeder::class);

        if (config('app.env') !== 'production') {
            // ✅ Ensure Demo Project exists (idempotent)
            DB::table('projects')->updateOrInsert(
                ['name' => 'Demo Project'], // unique constraint
                [
                    'code'        => 'DEMO',
                    'description' => 'Demo project for local testing',
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
