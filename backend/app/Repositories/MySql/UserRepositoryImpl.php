<?php

namespace App\Repositories\MySql;

use App\Models\User;
use App\Repositories\DAO\UserDAO;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class UserRepositoryImpl implements UserRepository
{
    public function insert(UserDAO $userDao): User
    {
        return User::create($userDao->toArray());
    }

    public function findByEmail(string $email): Collection
    {
        return User::where('email', $email)->get();
    }

    public function updateById(int $userId, UserDAO $userDao): bool
    {
        $userDao->setUpdatedAt(Carbon::now()->format('Y-m-d H:i:s'));

        return User::where('id', $userId)->update($userDao->toArray());
    }
}
