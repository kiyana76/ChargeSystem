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

        $payment_status = $order_class->payment($data['mobile'], $amount, $order_id);

        if (isset($payment_status['error']) && $payment_status['error'])
            return response()->json(['message' => $payment_status['message'], 'body' => $payment_status['body'], 'error' => $payment_status['error']], $payment_status['status_code']);

        $order_result = $order_class->update($order_id, $payment_status);

        return response()->json(['message' => $order_result['message'], 'body' => $order_result['body'], 'error' => $order_result['error']], $order_result['status_code']);
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
}
