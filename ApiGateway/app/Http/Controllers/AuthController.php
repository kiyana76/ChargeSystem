<?php

namespace App\Http\Controllers;



use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function login(Request $request) {
        $response = Http::post(config('api_gateway.user_service_url') . '/admin/login', $request->all());
        $json_response = json_decode($response->getBody()->getContents());

        return response()->json($json_response);
    }


}
