<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permissions')->upsert([
            ['key' => 'SUPER_ADMIN', 'name' => 'Super Admin'],
            ['key' => 'COMMUNITY_ADMIN', 'name' => 'Community Admin'],
            ['key' => 'ORG_MANAGER', 'name' => 'Organization Manager'],
            ['key' => 'USER', 'name' => 'User'],
        ], ['key'], ['name']);
    }
}
