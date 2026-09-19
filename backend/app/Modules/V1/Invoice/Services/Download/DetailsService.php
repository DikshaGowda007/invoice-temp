<?php

namespace App\Modules\V1\Invoice\Services\Download;

use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Jobs\GenerateInvoicePdfJob;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DetailsService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly AuthService $authService,
    ) {}

    /**
     * @return array{request_id: string}
     */
    public function dispatch(int $id): array
    {
        $userId = $this->authService->getData()->get('userId');
        $this->findInvoiceOrFail($id, $userId);

        $requestId = (string) Str::uuid();

        Cache::put("invoice-pdf:{$requestId}", [
            'status' => 'pending',
            'user_id' => $userId,
        ], now()->addMinutes(10));

        GenerateInvoicePdfJob::dispatch($requestId, $id, $userId);

        return CommonUtils::successDataResponse([
            'request_id' => $requestId,
        ]);
    }

    private function findInvoiceOrFail(int $id, int $userId): Collection
    {
        $invoice = $this->invoiceRepository->findByIdAndUserId($id, $userId)->first();

        if (! $invoice) {
            throw DataNotFoundException::withMessage('Invoice not found.');
        }

        return collect($invoice);
    }
}
