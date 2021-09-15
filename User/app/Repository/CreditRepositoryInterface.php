<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CreditRepositoryInterface
{
    public function findById(int $modelId, array $columns = ["*"], $relations = [], $appends = [], array $conditions = []): ?Model;
    public function create(array $payload) :?Model;
    public function update(int $modelId, array $payload, array $conditions = []) : bool;
    public function index(array $conditions = []) : ?Collection;
}
