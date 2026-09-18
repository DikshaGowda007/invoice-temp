<?php

namespace App\Providers;

use App\Repositories\MySql\UserRepositoryImpl;
use App\Repositories\MySql\V1\ClientRepositoryImpl;
use App\Repositories\UserRepository;
use App\Repositories\V1\ClientRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class, UserRepositoryImpl::class);
        $this->app->bind(ClientRepository::class, ClientRepositoryImpl::class);
    }

    public function boot(): void {}
}
