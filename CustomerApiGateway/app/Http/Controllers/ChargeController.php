<?php

namespace App\Http\Controllers;

use App\Classes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChargeController extends Controller
{
    public function burnt(Request $request) {
        $data = $request->all();
        $response = Http::post(config('api_gateway.charge_service_url') . 'charge/burnt', $data);
        return response()->json(json_decode($response->getBody()->getContents()));

    }

    public function index(Request $request) {
        $data = $request->all();

        $auth_class = new Auth();
        $token = $request->header('authorization') ?? '';
        $user = $auth_class->getUser($token);

        if ($user) {
            $data['mobile'] = $user['mobile']; // we don't store user_id for customer in charge service
            $query_params = http_build_query($data);
            $response = Http::get(config('api_gateway.order_service_url') . 'orders/charge-details?' . $query_params);
            $json_response = json_decode($response->getBody()->getContents());
            return response()->json($json_response);
        }

        return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
    }
}
