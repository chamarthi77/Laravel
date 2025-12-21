<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $table = 'incidents';

    protected $fillable = [
        'title',
        'description',
        'status',
        'project_id',
        'city',
        'latitude',
        'longitude',
        'location',
       
    ];
}
