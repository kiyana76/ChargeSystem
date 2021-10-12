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
        if (config('elasticquent.config.status') == 'enable') {
            return $this->searchRepository->indexWithChargeDetails(['*'], array_merge($order_filters, $charge_filters), ['orderItem']);

        } else {
            return $this->searchRepository->indexWithChargeDetails(['*'], ['order_filters' => $order_filters, 'charge_filters' => $charge_filters]);
        }
    }
}
