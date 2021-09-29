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
        $order_id = $result['body']['order']->id;
        $amount = $order_class->getAmountOrder($order_id);

        $payment_link = $order_class->payment($data['mobile'], $amount, $order_id);

        return response()->json($payment_link);
    }

    public function index(Request $request) {
        $order_class = new Order();
        $filters = $request->all();

        foreach ($filters as $key => $value) {
            if ($value == '')
                unset($filters[$key]);
        }

        $result = $order_class->index($filters);

        return response()->json(['message' => 'all orders retrieve!', 'body' => $result, 'error' => false], 200);
    }

    public function update(Request $request) {
        $data = $request->all();
        $order_class = new Order();
        $order_result = $order_class->update($data['body']['order_id'], $data['body']);

        return response()->json(['message' => $order_result['message'], 'body' => $order_result['body'], 'error' => $order_result['error']], $order_result['status_code']);
    }
}
