<?php

namespace App\Http\Controllers;

use App\Classes\Burnt;
use App\Classes\DemandCharge;
use App\Models\ChargeCategory;
use App\Repository\ChargeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class ChargeController extends Controller
{
    private ChargeRepositoryInterface $chargeRepository;

    public function __construct (ChargeRepositoryInterface $chargeRepository)
    {
        $this->chargeRepository = $chargeRepository;
    }

    public function demand (Request $request)
    {
        $rules = [
            'charge_category_id' => 'required|exists:charge_categories,id',
            'user_type'          => ['required', Rule::in(['admin', 'seller', 'customer'])],
            'user_id'            => [Rule::requiredIf(function () use ($request) {
                return $request->user_type != 'customer';
            })],
            'company_id'         => 'required',
            'count'              => 'required',
        ];

        $items = [
            'charge_category_id',
            'company_id',
            'user_id',
            'count',
        ];

        $this->validate($request, $rules);
        $data                = $request->only($items);
        $demand_charge_class = new DemandCharge();
        switch ($request->user_type) {
            case 'customer':
            case 'admin':
                $result = $demand_charge_class->adminDemandCharge($data);
                break;
            case 'seller':
                $result = $demand_charge_class->sellerDemandCharge($data);
                break;
        }

        if (gettype($result) == 'array')
            return response()->json(['message' => 'charge created', 'body' => $result, 'error' => false], 200);
        return response()->json(['message' => $result, 'body' => [], 'error' => true], 400);
    }

    public function index (Request $request)
    {
        $filters = $request->all();
        foreach ($filters as $key => $value) {
            if ($value == '')
                unset($filters[$key]);
        }
        $result = $this->chargeRepository->index(['*'], $filters);
        return response()->json(['message' => 'all charges retrieved', 'body' => $result, 'error' => false], 200);
    }

    public function burnt (Request $request)
    {
        $rules = [
            'mobile' => 'required|valid_mobile',
            'code'   => 'required',
        ];
        $this->validate($request, $rules);

        $data = $request->all();

        $burnt_class = new Burnt();

        $result = $burnt_class->burnt($data);

        return response()->json(['message' => $result['message'], 'body' => [], 'error' => $result['error']], $result['status_code']);
    }
}
