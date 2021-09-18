<?php

namespace app\Repository\Eloquent;

use App\Models\CreditLogs;
use App\Repository\CreditRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CreditEloquentRepository implements CreditRepositoryInterface
{

    public function findById(int $modelId, array $columns = ["*"], $relations = [], $appends = [], $conditions = []): ?Model
    {
        return CreditLogs::select($columns)->with($relations)->where($conditions)->findOrFail($modelId)->append($appends);
    }

    public function create(array $payload): ?Model
    {
        return CreditLogs::create($payload);
    }

    public function update(int $modelId, array $payload, array $conditions = []): bool
    {
        $model = $this->findById($modelId,['*'],[],[],$conditions);
        if ($model)
            return $model->update($payload);
        return false;
    }

    public function index(array $conditions = []): ?Collection
    {
        $model = CreditLogs::select(["*"]);

        if (isset($conditions['company_id']) && $conditions['company_id'] != null)
            $model->whereHas('user.company', function ($q) use ($conditions) {
                $q->where('id', $conditions['company_id']);
            });
        if (isset($conditions['seller_id']) && $conditions['seller_id'] != null)
            $model->where('user_id', $conditions['seller_id']);

        return $model->get();
    }
}
