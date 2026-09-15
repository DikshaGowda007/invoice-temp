<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\DAO\UserDAO;
use Illuminate\Database\Eloquent\Collection;

interface UserRepository
{
    public function insert(UserDAO $userDao): User;

    public function findByEmail(string $email): Collection;

    public function updateById(int $userId, UserDAO $userDao): bool;
}
