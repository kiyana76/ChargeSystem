<?php

namespace App\Repository\Eloquent;

use App\Models\Charge;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ChargeEloquentRepository implements \App\Repository\ChargeRepositoryInterface
{

    public function show(array $columns = ['*'], array $conditions = [], array $relation = []): ?Model
    {
        return Charge::select($columns)->where($conditions)->with($relation)->first();
    }

    public function create(array $payload): ?Model
    {
        try {
            return Charge::create($payload)->fresh();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function update(int $id, array $payload): ?Model
    {
        $model = $this->show(['*'], ['id' => $id]);
        return $model->update($payload) ? $model->fresh() : null;
    }

    public function index(array $columns = ['*'], array $conditions = [], array $relations = []): ?Collection
    {
        $charge_class = Charge::select($columns);

        if (isset($conditions['expire_date_from']) && $conditions['expire_date_from'] != null) {
            $date_from = Carbon::parse($conditions['expire_date_from'])->format('Y-m-d 00:00:00');
            $charge_class = $charge_class->where('expire_date', '>=', $date_from);
            unset($conditions['expire_date_from']);
        }

        if (isset($conditions['expire_date_to']) && $conditions['expire_date_to'] != null) {
            $date_to = Carbon::parse($conditions['expire_date_to'])->format('Y-m-d 23:59:59');
            $charge_class = $charge_class->where('expire_date', '<=', $date_to);
            unset($conditions['expire_date_to']);
        }

        if (isset($conditions['created_at']) && $conditions['created_at'] != null) {
            $charge_class = $charge_class->whereDate('created_at', $conditions['created_at']);
            unset($conditions['created_at']);
        }

        if (isset($conditions['charges_id']) && $conditions['charges_id'] != null) {
            $charge_class = $charge_class->whereIn('id', $conditions['charges_id']);
            unset($conditions['charges_id']);
        }

        return $charge_class->where($conditions)->with($relations)->get();
    }
}
