<?php

namespace App\Repository\Eloquent;

use App\Models\Charge;
use Illuminate\Database\Eloquent\Model;

class ChargeEloquentRepository implements \App\Repository\ChargeRepositoryInterface
{

    public function show(array $columns = ['*'], array $conditions = [], array $relation = []): ?Model
    {
        return Charge::select($columns)->where($conditions)->with($relation)->first();
    }

    public function create(array $payload): ?Model
    {
        return Charge::create($payload);
    }

    public function update(int $id, array $payload): bool
    {
        $model = $this->show(['*'], ['id' => $id]);
        return $model->update($payload);
    }
}
