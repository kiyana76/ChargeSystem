<?php

namespace App\Http\Controllers\UserService;

use App\Classes\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CreditController extends Controller
{
    public function log(Request $request) {
        $auth_class = new Auth();
        $token = $request->header('authorization') ?? '';
        $user = $auth_class->getUser($token);

        if ($user && $user['type'] == 'admin') {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get(config('api_gateway.user_service_url') . 'credit/log', $request->all());
            return response()->json(json_decode($response->getBody()->getContents()));
        }

        elseif ($user && $user['type'] == 'seller') { // seller just can see log itself
            $data = $request->all();
            $data['company_id'] = $user['company_id'];
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get(config('api_gateway.user_service_url') . 'credit/log', $data);
            return response()->json(json_decode($response->getBody()->getContents()));
        }


        return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
    }

    public function create(Request $request) {
        $auth_class = new Auth();
        $token = $request->header('authorization') ?? '';
        $user = $auth_class->getUser($token);

        if ($user && $user['type'] == 'admin') {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->post(config('api_gateway.user_service_url') . 'credit', $request->all());
            return response()->json(json_decode($response->getBody()->getContents()));
        }
        return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
    }

    public function show(Request $request) {
        $auth_class = new Auth();
        $token = $request->header('authorization') ?? '';
        $user = $auth_class->getUser($token);

        if ($user && $user['type'] == 'admin') {
            $this->validate($request, ['user_id' => 'required']);

            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get(config('api_gateway.user_service_url') . 'get-credit?user_id=' . $request->user_id);
            return response()->json(json_decode($response->getBody()->getContents()));
        }

        elseif ($user && $user['type'] == 'seller') { // seller just can see log itself
            $data = $request->all();
            $data['company_id'] = $user['company_id'];
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get(config('api_gateway.user_service_url') . 'get-credit?user_id=' . $user['id']);
            return response()->json(json_decode($response->getBody()->getContents()));
        }


        return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);

    }
}
