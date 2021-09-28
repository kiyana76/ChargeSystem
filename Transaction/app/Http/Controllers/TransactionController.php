<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class TransactionController extends Controller
{
    public function payment(Request $request) {
        $array = ['fail', 'cancel', 'success'];

        $random_status = $array[array_rand($array)];

        if ($random_status == 'success')
            return response()->json(['message' => 'payment successful', 'body' => ['status' => 'success'], 'error' => false], 200);
        return response()->json(['message' => 'payment ' . $random_status, 'body' => ['status' => $random_status], 'error' => true], 200);
    }

    public function payment_test(Request $request) {
        $invoice = new Invoice();
        $invoice->amount(1000);
        $invoice->detail(['order_id' => 1, 'mobile' => '09302828629']);

        Payment::via('zarinpal')->config(['mode' => 'sandbox'])->purchase($invoice, function () {
            //save transaction
        })->pay()->toJson();

    }
}
