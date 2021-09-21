<?php
namespace App\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ChargeCategoryRepositoryInterface
{
    public function index(array $columns = ['*'], array $conditions = [], array $relations = []) : ?Collection;
    public function findById(int $modelId, array $columns = ["*"], $relations = []): ?Model;
}
