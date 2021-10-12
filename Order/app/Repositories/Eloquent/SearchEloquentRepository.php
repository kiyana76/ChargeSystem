<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\SearchRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SearchEloquentRepository implements SearchRepositoryInterface {

    public function index (array $columns = ['*'], array $conditions = [], array $relations = []): ? Collection {
        $order_class = Order::select($columns);
        if (isset($conditions['date_from']) && $conditions['date_from'] != null) {
            $date_from   = Carbon::parse($conditions['date_from'])->format('Y-m-d 00:00:00');
            $order_class = $order_class->where('created_at', '>=', $date_from);
            unset($conditions['date_from']);
        }

        if (isset($conditions['date_to']) && $conditions['date_to'] != null) {
            $date_to     = Carbon::parse($conditions['date_to'])->format('Y-m-d 23:59:59');
            $order_class = $order_class->where('created_at', '<=', $date_to);
            unset($conditions['date_to']);
        }

        return $order_class->where($conditions)->with($relations)->get();
    }

    public function indexWithChargeDetails (array $columns = ['*'], array $conditions = [] , array $relations = []): ?array {
        $order_filters    = $conditions['order_filters'];
        $charge_filters   = $conditions['charge_filters'];
        $orders           = $this->index(['*'], $order_filters, ['orderItem']);
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
