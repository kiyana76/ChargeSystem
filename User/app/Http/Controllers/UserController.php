<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    protected $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show($id) {
        $user = $this->userRepository->findById($id);

        if (!$user)
            return response()->json(['message' => 'user not found!', 'body' => [], 'error' => true], 404);
        return response()->json(['message' => 'user founded!', 'body' => [$user], 'error' => false], 200);
    }
}
