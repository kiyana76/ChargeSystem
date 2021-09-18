<?php

namespace App\Http\Controllers;

use App\Classes\Credit;
use App\Models\Company;
use App\Repository\CreditRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CreditController extends Controller
{
    private UserRepositoryInterface $userRepository;
    private $creditRepository;
    public function __construct(UserRepositoryInterface $userRepository, CreditRepositoryInterface $creditRepository)
    {
        $this->userRepository = $userRepository;
        $this->creditRepository = $creditRepository;
    }

    public function create(Request $request) {
        $rules = [
            'user_id' => ['required', Rule::exists('users', 'id')->where(function ($q) {
                return $q->where('type', 'seller');
            })],
            'amount' => 'required',
            'type' => ['required', Rule::in(['increase', 'decrease', 'lock'])],
            'admin_id' => [
                Rule::requiredIf(function () use ($request) {
               return $request->type == 'increase';
            }),
                Rule::exists('users', 'id')->where(function ($q) {
                    return $q->where('type', 'admin');
                })
            ]
        ];

        $items = [
            'user_id',
            'admin_id',
            'amount',
            'type'
        ];

        $this->validate($request, $rules);

        $credit_class = new Credit($this->userRepository, $this->creditRepository);

        $result = $credit_class->create($request->only($items));

        return response()->json(['message' => 'credit log create successfully!', 'body' => [$result], 'error' => false], 201);

    }

    public function log(Request $request) {
        //depend on admin or seller
        $items = [
            'company_id',
            'seller_id'
        ];
        $data = $request->only($items);

        $credit_class = new Credit($this->userRepository, $this->creditRepository);
        $result = $credit_class->log($data);

        return response()->json(['message' => 'credit retrieved!', 'body' => $result, 'error' => false], 200);
    }

    public function getCredit(Request $request) {
        $rules = ['user_id' => 'required|exists:users,id'];

        $this->validate($request, $rules);

        $credit_class = new Credit($this->userRepository, $this->creditRepository);
        $credit = $credit_class->getCredit(['user_id' => $request->user_id]);

        return response()->json(['message' => 'credit retrieved!', 'body' => ['credit' => $credit], 'error' => false], 200);
    }

    public function update(Request $request) {
        // we can only update lock type to decrease type
        $rules = [
            'credit_log_id' => ['required', Rule::exists('credit_logs', 'id')->where(function ($q) {
                return $q->where('type', 'lock');
            })],
            'type' => ['required', Rule::in('decrease')]
        ];

        $items = [
            'credit_log_id',
            'type'
        ];

        $this->validate($request, $rules);

        $data = $request->only($items);

        $credit_class = new Credit($this->userRepository, $this->creditRepository);
        $result = $credit_class->update($data);

        return response()->json(['message' => 'credit logs update successfully!', 'body' => [], 'error' => false], 200);
    }
}
