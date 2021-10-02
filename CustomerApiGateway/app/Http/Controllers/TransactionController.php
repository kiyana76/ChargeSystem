<?php

namespace App\Http\Controllers;

use App\Classes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    public function index(Request $request) {
        $data = $request->all();

        $auth_class = new Auth();
        $token = $request->header('authorization') ?? '';
        $user = $auth_class->getUser($token);
        if ($user) {
            $data['mobile'] = $user['mobile'];
            $response = Http::get(config('api_gateway.transaction_service_url') . 'transactions', $data);
            return response()->json(json_decode($response->getBody()->getContents()));
        }

        return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
    }
}
