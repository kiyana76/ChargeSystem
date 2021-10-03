<?php

namespace App\Classes;

use App\Repository\ChargeLogRepositoryInterface;
use App\Repository\ChargeRepositoryInterface;
use Illuminate\Support\Facades\App;

class Burnt
{
    private $chargeRepository;
    private $chargeLogRepository;

    public function __construct ()
    {
        $this->chargeRepository    = App::make(ChargeRepositoryInterface::class);
        $this->chargeLogRepository = App::make(ChargeLogRepositoryInterface::class);
    }

    public function burnt (array $data)
    {
        $charge = $this->chargeRepository->show(['*'], ['code' => $data['code']]);
        if ($charge == null) {
            $this->chargeLogRepository->create([
                                                   'code'   => $data['code'],
                                                   'mobile' => $data['mobile'],
                                                   'status' => 'not_found',
                                               ]);

            return ['message' => 'this code is not found', 'error' => true, 'status_code' => 200];
        } elseif ($charge->status == 'invalid' || $charge->status == 'lock' || $charge->sold_status == 'free') {
            $this->chargeLogRepository->create([
                                                   'charge_id' => $charge->id,
                                                   'code'      => $data['code'],
                                                   'mobile'    => $data['mobile'],
                                                   'status'    => 'fail',
                                               ]);

            return ['message' => 'this code is not valid', 'error' => true, 'status_code' => 200];
        } elseif ($charge->sold_status == 'burnt') {
            $this->chargeLogRepository->create([
                                                   'charge_id' => $charge->id,
                                                   'code'      => $data['code'],
                                                   'mobile'    => $data['mobile'],
                                                   'status'    => 'used',
                                               ]);

            return ['message' => 'this code was used before', 'error' => true, 'status_code' => 200];
        }

        $this->chargeLogRepository->create([
                                               'charge_id' => $charge->id,
                                               'code'      => $data['code'],
                                               'mobile'    => $data['mobile'],
                                               'status'    => 'store',
                                           ]);

        $this->chargeRepository->update($charge->id, ['sold_status' => 'burnt']);


        return ['message' => 'mobile charged!', 'error' => false, 'status_code' => 200];
    }
}
