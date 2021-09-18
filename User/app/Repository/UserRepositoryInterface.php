<?php
namespace App\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface {
    public function findById(int $modelId, array $columns = ["*"], $relations = []): ?Model;
    public function create(array $payload) :?Model;
}
