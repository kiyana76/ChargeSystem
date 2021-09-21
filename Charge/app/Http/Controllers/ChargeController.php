<?php

namespace App\Http\Controllers;

use App\Classes\DemandCharge;
use App\Models\ChargeCategory;
use App\Repository\ChargeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class ChargeController extends Controller
{
    public function demand(Request $request) {
        $rules = [
            'charge_category_id' => 'required|exists:charge_categories,id',
            'user_type' => ['required', Rule::in(['admin', 'seller', 'customer'])],
            'user_id' => [Rule::requiredIf(function () use ($request) {
                return $request->user_type != 'customer';
            })],
            'company_id' => 'required',
            'count' => 'required',
        ];

        $items = [
            'charge_category_id',
            'company_id',
            'user_id',
            'count',
        ];

        $this->validate($request, $rules);
        $data = $request->only($items);
        $demand_charge_class = new DemandCharge();
        switch ($request->user_type) {
            case 'admin':
                $result = $demand_charge_class->adminDemandCharge($data);
                break;
            case 'seller':
                $result = $demand_charge_class->sellerDemandCharge($data);
                break;
            case 'customer':
                $result = $demand_charge_class->customerDemandCharge($data);
                break;
        }

        if ($result)
            return response()->json(['message' => 'charge created', 'body' => $result, 'error' => false], 200);
        return response()->json(['message' => 'charge create failed', 'body' => [], 'error' => true], 400);
    }
}
