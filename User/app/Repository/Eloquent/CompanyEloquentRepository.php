<?php

namespace App\Repository\Eloquent;

use App\Models\Company;
use App\Repository\CompanyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CompanyEloquentRepository implements CompanyRepositoryInterface
{

    public function create(array $payload): ?Model
    {
        return Company::create($payload);
    }

    public function index(array $conditions = []): ?Collection
    {
        return Company::where($conditions)->get();
    }
}
