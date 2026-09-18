<?php

namespace App\Modules\V1\Client\Services\Delete;

use App\Constants\CommonConstant;
use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Models\Client;
use App\Repositories\DAO\V1\ClientDAO;
use App\Repositories\V1\ClientRepository;
use App\Utils\CommonUtils;

class DetailsService
{
    public function __construct(
        private ClientDAO $clientDao,
        private readonly ClientRepository $clientRepository,
        private readonly AuthService $authService,
    ) {}

    public function delete(int $id): array
    {
        $userId = $this->authService->getData()->get('userId');

        $this->findClientOrFail($id, $userId);
        $this->updateClient($id);

        return CommonUtils::successResponse('Client deleted successfully.');
    }

    private function findClientOrFail(int $id, int $userId): Client
    {
        $client = $this->clientRepository->findByIdAndUserId($id, $userId)->first();

        if (! $client) {
            throw DataNotFoundException::withMessage('Client not found.');
        }

        return $client;
    }

    private function updateClient(int $id): void
    {
        $this->clientDao->setIsDeleted(CommonConstant::IS_DELETED_YES);
        $this->clientRepository->updateById($id, $this->clientDao);
    }
}
