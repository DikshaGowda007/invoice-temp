<?php

namespace App\Providers;

use App\Repositories\MySql\UserRepositoryImpl;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class, UserRepositoryImpl::class);
    }

    public function boot(): void {}
}
