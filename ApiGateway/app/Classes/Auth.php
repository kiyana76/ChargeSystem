<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class Auth
{
    public function getUser($token) {
        $response      = Http::withHeaders(['Authorization' => $token, 'Accept' => 'application/json'])
            ->get(config('api_gateway.user_service_url') . 'get-user');
        $json_response = json_decode($response->getBody()->getContents());

        if ($json_response->error)
            return null;
        else
            return (array)$json_response->body;
    }
}
