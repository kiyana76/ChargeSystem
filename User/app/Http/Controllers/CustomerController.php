<?php

namespace App\Http\Controllers;


use App\Classes\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public function login(Request $request) {
        $rules = [
            'mobile' => 'required|valid_mobile',
            'password' => 'required'
        ];
        $items = [
            'mobile',
            'password'
        ];
         $this->validate($request, $rules);

         $data = $request->only($items);
         $customer_class = new Customer();

         if (!$token = $customer_class->login($data))
             return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
         return $token;

         return $this->respondWithToken($token);
    }
}
