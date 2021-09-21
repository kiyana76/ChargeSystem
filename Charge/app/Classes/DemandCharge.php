<?php
namespace App\Classes;

use App\Classes\CreateCharge\CreateChargeFactoryInterface;
use App\Repository\ChargeCategoryRepositoryInterface;
use App\Repository\ChargeRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

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
            'user_id' => $data['user_id'],
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

    public function customerDemandCharge($data) : ?array {

    }

    public function sellerDemandCharge($data) : ?array {

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
}
