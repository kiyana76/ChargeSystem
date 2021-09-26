<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class OrderItemEloquentRepository implements OrderRepositoryInterface
{

    public function show(array $columns = ['*'], array $conditions = [], array $relation = []): ?Model
    {
        return OrderItem::select($columns)->where($conditions)->with($relation)->first();
    }

    public function create(array $payload): ?Model
    {
        return OrderItem::create($payload)->fresh();
    }

    public function update(int $id, array $payload): ?Model
    {

        $model = $this->show(['*'], ['id' => $id]);
        return $model->update($payload) ? $model->fresh() : null;
    }

    public function index(array $columns = ['*'], array $conditions = [], array $relations = []): ?Collection
    {
        return OrderItem::select($columns)->where($conditions)->with($relations)->get();
    }
}
