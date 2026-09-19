<?php

namespace App\Modules\V1\Invoice\Services\DownloadStatus;

use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Utils\CommonUtils;
use Illuminate\Support\Facades\Cache;

class DetailsService
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function status(string $requestId): array
    {
        $userId = $this->authService->getData()->get('userId');
        $entry = Cache::get("invoice-pdf:{$requestId}");

        if (! $entry || (int) $entry['user_id'] !== $userId) {
            throw DataNotFoundException::withMessage('Request not found.');
        }

        return CommonUtils::successDataResponse([
            'status' => $entry['status'],
        ]);
    }
}
