<?php

namespace App\Listeners;

use App\Events\CreateOrderEvent;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateOrderInElasticListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CreateOrderEvent  $event
     * @return void
     */
    public function handle(CreateOrderEvent $event)
    {
        $client = ClientBuilder::create()->setHosts(config('elasticquent.config.hosts'))->build();
        $params = [
            'index' => 'charges',
            'id' => $event->data['id'], //order_item_id
            'type' => '_doc',
            'body' => $event->data,
        ];
        $client->index($params);
    }
}
