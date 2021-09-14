<?php
namespace App\Classes;

use Illuminate\Support\Facades\Auth;
use \App\Models\Customer AS CustomerModel;
use Illuminate\Support\Facades\Hash;

class Customer
{

    public function login($credentials_data)
    {
        if (!$this->checkMobileExists($credentials_data['mobile'])) {
            $this->create($credentials_data);
        }

        if (!(($token = Auth::guard('customer')->attempt($credentials_data)) && Auth::guard('customer')->user()->status == 'active')) {
            return false;
        }

        return $token;
    }

    private function checkMobileExists($mobile):bool {
        $mobile_exists = CustomerModel::whereMobile($mobile)->first();
        return $mobile_exists != null;
    }

    private function create($credentials_data)
    {
        $credentials_data['status'] = 'active';
        $credentials_data['password'] = Hash::make($credentials_data['password']);
        $customer = CustomerModel::create($credentials_data);
    }
}
