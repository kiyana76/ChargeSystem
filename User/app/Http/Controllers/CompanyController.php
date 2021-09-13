<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function create(Request $request) {
        $this->authorize('isAdmin', Auth::guard('user')->user());

        $rules = [
            'name' => 'required'
        ];

        $this->validate($request, $rules);

        $item = ['name'];

        $data = $request->only($item);

        $company = Company::create($data);

        if ($company)
            return response()->json(['message' => 'company created successfully!', 'body' => [$company], 'error' => false], 201);
        return response()->json(['message' => 'company not created!', 'body' => [], 'error' => true], 400);

    }

    public function index() {
        $this->authorize('isAdmin', Auth::guard('user')->user());

        $companies = Company::all();

        return response()->json(['message' => 'all data retrieved!', 'body' => [$companies], 'error' => false], 200);
    }
}
