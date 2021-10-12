<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface SearchRepositoryInterface {
    public function index(array $columns = ['*'], array $conditions = [], array $relations = []) :? Collection;
    public function indexWithChargeDetails(array $columns = ['*'], array $conditions = [], array $relations = []) :? Collection;
}
