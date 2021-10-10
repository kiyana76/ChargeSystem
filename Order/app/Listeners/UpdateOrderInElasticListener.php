<?php

namespace App\Listeners;

use App\Events\CreateOrderEvent;
use App\Events\UpdateOrderEvent;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateOrderInElasticListener implements ShouldQueue{

    public function __construct () {
        //
    }


    public function handle (UpdateOrderEvent $event) {
        $client             = ClientBuilder::create()->setHosts(config('elasticquent.config.hosts'))->build();
        $code               = $event->data['code'] ?? '';
        $charge_category_id = $event->data['charge_category_id'] ?? '';
        $status             = $event->data['status'] ?? '';
        $sold_status        = $event->data['sold_status'] ?? '';
        $amount             = $event->data['amount'] ?? '';
        $expire_date        = $event->data['expire_date'] ?? '';
        $order_status       = $event->data['order_status'];
        $update_string      = "ctx._source['order_status'] = '$order_status';
        ctx._source['code'] = '$code';
        ctx._source['charge_category_id'] = '$charge_category_id';
        ctx._source['status'] = '$status';
        ctx._source['sold_status'] = '$sold_status';
        ctx._source['amount'] = '$amount';
        ctx._source['expire_date'] = '$expire_date'";
        $params             = [
            'index'     => 'charges',
            'type'      => '_doc',
            'conflicts' => 'proceed',
            'body'      => [
                "script" => [
                    "source" => $update_string,
                    "lang"   => "painless",
                ],
                "query"  => [
                    "query_string" => [
                        "query"  => $event->data['id'], //order_item
                        "fields" => ["id"],
                    ],
                ],
            ],

        ];
        $client->updateByQuery($params);
    }
}
