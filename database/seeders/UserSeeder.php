<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insertOrIgnore([
            [
                'firebase_uid' => 'b87U1qXAXlgpEWKtubJYTAJ2xtF2', // optional placeholder
                'email' => 'hansikagarapati.go@gmail.com',
                'name' => 'Hansika Garapati',
                'role' => 'SUPER_ADMIN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'firebase_uid' => '0mif9OqKb7hOHNjnXB8XjulWgyE3',
                'email' => 'hgara2@uic.edu',
                'name' => 'Hansika Gara',
                'role' => 'COMMUNITY_ADMIN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'firebase_uid' => 'azch3xkiGmgDCNLYBlbj8Ds9aNl2',
                'email' => 'rudrarevanth@gmail.com',
                'name' => 'Rudra Revanth',
                'role' => 'ORGANIZATION_MANAGER',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'firebase_uid' => 'ukbZQBZzXkUfAYeh55WbMF83ikV2',
                'email' => 'hansika.garapati@gmail.com',
                'name' => 'Hansika Garapati (User)',
                'role' => 'USER',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
