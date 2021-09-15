<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Model;

interface CustomerRepositoryInterface
{
    public function create(array $payload) : ?Model;
    public function find(array $columns = ["*"],$conditions = [], $relations = []) : ?Model;

}
