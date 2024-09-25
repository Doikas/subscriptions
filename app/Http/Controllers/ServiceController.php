<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    public function getExpiration($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        return response()->json(['expiration' => $service->expiration])
        ->header('Content-Type', 'application/json');
    }
}
