<?php
namespace App\Classes;

use App\Classes\CreateCharge\CreateChargeFactoryInterface;
use App\Repository\ChargeCategoryRepositoryInterface;
use App\Repository\ChargeRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DemandCharge
{
    private $chargeRepository;
    private $chargeCategoryRepository;
    public function __construct()
    {
        $this->chargeRepository = App::make(ChargeRepositoryInterface::class);
        $this->chargeCategoryRepository = App::make(ChargeCategoryRepositoryInterface::class);
    }

    public function adminDemandCharge($data) : ?array {
        DB::beginTransaction();
        $count = $data['count'];
        $update_data = [
            'expire_date' => Carbon::now()->addDays(config('charge.expire_date_after')),
            'sold_status' => 'sold',
            'user_id' => $data['user_id'], //if order service demand charge we save user_id null and company_id must be root company
            'company_id' => $data['company_id'],
        ];
        $charges = [];
        for ($i = 0; $i < $count; $i++) {
            $charge = $this->getValidFreeCharge($data['charge_category_id']);
            $charge = $this->chargeRepository->update($charge->id, $update_data);
            if ($charge)
                array_push($charges, $charge);
            else
                $i--;
        }
        DB::commit();

        return $charges;
    }

    public function sellerDemandCharge($data) {
        DB::beginTransaction();
            $price = $this->getPriceDemandCharges($data['count'], $data['charge_category_id']);
            $credit_status = $this->checkCredit($data['user_id'], $price);
            if (! $credit_status)
                return ' credit not enough';

            $lock_credit = $this->changeSellerCredit($data['user_id'], $price, 'lock');

            if ($lock_credit->error)
                return 'something went wrong';

            $create_date = [
                'charge_category_id' => $data['charge_category_id'],
                'user_id' => $data['user_id'],
                'company_id' => $data['company_id'],
                'amount' => $this->chargeCategoryRepository->findById($data['charge_category_id'])->amount,
                'sold_status' => 'sold',
                'status' => 'valid',
                'expire_date' => Carbon::now()->addDays(config('charge.expire_date_after'))
            ];

        $count = $data['count'];
        $charges = [];
        $i = 0;

        try {
            for ($i = 0; $i < $count; $i++)
                array_push($charges, $this->createCharge($create_date));
            $this->updateLockCredit($lock_credit->body[0]->id, 'decrease');
            DB::commit();
            return $charges;


        } catch (\Throwable $e) {
            DB::rollBack();
            $this->changeSellerCredit($data['user_id'], $price, 'increase');
            return 'something wrong';
        }
    }

    private function getValidFreeCharge($charge_category_id) {
        $charge = $this->chargeRepository->show(['*'],
            ['charge_category_id' => $charge_category_id,
                'status' => 'valid',
                'sold_status' => 'free']);

        if (!$charge) {
            $data = [
                'charge_category_id' => $charge_category_id,
                'amount' => $this->chargeCategoryRepository->findById($charge_category_id)->amount,
                'sold_status' => 'free',
                'status' => 'valid',
            ];
            $charge = $this->createCharge($data);
        }

        return $charge;
    }

    private function createCharge(&$data) {
        $get_code_class = App::make(CreateChargeFactoryInterface::class);

        //write do..while for get not unique code and produce again
        do {
            $data['code'] = $get_code_class->getChargeCode();
            $charge = $this->chargeRepository->create($data);
        } while ($charge == null);


        return $charge;
    }

    private function checkCredit($user_id, $price) : ?bool {
        $response = Http::get(config('charge.user_service_url') . 'get-credit?user_id=' . $user_id);
        $credit = json_decode($response->getBody()->getContents())->body->credit;
        if (!isset($credit))
            return null;

        return $credit >= $price;

    }

    private function getPriceDemandCharges($count, $category_id) : int {
        $amount = $this->chargeCategoryRepository->findById($category_id)->amount;

        return $amount * $count;
    }

    private function changeSellerCredit($user_id, $amount, $type) {
        $params = [
            'user_id' => $user_id,
            'amount' => $amount,
            'type' => $type
        ];

        $response = Http::post(config('charge.user_service_url') . 'credit', $params);

        return json_decode($response->getBody()->getContents());
    }

    private function updateLockCredit($credit_id, $type) {
        $params = [
            'credit_log_id' => $credit_id,
            'type' => $type
        ];
        $response = Http::put(config('charge.user_service_url') . 'credit', $params);

        return json_decode($response->getBody()->getContents());
    }
}
