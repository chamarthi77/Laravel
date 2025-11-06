<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(['key'=>'SUPER_ADMIN'],     ['name'=>'Super Admin','rank'=>100]);
        Role::updateOrCreate(['key'=>'COMMUNITY_ADMIN'], ['name'=>'Community Admin','rank'=>70]);
        Role::updateOrCreate(['key'=>'ORG_MANAGER'],     ['name'=>'Organization Manager','rank'=>50]);
        Role::updateOrCreate(['key'=>'USER'],            ['name'=>'User','rank'=>10]);
    }
}
