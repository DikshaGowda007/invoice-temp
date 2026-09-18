<?php

namespace App\Repositories\V1;

use App\Models\Client;
use App\Repositories\DAO\V1\ClientDAO;
use Illuminate\Database\Eloquent\Collection;

interface ClientRepository
{
    public function insert(ClientDAO $clientDao): Client;

    public function findByUserId(int $userId): Collection;

    public function findByIdAndUserId(int $id, int $userId): Collection;

    public function updateById(int $id, ClientDAO $clientDao): bool;
}
