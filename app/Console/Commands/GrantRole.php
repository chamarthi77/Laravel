<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{User, Role};

class GrantRole extends Command
{
    protected $signature = 'rbac:grant {email} {role=SUPER_ADMIN}';
    protected $description = 'Grant a role to a user (global scope)';

    public function handle(): int
    {
        $email = $this->argument('email');
        $roleKey = strtoupper($this->argument('role'));

        $user = User::where('email',$email)->firstOrFail();
        $role = Role::where('key',$roleKey)->firstOrFail();

        $user->roles()->syncWithoutDetaching([
            $role->id => ['community_id'=>null, 'organization_id'=>null]
        ]);

        $this->info("Granted {$roleKey} to {$email}");
        return self::SUCCESS;
    }
}
