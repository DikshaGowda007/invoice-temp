<?php

namespace App\Modules\V1\Client\Services\List;

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

    public function list(): array
    {
        $userId = $this->authService->getData()->get('userId');
        $clients = $this->clientRepository->findByUserId($userId);

        return CommonUtils::successDataResponse([
            'clients' => $clients->map(fn (Client $client) => $this->formatClient(collect($client)))->values()->toArray(),
        ]);
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
