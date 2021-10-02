<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChargeController extends Controller
{
    public function burnt(Request $request) {
        $data = $request->all();
        $response = Http::post(config('api_gateway.charge_service_url') . 'charge/burnt', $data);
        return response()->json(json_decode($response->getBody()->getContents()));

    }
}
