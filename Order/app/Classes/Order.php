<?php

namespace App\Classes;

use App\Repositories\Eloquent\OrderEloquentRepository;
use App\Repositories\Eloquent\OrderItemEloquentRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

       return ['message' => 'order created successfully', 'body' => ['order' => $order, 'order_item' => $order_items], 'error' => false, 'status_code' => 201];
    }

    public function getAmountOrder($order_id) {
        $order = $this->orderRepository->show(['*'], ['id' => $order_id], ['orderItem']);

        $response = Http::get(config('api_gateway.charge_service_url') . 'charge-categories');
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

    public function payment($mobile, int $amount, $order_id)
    {
        $data = ['mobile' => $mobile, 'amount' => $amount, 'order_id' => $order_id];
        $response = Http::post(config('api_gateway.transaction_service_url') . 'payment', $data);
        $json_response = json_decode($response->getBody()->getContents());

        if (!$json_response)
            return ['message' => 'something wrong in payment', 'body' => [], 'error' => true, 'status_code' => 200];

        return $json_response;
    }

    public function update($order_id,  $status, $message)
    {
        $order = $this->orderRepository->show(['*'], ['id' => $order_id], ['orderItem']);

        DB::beginTransaction();
        if ($status == 'success') {
            $charges = [];
            foreach ($order->orderItem as $orderItem) {
                $charge = $this->getCharge($orderItem->category_id);

                if (!$charge) {
                    DB::rollBack();
                    return ['message' => 'something wrong in produce charge', 'body' => [], 'error' => true, 'status_code' => 200];
                }
                $this->orderItemRepository->update($orderItem->id, ['charge_id' => $charge->id]);
                array_push($charges, $charge);
            }
        }

        $this->orderRepository->update($order_id, ['status' => $status]);

        DB::commit();

        if ($status == 'success')
            return ['message' => 'charge created successfully', 'body' => $charges, 'error' => false, 'status_code' => 201];
        return ['message' => $message, 'body' => [], 'error' => true, 'status_code' => 200];

    }

    private function getCharge($charge_category_id)
    {
        $data = [
            'charge_category_id' => $charge_category_id,
            'count' => 1,
            'user_type' => 'customer',
            'company_id' => config('api_gateway.company_id'),
        ];

        $response = Http::post(config('api_gateway.charge_service_url') . 'charge/demand', $data);
        $json_response = json_decode($response->getBody()->getContents());

        if (!$json_response || $json_response->error)
            return false;

        return $json_response->body[0];
    }

    public function index(array $data)
    {
        return $this->orderRepository->index(['*'], $data, ['orderItem']);
    }

    public function indexWithChargeDetails(array $order_data, array $charge_data) {
        $orders = $this->index($order_data);
        $orderItem_id = $orders->pluck('orderItem.*.charge_id')->toArray();
        $charges_id_array = [];
        foreach ($orderItem_id as $charges_id) {
            foreach ($charges_id as $charge_id) {
                if ($charge_id)
                    array_push($charges_id_array, $charge_id);
            }
        }
        $charge_data['charges_id'] = $charges_id_array;

        $charges_id_query_param = http_build_query($charge_data);

        $response = Http::get(config('api_gateway.charge_service_url') . 'charge?' . $charges_id_query_param);
        $json_response = json_decode($response->getBody()->getContents());
        $response = $json_response->body;

        $result_array = [];
        foreach ($response as $charge) {
            foreach ($orders as $order) {
                foreach ($order->orderItem as $orderItem) {
                    if ($orderItem->charge_id == $charge->id) {
                        $result ['id'] = $order->id;
                        $result ['status'] = $order->status;
                        $result ['customer_id'] = $order->customer_id;
                        $result ['mobile'] = $order->mobile;
                        $result ['created_at'] = $order->created_at;
                        $result ['orderItem'] ['id'] = $charge->id ?? '';
                        $result ['orderItem'] ['charge_category_id'] = $charge->charge_category_id;
                        $result ['orderItem'] ['code'] = $charge->code;
                        $result ['orderItem'] ['expire_date'] = $charge->expire_date;
                        $result ['orderItem'] ['sold_status'] = $charge->sold_status;
                        $result ['orderItem'] ['status'] = $charge->status;
                        array_push($result_array, $result);
                    }
                }
            }
        }
        return $result_array;
    }
}
