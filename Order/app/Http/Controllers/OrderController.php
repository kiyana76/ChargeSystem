<?php

namespace App\Http\Controllers;

use App\Classes\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create(Request $request) {
        $rules = [
            'mobile' => 'required|valid_mobile',
            'charge_category_id' => 'required|array',
            'customer_id' => 'required',
        ];
        $this->validate($request, $rules);

        $items = [
            'mobile',
            'charge_category_id',
            'customer_id'
        ];

        $data = $request->only($items);
        $order_class = new Order();
        $result = $order_class->create($data);

        return response()->json(['message' => $result['message'], 'body' => $result['body'], 'error' => $result['error']], $result['status_code']);
    }
}
