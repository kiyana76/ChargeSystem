<?php

namespace App\Classes;

use App\Repositories\Eloquent\OrderEloquentRepository;
use App\Repositories\Eloquent\OrderItemEloquentRepository;
use App\Repositories\SearchRepositoryInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class IndexOrder {
    private $orderRepository;
    private $searchRepository;

    public function __construct () {
        $this->orderRepository     = App::make(OrderEloquentRepository::class);
        $this->searchRepository    = App::make(SearchRepositoryInterface::class);
    }

    public function index (array $data) {
        return $this->searchRepository->index(['*'], $data, ['orderItem']);
    }

    public function indexWithChargeDetails (array $order_filters, array $charge_filters) {
        $orders           = $this->index($order_filters);
        $orderItem_id     = $orders->pluck('orderItem.*.charge_id')->toArray();
        $charges_id_array = [];
        foreach ($orderItem_id as $charges_id) {
            foreach ($charges_id as $charge_id) {
                if ($charge_id)
                    array_push($charges_id_array, $charge_id);
            }
        }
        $charge_filters['charges_id'] = $charges_id_array;

        $charges_id_query_param = http_build_query($charge_filters);

        $response      = Http::get(config('api_gateway.charge_service_url') . 'charge?' . $charges_id_query_param);
        $json_response = json_decode($response->getBody()->getContents());
        $response      = $json_response->body;

        $result_array = [];
        foreach ($response as $charge) {
            foreach ($orders as $order) {
                foreach ($order->orderItem as $orderItem) {
                    if ($orderItem->charge_id == $charge->id) {
                        $result ['id']                               = $order->id;
                        $result ['status']                           = $order->status;
                        $result ['customer_id']                      = $order->customer_id;
                        $result ['mobile']                           = $order->mobile;
                        $result ['created_at']                       = $order->created_at;
                        $result ['orderItem'] ['id']                 = $charge->id ?? '';
                        $result ['orderItem'] ['charge_category_id'] = $charge->charge_category_id;
                        $result ['orderItem'] ['code']               = $charge->code;
                        $result ['orderItem'] ['expire_date']        = $charge->expire_date;
                        $result ['orderItem'] ['sold_status']        = $charge->sold_status;
                        $result ['orderItem'] ['status']             = $charge->status;
                        array_push($result_array, $result);
                    }
                }
            }
        }
        return $result_array;
    }
}
