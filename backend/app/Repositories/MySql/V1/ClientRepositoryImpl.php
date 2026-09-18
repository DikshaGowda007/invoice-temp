<?php

namespace App\Repositories\MySql\V1;

use App\Constants\CommonConstant;
use App\Models\Client;
use App\Repositories\DAO\V1\ClientDAO;
use App\Repositories\V1\ClientRepository;
use Illuminate\Database\Eloquent\Collection;

class ClientRepositoryImpl implements ClientRepository
{
    public function insert(ClientDAO $clientDao): Client
    {
        return Client::create($clientDao->toArray());
    }

    public function findByUserId(int $userId): Collection
    {
        return Client::where('user_id', $userId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function findByIdAndUserId(int $id, int $userId): Collection
    {
        return Client::where('id', $id)
            ->where('user_id', $userId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function updateById(int $id, ClientDAO $clientDao): bool
    {
        return Client::where('id', $id)->update($clientDao->toArray());
    }
}
