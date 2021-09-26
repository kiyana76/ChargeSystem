<?php
namespace App\Classes;

use App\Classes\CreateCharge\CreateChargeFactoryInterface;
use App\Classes\Traits\Credits;
use App\Events\ChargeSoldEvent;
use App\Repository\ChargeCategoryRepositoryInterface;
use App\Repository\ChargeRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DemandCharge
{
    use Credits;

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
            'user_id' => $data['user_id'] ?? null, //if order service demand charge we save user_id null and company_id must be root company
            'company_id' => $data['company_id'],
        ];
        $charges = [];
        for ($i = 0; $i < $count; $i++) {
            $charge = $this->getValidFreeCharge($data['charge_category_id']);
            $charge = $this->chargeRepository->update($charge->id, $update_data);

            event(new ChargeSoldEvent());
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
                return 'something went wrong in lock credit';

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
            return 'something wrong in decrease charge';
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

    public function createCharge(&$data) {
        $get_code_class = App::make(CreateChargeFactoryInterface::class);

        //write do..while for get not unique code and produce again
        do {
            $data['code'] = $get_code_class->getChargeCode();
            $charge = $this->chargeRepository->create($data);
        } while ($charge == null);


        return $charge;
    }
}
