<?php
namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\UserRepositoryInterface;

class UserEloquentRepository implements UserRepositoryInterface
{

    public function findById(int $modelId, array $columns = ["*"], $relations = [], $appends = []): ?\Illuminate\Database\Eloquent\Model
    {
        return User::select($columns)->with($relations)->findOrFail($modelId)->append($appends);
    }

    public function create(array $payload): ?\Illuminate\Database\Eloquent\Model
    {
        $model = User::create($payload);
        return $model->fresh();
    }
}
