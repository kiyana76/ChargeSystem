<?php

namespace App\Http\Controllers;

use App\Models\ChargeCategory;

class ChargeCategoryController extends Controller
{
    public function index() {
        $charge_categories = ChargeCategory::all();

        return response()->json(['message' => 'charge category retrieved!', 'body' => [$charge_categories], 'error' => false], 200);
    }
}
