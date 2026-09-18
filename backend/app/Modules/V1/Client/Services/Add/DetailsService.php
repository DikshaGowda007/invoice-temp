<?php

namespace App\Modules\V1\Client\Services\Add;

use App\Http\Requests\V1\Client\Add\DetailsRequest;
use App\Http\Services\AuthService;
use App\Modules\V1\Client\Bo\Add\DetailsBo;
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
        return $this->clientHelper->prepareAddClientBo($detailsRequest);
    }

    public function add(DetailsBo $detailsBo): array
    {
        $userId = $this->authService->getData()->get('userId');
        $clientDao = $this->clientHelper->prepareAddClientDao($detailsBo, $userId);
        $client = $this->clientRepository->insert($clientDao);

        return CommonUtils::successDataResponse([
            'client' => $this->formatClient(collect($client)),
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
