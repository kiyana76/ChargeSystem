<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\OrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class OrderEloquentRepository implements OrderRepositoryInterface
{

    public function show(array $columns = ['*'], array $conditions = [], array $relation = []): ?Model
    {
        return Order::select($columns)->where($conditions)->with($relation)->first();
    }

    public function create(array $payload): ?Model
    {
        return Order::create($payload)->fresh();
    }

    public function update(int $id, array $payload): ?Model
    {

        $model = $this->show(['*'], ['id' => $id]);
        return $model->update($payload) ? $model->fresh() : null;
    }

    public function index(array $columns = ['*'], array $conditions = [], array $relations = []): ?Collection
    {
        $order_class = Order::select($columns);
        if (isset($conditions['date_from']) && $conditions['date_from'] != null) {
            $date_from = Carbon::parse($conditions['date_from'])->format('Y-m-d 00:00:00');
            $order_class = $order_class->where('created_at', '>=', $date_from);
            unset($conditions['date_from']);
        }

        if (isset($conditions['date_to']) && $conditions['date_to'] != null) {
            $date_to = Carbon::parse($conditions['date_to'])->format('Y-m-d 23:59:59');
            $order_class = $order_class->where('created_at', '<=', $date_to);
            unset($conditions['date_to']);
        }

        return $order_class->where($conditions)->with($relations)->get();
    }
}
