<?php

namespace App\Modules\V1\Invoice\Services\DownloadResult;

use App\Exceptions\DataNotFoundException;
use App\Exceptions\InvalidDataException;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Cache;

class DetailsService
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * @return array{content: string, filename: string}
     */
    public function result(string $requestId): array
    {
        $userId = $this->authService->getData()->get('userId');
        $entry = Cache::get("invoice-pdf:{$requestId}");

        if (! $entry || (int) $entry['user_id'] !== $userId) {
            throw DataNotFoundException::withMessage('Request not found.');
        }

        if ($entry['status'] === 'failed') {
            throw DataNotFoundException::withMessage('Invoice not found.');
        }

        if ($entry['status'] !== 'ready') {
            throw InvalidDataException::withMessage('PDF is not ready yet.');
        }

        return [
            'content' => base64_decode($entry['content']),
            'filename' => $entry['filename'],
        ];
    }
}
