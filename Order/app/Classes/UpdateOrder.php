<?php

namespace App\Classes;

use App\Events\UpdateOrderEvent;
use App\Repositories\Eloquent\OrderEloquentRepository;
use App\Repositories\Eloquent\OrderItemEloquentRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateOrder {
    private $orderItemRepository;
    private $orderRepository;

    public function __construct () {
        $this->orderRepository     = App::make(OrderEloquentRepository::class);
        $this->orderItemRepository = App::make(OrderItemEloquentRepository::class);
    }

    public function update ($order_id, $status, $message) {
        $order = $this->orderRepository->show(['*'], ['id' => $order_id], ['orderItem']);

        DB::beginTransaction();
        $charges = [];
        foreach ($order->orderItem as $orderItem) {
            if ($status == 'success') {
                $charge = $this->getCharge($orderItem->category_id);

                if (!$charge) {
                    DB::rollBack();
                    return ['message' => 'something wrong in produce charge', 'body' => [], 'error' => true, 'status_code' => 200];
                }
                $this->orderItemRepository->update($orderItem->id, ['charge_id' => $charge->id]);
                array_push($charges, $charge);
                if (config('elasticquent.config.status') == 'enable')
                    event(new UpdateOrderEvent(array_merge((array)$charge, ['order_id' => $order_id, 'order_item_id' => $orderItem->id, 'order_status' => $status])));
            } else {
                if (config('elasticquent.config.status') == 'enable')
                    event(new UpdateOrderEvent(['order_id' => $order_id, 'id' => $orderItem->id, 'order_status' => $status]));
            }
        }

        $this->orderRepository->update($order_id, ['status' => $status]);

        DB::commit();

        if ($status == 'success')
            return ['message' => 'charge created successfully', 'body' => $charges, 'error' => false, 'status_code' => 201];
        return ['message' => $message, 'body' => [], 'error' => true, 'status_code' => 200];

    }

    private function getCharge ($charge_category_id) {
        $data = [
            'charge_category_id' => $charge_category_id,
            'count'              => 1,
            'user_type'          => 'customer',
            'company_id'         => config('api_gateway.company_id'),
        ];

        $response      = Http::post(config('api_gateway.charge_service_url') . 'charge/demand', $data);
        $json_response = json_decode($response->getBody()->getContents());

        if (!$json_response || $json_response->error)
            return false;

        return $json_response->body[0];
    }
}
