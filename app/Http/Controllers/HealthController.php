<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HealthController extends Controller
{
    public function alive()
    {
        return response()->json(['status' => 'ok'], 200);
    }
}

