<?php

namespace App\Services\Gateway\Adapter;

use App\Repository\TransactionRepositoryInterface;
use Illuminate\Support\Facades\App;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Multipay\Payment;

class Zarinpal implements GatewayInterface
{
    private $transactionRepository;

    public function __construct()
    {
        $this->transactionRepository = App::make(TransactionRepositoryInterface::class);
    }

    public function pay($payload = [])
    {
        $paymentConfig = app('config')['payment'];

        $payment = new Payment($paymentConfig);

        $invoice = new Invoice;
        $invoice->amount((int)$payload['amount']);// amount must be integer

        return json_decode($payment->purchase($invoice, function ($driver, $transactionId) use ($payload) {
            return $this->transactionRepository->create([
                'AuthId' => $transactionId,
                'order_id' => $payload['order_id'] ,
                'mobile' => $payload['mobile'],
                'amount' => (int)$payload['amount'],
                'driver' => 'zarinpal',
                'status' => 'created'
            ]);
        })->pay()->toJson())->action;
    }

    public function verify($AuthId)
    {
        $transaction = $this->transactionRepository->show(['*'], ['AuthId' => $AuthId]);

        // load the config file from your project
        $paymentConfig = app('config')['payment'];

        $payment = new Payment($paymentConfig);

        try {
            $receipt = $payment->amount($transaction->amount)->transactionId($AuthId)->verify();

            // You can show payment referenceId to the user.

            $this->transactionRepository->update($transaction->id, ['status' => 'success', 'ref_number' => $receipt->getReferenceId()]);

            return ['status' => 'success', 'message' => 'با موفقیت پرداخت شد. شماره مرجع:' . $receipt->getReferenceId(), 'order_id' => $transaction->order_id];


        } catch (InvalidPaymentException $exception) {
            /**
            when payment is not verified, it will throw an exception.
            We can catch the exception to handle invalid payments.
            getMessage method, returns a suitable message that can be used in user interface.
             **/

            if ($exception->getCode() == -22) {
                $this->transactionRepository->update($transaction->id, ['status' => 'cancel']);
                return ['status' => 'cancel', 'message' => $exception->getMessage(), 'order_id' => $transaction->order_id];
            }
            else {
                $this->transactionRepository->update($transaction->id, ['status' => 'fail']);
                return ['status' => 'fail', 'message' => $exception->getMessage(), 'order_id' => $transaction->order_id];
            }
        }
    }
}
