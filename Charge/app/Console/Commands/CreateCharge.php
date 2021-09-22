<?php

namespace App\Console\Commands;

use App\Classes\DemandCharge;
use App\Repository\ChargeCategoryRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class CreateCharge extends Command
{

    protected $signature = 'create:charge';


    protected $description = 'create charge for admins';
    private $chargeCategoryRepository;

    public function __construct()
    {
        $this->chargeCategoryRepository = App::make(ChargeCategoryRepositoryInterface::class);
        parent::__construct();
    }

    public function handle()
    {
        $charge_class = new DemandCharge();
        $charge_categories = $this->chargeCategoryRepository->index();
        foreach ($charge_categories as $charge_category) {
            $data = [
                'charge_category_id' => $charge_category->id,
                'amount' => $charge_category->amount,
                'sold_status' => 'free',
                'status' => 'valid',
            ];
            for ($i = 0; $i < config('charge.automatic_charge_produce_every_night'); $i++) {
                $charge_class->createCharge($data);

                echo 'charge produced';
            }
        }
    }
}
