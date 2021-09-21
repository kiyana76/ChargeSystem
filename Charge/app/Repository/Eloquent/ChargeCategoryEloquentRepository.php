<?php

namespace App\Repository\Eloquent;

use App\Models\ChargeCategory;
use App\Repository\ChargeCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ChargeCategoryEloquentRepository implements ChargeCategoryRepositoryInterface
{

    public function index(array $columns = ['*'], array $conditions = [], array $relations = []): ?Collection
    {
        return ChargeCategory::select($columns)->where($conditions)->with($relations)->get();
    }


    public function findById(int $modelId, array $columns = ["*"], $relations = []): ?Model
    {
        return ChargeCategory::select($columns)->with($relations)->find($modelId);
    }
}
