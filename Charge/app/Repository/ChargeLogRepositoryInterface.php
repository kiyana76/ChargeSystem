<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ChargeLogRepositoryInterface
{
    public function show (array $columns = ['*'], array $conditions = [], array $relation = []): ?Model;

    public function create (array $payload): ?Model;

    public function index (array $columns = ['*'], array $conditions = [], array $relations = []): ?Collection;
}
