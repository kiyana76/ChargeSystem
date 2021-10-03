<?php

namespace App\Classes\Traits;

use Illuminate\Support\Facades\Http;

trait Credits
{
    private function checkCredit ($user_id, $price): ?bool
    {
        $response = Http::get(config('charge.user_service_url') . 'get-credit?user_id=' . $user_id);
        $credit   = json_decode($response->getBody()->getContents())->body->credit;
        if (!isset($credit))
            return null;

        return $credit >= $price;

    }

    private function getPriceDemandCharges ($count, $category_id): int
    {
        $amount = $this->chargeCategoryRepository->findById($category_id)->amount;

        return $amount * $count;
    }

    private function changeSellerCredit ($user_id, $amount, $type)
    {
        $params = [
            'user_id' => $user_id,
            'amount'  => $amount,
            'type'    => $type,
        ];

        $response = Http::post(config('charge.user_service_url') . 'credit', $params);

        return json_decode($response->getBody()->getContents());
    }

    private function updateLockCredit ($credit_id, $type)
    {
        $params   = [
            'credit_log_id' => $credit_id,
            'type'          => $type,
        ];
        $response = Http::put(config('charge.user_service_url') . 'credit', $params);

        return json_decode($response->getBody()->getContents());
    }
}
