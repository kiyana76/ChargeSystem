<?php

namespace App\Http\Controllers\ChargeService;

use App\Classes\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChargeController extends Controller
{
    public function chargeCategoryIndex ()
    {
        $response = Http ::get(config('api_gateway.charge_service_url') . 'charge-categories');
        return response() -> json(json_decode($response -> getBody() -> getContents()));
    }

    public function chargeDemand (Request $request)
    {
        $auth_class = new Auth();
        $token      = $request -> header('authorization') ?? '';
        $user       = $auth_class -> getUser($token);

        if ($user && $user['type'] == 'admin') {
            $data               = $request -> all();
            $data['user_type']  = 'admin';
            $data['user_id']    = $user['id'];
            $data['company_id'] = $user['company_id'];
            $response           = Http ::post(config('api_gateway.charge_service_url') . 'charge/demand', $data);
            return response() -> json(json_decode($response -> getBody() -> getContents()));
        } elseif ($user && $user['type'] == 'seller') {
            $data               = $request -> all();
            $data['user_type']  = 'seller';
            $data['user_id']    = $user['id'];
            $data['company_id'] = $user['company_id'];
            $response           = Http ::post(config('api_gateway.charge_service_url') . 'charge/demand', $data);
            return response() -> json(json_decode($response -> getBody() -> getContents()));
        }
        return response() -> json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
    }

    public function index (Request $request)
    {
        $auth_class = new Auth();
        $token      = $request -> header('authorization') ?? '';
        $user       = $auth_class -> getUser($token);

        if ($user && $user['type'] == 'admin') {
            $data     = $request -> all();
            $response = Http ::get(config('api_gateway.charge_service_url') . 'charge', $data);
            return response() -> json(json_decode($response -> getBody() -> getContents()));
        } elseif ($user && $user['type'] == 'seller') {
            $data               = $request -> all();
            $data['company_id'] = $user['company_id'];
            $response           = Http ::get(config('api_gateway.charge_service_url') . 'charge', $data);
            return response() -> json(json_decode($response -> getBody() -> getContents()));
        }
        return response() -> json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
    }

    public function burnt (Request $request)
    {
        $data     = $request -> all();
        $response = Http ::post(config('api_gateway.charge_service_url') . 'charge/burnt', $data);
        return response() -> json(json_decode($response -> getBody() -> getContents()));
    }
}
