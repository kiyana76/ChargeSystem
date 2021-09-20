<?php

namespace App\Http\Controllers\UserService;

use App\Classes\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CompanyController extends Controller
{
    public function create(Request $request) {
        $auth_class = new Auth();
        $token = $request->header('authorization') ?? '';
        $user = $auth_class->getUser($token);

        if ($user && $user['type'] == 'admin') {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->post(config('api_gateway.user_service_url') . 'companies', $request->all());
            return response()->json(json_decode($response->getBody()->getContents()));
        }
        return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
    }

    public function index(Request $request) {
        $auth_class = new Auth();
        $token = $request->header('authorization') ?? '';
        $user = $auth_class->getUser($token);

        if ($user && $user['type'] == 'admin') {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get(config('api_gateway.user_service_url') . 'companies', $request->all());
            return response()->json(json_decode($response->getBody()->getContents()));
        }
        return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
    }
}
