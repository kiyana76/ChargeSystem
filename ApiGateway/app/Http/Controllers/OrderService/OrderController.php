<?php

namespace App\Http\Controllers\OrderService;

use App\Classes\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{


    public function index(Request $request) {
        $auth_class = new Auth();
        $token = $request->header('authorization') ?? '';
        $user = $auth_class->getUser($token);

        if ($user && $user['type'] == 'admin') {
            $data = $request->all();
            $response = Http::get(config('api_gateway.order_service_url') . 'orders', $data);
            return response()->json(json_decode($response->getBody()->getContents()));
        }
        return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
    }
}
