<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CompanyRepositoryInterface
{
    public function create(array $payload): ?Model;
    public function index(array $conditions = []): ?Collection;

}
