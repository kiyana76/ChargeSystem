<?php

namespace App\Repository\Eloquent;

use App\Models\ChargeLog;
use App\Repository\ChargeLogRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ChargeLogEloquentRepository implements ChargeLogRepositoryInterface
{

    public function show (array $columns = ['*'], array $conditions = [], array $relation = []): ?Model
    {
        return ChargeLog::select($columns)->where($conditions)->with($relation)->first();
    }

    public function create (array $payload): ?Model
    {
        try {
            return ChargeLog::create($payload)->fresh();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function index (array $columns = ['*'], array $conditions = [], array $relations = []): ?Collection
    {

        return ChargeLog::select($columns)->where($conditions)->with($relations)->get();
    }
}
