<?php

namespace App\Http\Controllers;

use App\Repository\TransactionRepositoryInterface;
use App\Services\Gateway\GatewayFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
        return response()->json($gateway->select()->pay($data));
    }

    public function zarinpalCallback(Request $request) {
        $gateway = new GatewayFactory('zarinpal');
        $result = $gateway->select()->verify($request->Authority);

        if ($result['status'] == 'success')
            $data = ['message' => 'payment successful', 'body' => ['status' => 'success', 'message' => $result['message'], 'order_id' => $result['order_id']], 'error' => false];
        else
            $data = ['message' => 'payment ' . $result['status'], 'body' => ['status' => $result['status'], 'message' => $result['message'], 'order_id' => $result['order_id']], 'error' => true];

        $guzzle_request = Http::put(config('api_gateway.order_service_url') . 'orders', $data);
        $json_response = json_decode($guzzle_request->getBody()->getContents());
        return response()->json($json_response);
    }
}
