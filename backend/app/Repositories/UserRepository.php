<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\DAO\UserDAO;

interface UserRepository
{
    public function insert(UserDAO $userDao): User;
}
