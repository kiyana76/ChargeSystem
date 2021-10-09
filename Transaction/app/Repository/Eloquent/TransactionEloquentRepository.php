<?php

namespace App\Repository\Eloquent;

use App\Models\Transaction;
use App\Repository\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TransactionEloquentRepository implements TransactionRepositoryInterface
{
    public function show(array $columns = ['*'], array $conditions = [], array $relation = []): ?Model
    {
        return Transaction::select($columns)->where($conditions)->with($relation)->first();
    }

    public function create(array $payload): ?Model
    {
        return Transaction::create($payload)->fresh();
    }

    public function update(int $id, array $payload): ?Model
    {
        $model = $this->show(['*'], ['id' => $id]);
        return $model->update($payload) ? $model->fresh() : null;
    }

    public function index(array $columns = ['*'], array $conditions = [], array $relations = []): ?Collection
    {
        return Transaction::select($columns)->where($conditions)->with($relations)->get();
    }
}
