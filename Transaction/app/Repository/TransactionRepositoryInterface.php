<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TransactionRepositoryInterface
{
    public function show( array$columns = ['*'], array $conditions = [], array $relation = []) : ?Model;
    public function create(array $payload) : ?Model;
    public function update(int $id, array $payload) : ?Model;
    public function index(array $columns = ['*'], array $conditions = [], array $relations = []) : ?Collection;
}
