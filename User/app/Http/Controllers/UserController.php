<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function show($id) {
        $user = User::whereId($id)->first();

        if (!$user)
            return response()->json(['message' => 'user not found!', 'body' => [], 'error' => true], 404);
        return response()->json(['message' => 'user founded!', 'body' => [$user], 'error' => false], 200);
    }
}
