<?php

namespace App\Http\Controllers;

use App\Repository\TransactionRepositoryInterface;
use App\Services\Gateway\GatewayFactory;
use Illuminate\Http\Request;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Multipay\Payment;

class TransactionController extends Controller
{

    private $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function payment(Request $request) {
        $array = ['fail', 'cancel', 'success'];

        $random_status = $array[array_rand($array)];

        if ($random_status == 'success')
            return response()->json(['message' => 'payment successful', 'body' => ['status' => 'success'], 'error' => false], 200);
        return response()->json(['message' => 'payment ' . $random_status, 'body' => ['status' => $random_status], 'error' => true], 200);
    }

    public function payment_test(Request $request) {
        $rules = [
            'amount' => 'required',
            'order_id' => 'required',
            'mobile' => 'required',
            ];
        $this->validate($request, $rules);

        $items = [
            'mobile',
            'order_id',
            'amount'
        ];
        $data = $request->only($items);
        $gateway = new GatewayFactory();
        return $gateway->select()->pay($data);
    }

    public function zarinpalCallback(Request $request) {
        $gateway = new GatewayFactory('zarinpal');
        $result = $gateway->select()->verify($request->Authority);

        if ($result['status'] == 'success')
            return response()->json(['message' => 'payment successful', 'body' => ['status' => 'success', 'message' => $result['message']], 'error' => false], 200);
        return response()->json(['message' => 'payment ' . $result['status'], 'body' => ['status' => $result['status'], 'message' => $result['message']], 'error' => true], 200);
    }
}
