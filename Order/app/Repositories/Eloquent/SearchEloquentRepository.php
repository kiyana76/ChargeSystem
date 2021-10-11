<?php

namespace App\Repositories\Eloquent;

class SearchEloquentRepository implements \App\Repositories\SearchRepositoryInterface {

    public function index (array $columns = ['*'], array $conditions = []): ?array {
        // TODO: Implement index() method.
    }

    public function indexWithChargeDetails (array $columns = ['*'], array $conditions = []): ?array {
        // TODO: Implement indexWithChargeDetails() method.
    }
}
