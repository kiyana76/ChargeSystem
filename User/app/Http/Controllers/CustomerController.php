<?php

namespace App\Http\Controllers;


use App\Classes\Customer;
use App\Repository\CustomerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    private  $customerRepository;
    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

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
         $customer_class = new Customer($this->customerRepository);

         if (!$token = $customer_class->login($data))
             return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);

         return $this->respondWithToken($token);
    }

    public function get(Request $request) {
        if (Auth::guard('customer')->check())
            return response()->json(['message' => 'Authenticated', 'body' => Auth::guard('customer')->user(), 'error' => false],200);
        return response()->json(['message' => 'UnAuthorized', 'body' => [], 'error' => true], 401);
    }
}
