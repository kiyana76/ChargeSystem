<?php

namespace App\Repositories\Elastic;

use App\Repositories\SearchRepositoryInterface;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Collection;

class SearchElasticRepository implements SearchRepositoryInterface {

    public function index (array $columns = ['*'], array $conditions = [], array $relations = []): ?Collection {
        $conditions         = $this->makeConditions($conditions);
        $must_condition     = $conditions['must_condition'];
        $filter_condition   = $conditions['filter_condition'];
        $order_columns      = [
            'id'          => 'order_id',
            'customer_id' => 'customer_id',
            'status'      => 'order_status',
            'mobile'      => 'mobile',
            'created_at'  => 'created_at',
        ];
        $order_item_columns = [
            'id'          => 'id',
            'order_id'    => 'order_id',
            'category_id' => 'category_id',
            'charge_id'   => 'charge_id',
        ];
        return $this->search($must_condition, $filter_condition, $order_columns, $order_item_columns, $relations);
    }

    public function indexWithChargeDetails (array $columns = ['*'], array $conditions = [], array $relations = []): ?Collection {
        $conditions         = $this->makeConditions($conditions);
        $must_condition     = $conditions['must_condition'];
        $filter_condition   = $conditions['filter_condition'];
        $order_columns      = [
            'id'          => 'order_id',
            'customer_id' => 'customer_id',
            'status'      => 'order_status',
            'mobile'      => 'mobile',
            'created_at'  => 'created_at',
        ];
        $order_item_columns = [
            'id'          => 'id',
            'order_id'    => 'order_id',
            'category_id' => 'category_id',
            'charge_id'   => 'charge_id',
            'code'        => 'code',
            'expire_date' => 'expire_date',
            'sold_status' => 'sold_status',
            'status'      => 'status'
        ];
        return $this->search($must_condition, $filter_condition, $order_columns, $order_item_columns, $relations);
    }

    private function makeConditions ($conditions): array {
        $must_condition   = [];
        $filter_condition = [];

        if (isset($conditions['date_from'])) {
            $date_from        = $conditions['date_from'] . 'T00:00:00';
            $filter_condition = ['range' => ['created_at' => ['gte' => $date_from]]];
            unset($conditions['date_from']);
        }

        if (isset($conditions['date_to'])) {
            $date_to          = $conditions['date_to'] . 'T23:59:59';
            $filter_condition = ['range' => ['created_at' => ['lte' => $date_to]]];
            unset($conditions['date_to']);
        }

        if (isset($conditions['expire_date_from'])) {
            $expire_date_from = $conditions['expire_date_from'] . 'T00:00:00';
            $filter_condition = ['range' => ['created_at' => ['gte' => $expire_date_from]]];
            unset($conditions['expire_date_from']);
        }

        if (isset($conditions['expire_date_to'])) {
            $expire_date_to   = $conditions['expire_date_to'] . 'T23:59:59';
            $filter_condition = ['range' => ['created_at' => ['lte' => $expire_date_to]]];
            unset($conditions['expire_date_to']);
        }

        // rename status to order_status
        if (isset($conditions['status'])) {
            $conditions['order_status'] = $conditions['status'];
            unset($conditions['status']);
        }

        foreach ($conditions as $key => $value) {
            $match = ['match' => [$key => $value]];
            array_push($must_condition, $match);
        }

        return ['must_condition' => $must_condition, 'filter_condition' => $filter_condition];
    }

    private function search($must_condition, $filter_condition, $order_columns, $order_item_columns, $relations) {
        $collection         = [];
        $params             = [
            'index' => config('elasticquent.default_index'),
            'type'  => '_doc',
            'size'  => 9000,
            'from'  => 0,
            'sort'  => ['order_id'],
            'body'  => [
                'query' => [
                    'bool' => [
                        'must'   => $must_condition,
                        'filter' => $filter_condition,
                    ],
                ],
            ]];
        $client             = ClientBuilder::create()->setHosts(config('elasticquent.config.hosts'))->build();
        $result             = $client->search($params);
        $result             = $result['hits']['hits'];

        foreach ($result as $key => $item) {
            foreach ($item['_source'] as $elastic_key => $elastic_value) {
                foreach ($order_columns as $order_eloquent_column => $order_elastic_column) {
                    if ($elastic_key == $order_elastic_column) {
                        $collection[$key][$order_eloquent_column] = $elastic_value;
                    }
                }
                if (in_array("orderItem", $relations)) {
                    foreach ($order_item_columns as $order_item_eloquent_column => $order_item_elastic_column) {
                        if ($elastic_key == $order_item_elastic_column) {
                            $collection[$key]['orderItem'][0][$order_item_eloquent_column] = $elastic_value;
                        }
                    }
                }
            }
        }

        // for remove duplicate order and merge order_item of order removed
        foreach ($collection as &$datum) {
            $count = 0;
            foreach ($collection as $key => $value) {
                if ($value['id'] == $datum['id']) {
                    if ($count >= 1) {
                        if (in_array("orderItem", $relations)) {
                            array_push($datum['orderItem'], $value['orderItem'][0]);
                        }
                        unset($collection[$key]);
                        $collection = array_values($collection);
                    }
                    $count++;
                }
            }
        }
        return collect($collection);
    }
}
