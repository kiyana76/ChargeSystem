<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Model;

interface ChargeRepositoryInterface
{
    public function show( array$columns = ['*'], array $conditions = [], array $relation = []) : ?Model;
    public function create(array $payload) : ?Model;
    public function update(int $id, array $payload) : ?Model;
}
