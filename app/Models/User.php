<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
     'firebase_uid',
    'name',
    'email',
    'role',
    'email_verified',
    'project_id',
    'permission_id',
    'is_super',
    'latitude',
    'longitude',
    'address',
    ];

    // 🔑 Define many-to-many relationship with permissions
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
                    ->withTimestamps();
    }

    // (Optional) shortcut if you also want a "roles" alias
    public function roles()
    {
        return $this->permissions();
    }
}
