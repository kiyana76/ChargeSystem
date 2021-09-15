<?php

namespace App\Repository\Eloquent;

use App\Models\Customer;
use App\Repository\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CustomerEloquentRepository implements CustomerRepositoryInterface
{

    public function create(array $payload): ?Model
    {
        $model = Customer::create($payload);
        return $model->fresh();
    }

    public function find(array $columns = ["*"], $conditions = [],  $relations = []): ?Model
    {
        return Customer::select($columns)->where($conditions)->first();

    }
}
