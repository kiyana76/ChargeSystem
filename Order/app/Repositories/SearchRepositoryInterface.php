<?php

namespace App\Repositories;

interface SearchRepositoryInterface {
    public function index(array $columns = ['*'], array $conditions = []) :? array;
    public function indexWithChargeDetails(array $columns = ['*'], array $conditions = []) :? array;
}
