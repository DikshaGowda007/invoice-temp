<?php

namespace App\Modules\V1\Client\Services\Get;

use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Models\Client;
use App\Repositories\V1\ClientRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly AuthService $authService,
    ) {}

    public function get(int $id): array
    {
        $client = $this->findClientOrFail($id, $this->authService->getData()->get('userId'));

        return CommonUtils::successDataResponse([
            'client' => $this->formatClient(collect($client)),
        ]);
    }

    private function findClientOrFail(int $id, int $userId): Client
    {
        $client = $this->clientRepository->findByIdAndUserId($id, $userId)->first();

        if (! $client) {
            throw DataNotFoundException::withMessage('Client not found.');
        }

        return $client;
    }

    private function formatClient(Collection $client): array
    {
        return [
            'id' => $client->get('id'),
            'name' => $client->get('name'),
            'email' => $client->get('email'),
            'phone' => $client->get('phone'),
            'address' => $client->get('address'),
            'notes' => $client->get('notes'),
        ];
    }
}
