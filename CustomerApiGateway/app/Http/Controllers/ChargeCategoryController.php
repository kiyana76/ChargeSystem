<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChargeCategoryController extends Controller
{
    public function index() {
        $response = Http::get(config('api_gateway.charge_service_url') . '/charge-categories');
        $json_response = json_decode($response->getBody()->getContents());

        return response()->json($json_response);
    }
}
