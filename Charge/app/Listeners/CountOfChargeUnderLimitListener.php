<?php

namespace App\Listeners;

use App\Classes\DemandCharge;
use App\Events\ChargeSoldEvent;
use App\Repository\ChargeCategoryRepositoryInterface;
use App\Repository\ChargeRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;

class CountOfChargeUnderLimitListener
{

    private $chargeRepository;
    private $chargeCategoryRepository;

    public function __construct()
    {
        $this->chargeRepository = App::make(ChargeRepositoryInterface::class);
        $this->chargeCategoryRepository = App::make(ChargeCategoryRepositoryInterface::class);
    }


    public function handle(ChargeSoldEvent $event)
    {
        $charge_categories = $this->chargeCategoryRepository->index();
        $charge_class = new DemandCharge();
        foreach ($charge_categories as $charge_category) {
            $charges = $this->chargeRepository->index(["*"], ['status' => 'valid', 'sold_status' => 'free', 'charge_category_id' => $charge_category->id]);
            if ($charges->count() < config('charge.limit_count_produced_charge')) {
                $data = [
                    'charge_category_id' => $charge_category->id,
                    'amount' => $charge_category->amount,
                    'sold_status' => 'free',
                    'status' => 'valid',
                ];
                for ($i = 0; $i < config('charge.automatic_charge_produce_count'); $i++) {
                    $charge_class->createCharge($data);
                }
            }
        }
    }
}
