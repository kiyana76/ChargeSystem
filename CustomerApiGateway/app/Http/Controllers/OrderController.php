<?php

namespace App\Http\Controllers;

use App\Classes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function create(Request $request) {
        $auth_class = new Auth();
        $token = $request->header('authorization') ?? '';
        $user = $auth_class->getUser($token);

        if ($user) {
            $data = $request->all();
            $data['mobile'] = $user['mobile'];
            $data['customer_id'] = $user['id'];

            $response = Http::post(config('api_gateway.order_service_url') . '/orders', $data);
            $json_response = json_decode($response->getBody()->getContents());

            return response()->json($json_response);
        }
        return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
    }

    public function index(Request $request) {
        $auth_class = new Auth();
        $token = $request->header('authorization') ?? '';
        $user = $auth_class->getUser($token);
        if ($user) {
            $data = $request->all();
            $data['customer_id'] = $user['id'];
            $data['mobile'] = $user['mobile'];

            $response = Http::get(config('api_gateway.order_service_url') . '/orders', $data);
            $json_response = json_decode($response->getBody()->getContents());

            return response()->json($json_response);
        }

        return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
    }
}
