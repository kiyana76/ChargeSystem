<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'mobile' => ['required', 'valid_mobile'],
            'password' => ['required']
        ]);

        $data = $request->only(['mobile', 'password']);

        if (!(($token = Auth::attempt($data)) && Auth::user()->status == 'active')) {
            return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
        }

        return $this->respondWithToken($token);
    }

    public function registerSeller(Request $request)
    {
        $this->authorize('isAdmin', Auth::user());

        $rules = [
            'name' => 'required',
            'mobile' => 'required|valid_mobile',
            'company_id' => 'required|exists:companies,id',
            'email' => 'email',
            'password' => 'required|min:6',
            'status' => 'required'
        ];

        $items = [
            'name',
            'company_id',
            'mobile',
            'email',
            'status',
        ];

        $this->validate($request, $rules);

        $data = $request->only($items);
        //admin can add seller
        $data['type'] = 'seller';
        $data['password'] = Hash::make($request->password);

        $user = User::create($data);

        if ($user)
            return response()->json(['message' => 'user created successfully!', 'body' => [$user], 'error' => false], 201);
        return response()->json(['message' => 'user not create!', 'body' => [], 'error' => true], 401);
    }
}
