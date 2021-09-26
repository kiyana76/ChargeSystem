<?php

namespace App\Classes;

use App\Repositories\Eloquent\OrderEloquentRepository;
use App\Repositories\Eloquent\OrderItemEloquentRepository;
use Illuminate\Support\Facades\App;

class Order
{

    private $orderItemRepository;
    private $orderRepository;

    public function __construct()
    {
        $this->orderRepository = App::make(OrderEloquentRepository::class);
        $this->orderItemRepository = App::make(OrderItemEloquentRepository::class);
    }

    public function create($data) {
       $order_data['mobile'] = $data['mobile'];
       $order_data['customer_id'] = $data['customer_id'];
       $order_data['status'] = 'pending';

       $order = $this->orderRepository->create($order_data);

       $order_items = [];
       foreach ($data['charge_category_id'] as $key => $value) {
           $order_item_data[$key]['category_id'] = $value;
           $order_item_data[$key]['order_id'] = $order->id;
           $order_items[$key] = $this->orderItemRepository->create($order_item_data[$key]);
       }

       return ['message' => 'order created successfully', 'body' => [$order, $order_items], 'error' => false, 'status_code' => 201];
    }
}
