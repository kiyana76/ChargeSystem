<?php

namespace App\Policies;

use App\Models\User AS UserModel;

class User
{

    public function isAdmin(UserModel $user) {
        return $user->type == 'admin';
    }
}
