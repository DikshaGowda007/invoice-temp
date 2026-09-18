<?php

namespace App\Modules\V1\Client\Services\Edit;

use App\Exceptions\DataNotFoundException;
use App\Http\Requests\V1\Client\Edit\DetailsRequest;
use App\Http\Services\AuthService;
use App\Models\Client;
use App\Modules\V1\Client\Bo\Edit\DetailsBo;
use App\Modules\V1\Client\Helpers\ClientHelper;
use App\Repositories\V1\ClientRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private ClientHelper $clientHelper,
        private readonly ClientRepository $clientRepository,
        private readonly AuthService $authService,
    ) {}

    public function prepareBo(DetailsRequest $detailsRequest): DetailsBo
    {
        return $this->clientHelper->prepareEditClientBo($detailsRequest);
    }

    public function edit(DetailsBo $detailsBo): array
    {
        $userId = $this->authService->getData()->get('userId');
        $id = $detailsBo->getId();

        $this->findClientOrFail($id, $userId);

        $clientDao = $this->clientHelper->prepareEditClientDao($detailsBo);
        $this->clientRepository->updateById($id, $clientDao);
        $client = $this->findClientOrFail($id, $userId);

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
