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

        $update_string = '';
        foreach ($event->data as $key => $value) {
            $update_string .= "ctx._source['$key'] = '$value';";
        }

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
                        "query"  => $event->data['id'], //order_item_id
                        "fields" => ["id"],
                    ],
                ],
            ],

        ];
        $client->updateByQuery($params);
    }
}
