<?php

namespace App\Repositories\MySql;

use App\Models\User;
use App\Repositories\DAO\UserDAO;
use App\Repositories\UserRepository;

class UserRepositoryImpl implements UserRepository
{
    public function insert(UserDAO $userDao): User
    {
        return User::create($userDao->toArray());
    }
}
