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

    public function indexWithChargeDetails(Request $request) {
        $order_class = new Order();
        $order_items = [
            'mobile',
            'status',
            'date_from',
            'date_to'
        ];
        $charge_items = [
            'expire_date_from',
            'expire_date_to',
            'sold_status',
            'charge_category_id'
        ];
        $order_filters = $request->only($order_items);
        $charge_filters = $request->only($charge_items);
        //return response()->json(['message' => 'all orders retrieve!', 'body' => [$order_filters, $charge_filters], 'error' => false], 200);
        foreach ($order_filters as $key => $value) {
            if ($value == '')
                unset($order_filters[$key]);
        }

        foreach ($charge_filters as $key => $value) {
            if ($value == '')
                unset($charge_filters[$key]);
        }

        $result = $order_class->indexWithChargeDetails($order_filters, $charge_filters);

        return response()->json(['message' => 'all orders retrieve!', 'body' => $result, 'error' => false], 200);
    }

    public function update(Request $request) {
        $data = $request->all();
        $order_class = new Order();
        $order_id = $data['body']['order_id'];
        $status  = $data['body']['status'];
        $message = $data['body']['message'];
        $order_result = $order_class->update($order_id, $status , $message);

        return response()->json(['message' => $order_result['message'], 'body' => $order_result['body'], 'error' => $order_result['error']], $order_result['status_code']);
    }
}
