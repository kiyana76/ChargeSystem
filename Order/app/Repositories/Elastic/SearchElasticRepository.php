<?php

namespace App\Repositories\Elastic;

use App\Repositories\SearchRepositoryInterface;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Collection;

class SearchElasticRepository implements SearchRepositoryInterface {

    public function index (array $columns = ['*'], array $conditions = [], array $relations = []): ? Collection {
        $search_condition = [];
        foreach ($conditions as $key => $value) {
            $match = ['match' => [$key => $value]];
            array_push($search_condition, $match);
        }
        //dd($search_condition);
        $client = ClientBuilder::create()->setHosts(config('elasticquent.config.hosts'))->build();
        $result = $client->search([
            'index' => config('elasticquent.default_index'),
            'type'  => '_doc',
            'body'  => [
                'query' => [
                    'bool' => [
                        'must' => $search_condition
                    ]
                ]
            ]
                                  ]);
        $result = $result['hits']['hits'];
        $order_columns = [
            'id' => 'order_id',
            'customer_id' => 'customer_id',
            'status' => 'order_status',
            'mobile' => 'mobile',
            'created_at' => 'created_at'
        ];
        $order_item_columns = [
            'id' => 'id',
            'order_id' => 'order_id',
            'category_id' => 'category_id',
            'charge_id' => 'charge_id',
        ];

        $collection = [];
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
                            $collection[$key]['orderItem'][$order_item_eloquent_column] = $elastic_value;
                        }
                    }
                }
            }
        }

        return collect($collection);
    }

    /*public function indexWithChargeDetails (array $columns = ['*'], array $conditions = []): ?array {
        // TODO: Implement indexWithChargeDetails() method.
    }*/
}
