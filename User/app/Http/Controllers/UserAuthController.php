<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserAuthController extends Controller
{
    private $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'mobile' => ['required', 'valid_mobile'],
            'password' => ['required']
        ]);

        $data = $request->only(['mobile', 'password']);

        if (!(($token = Auth::guard('user')->attempt($data)) && Auth::user()->status == 'active')) {
            return response()->json(['message' => 'Unauthorized', 'body' => [], 'error' => true], 401);
        }

        return $this->respondWithToken($token);
    }

    public function registerUser(Request $request)
    {
        $this->authorize('isAdmin', Auth::guard('user')->user());

        $rules = [
            'name' => 'required',
            'mobile' => 'required|valid_mobile|unique:users,mobile',
            'company_id' => 'required|exists:companies,id',
            'email' => 'email',
            'password' => 'required|min:6',
            'status' => 'required',
            'type' => ['required', Rule::in(['admin', 'seller'])]
        ];

        $items = [
            'name',
            'company_id',
            'mobile',
            'email',
            'status',
            'type'
        ];

        $this->validate($request, $rules);

        $data = $request->only($items);
        $data['password'] = Hash::make($request->password);

        $user = $this->userRepository->create($data);

        if ($user)
            return response()->json(['message' => 'user created successfully!', 'body' => [$user], 'error' => false], 201);
        return response()->json(['message' => 'user not create!', 'body' => [], 'error' => true], 401);
    }
}
