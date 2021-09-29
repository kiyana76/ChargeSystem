<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Multipay\Payment;

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
        $paymentConfig = require(__DIR__ . '/../../../config/payment.php');

        $payment = new Payment($paymentConfig);

        $invoice = new Invoice;
        $invoice->amount(1000);
        $invoice->detail(['mobile' => '09302828629']);
        $driver = $invoice->getDriver();

        return json_decode($payment->purchase($invoice, function ($driver, $transactionId) {
            return Transaction::create(['AuthId' => $transactionId, 'driver' => 'zarinpal', 'order_id' => 5, 'status' => 'created', 'mobile' => '09302828629', 'amount' => '1000000']);
        })->pay()->toJson())->action;
    }

    public function callback(Request $request) {
        $status = $request->Status;
        $AuthID= $request->Authority;
        $transaction = Transaction::where('AuthId', $AuthID)->first();

        // load the config file from your project
        $paymentConfig = require(__DIR__ . '/../../../config/payment.php');

        $payment = new Payment($paymentConfig);

        try {
            $receipt = $payment->amount(1000)->transactionId($AuthID)->verify();

            // You can show payment referenceId to the user.

            $transaction->update(['status' => 'success', 'ref_number' => $receipt->getReferenceId()]);
            echo 'this is ref id: ' . $receipt->getReferenceId();


        } catch (InvalidPaymentException $exception) {
            /**
            when payment is not verified, it will throw an exception.
            We can catch the exception to handle invalid payments.
            getMessage method, returns a suitable message that can be used in user interface.
             **/

            if ($exception->getCode() == -22)
                $transaction->update(['status' => 'cancel']);
            else
                $transaction->update(['status' => 'failed']);
            echo $exception->getMessage();
        }
    }
}
