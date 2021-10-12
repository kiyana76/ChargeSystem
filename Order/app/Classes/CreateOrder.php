<?php

namespace App\Classes;

use App\Events\CreateOrderEvent;
use App\Events\UpdateOrderEvent;
use App\Repositories\Eloquent\OrderEloquentRepository;
use App\Repositories\Eloquent\OrderItemEloquentRepository;
use Elasticquent\ElasticquentCollectionTrait;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CreateOrder {

    private $orderItemRepository;
    private $orderRepository;

    public function __construct () {
        $this->orderRepository     = App::make(OrderEloquentRepository::class);
        $this->orderItemRepository = App::make(OrderItemEloquentRepository::class);
    }

    public function create ($data) {
        $order_data['mobile']      = $data['mobile'];
        $order_data['customer_id'] = $data['customer_id'];
        $order_data['status']      = 'pending';

        $order = $this->orderRepository->create($order_data);

        $order_items = [];
        foreach ($data['charge_category_id'] as $key => $value) {
            $order_item_data[$key]['category_id'] = $value;
            $order_item_data[$key]['order_id']    = $order->id;
            $order_items[$key]                    = $this->orderItemRepository->create($order_item_data[$key]);

            if (config('elasticquent.config.status') == 'enable')
                event(new CreateOrderEvent(array_merge($order->toArray(), $order_items[$key]->toArray())));
        }

        return ['message' => 'order created successfully', 'body' => ['order' => $order, 'order_item' => $order_items], 'error' => false, 'status_code' => 201];
    }

    public function getAmountOrder ($order_id) {
        $order = $this->orderRepository->show(['*'], ['id' => $order_id], ['orderItem']);

        $response      = Http::get(config('api_gateway.charge_service_url') . 'charge-categories');
        $json_response = json_decode($response->getBody()->getContents());
        if ($json_response->error)
            return ['message' => 'something wrong in get amount', 'body' => [], 'error' => true, 'status_code' => 200];

        $charge_categories = $json_response->body;

        $amount = 0;
        foreach ($order->orderItem as $orderItem) {
            foreach ($charge_categories as $charge_category) {
                if ($charge_category->id == $orderItem->category_id) {
                    $amount += $charge_category->amount;
                    break;
                }
            }
        }

        return $amount;
    }

    public function payment ($mobile, int $amount, $order_id) {
        $data          = ['mobile' => $mobile, 'amount' => $amount, 'order_id' => $order_id];
        $response      = Http::post(config('api_gateway.transaction_service_url') . 'payment', $data);
        $json_response = json_decode($response->getBody()->getContents());

        if (!$json_response)
            return ['message' => 'something wrong in payment', 'body' => [], 'error' => true, 'status_code' => 200];

        return $json_response;
    }
}
