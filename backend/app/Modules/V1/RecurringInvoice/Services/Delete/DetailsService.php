<?php

namespace App\Modules\V1\RecurringInvoice\Services\Delete;

use App\Constants\CommonConstant;
use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Repositories\DAO\V1\RecurringInvoiceDAO;
use App\Repositories\V1\RecurringInvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private RecurringInvoiceDAO $recurringInvoiceDao,
        private readonly RecurringInvoiceRepository $recurringInvoiceRepository,
        private readonly AuthService $authService,
    ) {}

    public function delete(int $id): array
    {
        $userId = $this->authService->getData()->get('userId');

        $this->findRecurringInvoiceOrFail($id, $userId);
        $this->updateRecurringInvoice($id);

        return CommonUtils::successResponse('Recurring invoice deleted successfully.');
    }

    private function findRecurringInvoiceOrFail(int $id, int $userId): Collection
    {
        $recurringInvoice = $this->recurringInvoiceRepository->findByIdAndUserId($id, $userId)->first();

        if (! $recurringInvoice) {
            throw DataNotFoundException::withMessage('Recurring invoice not found.');
        }

        return collect($recurringInvoice);
    }

    private function updateRecurringInvoice(int $id): void
    {
        $this->recurringInvoiceDao->setIsDeleted(CommonConstant::IS_DELETED_YES);
        $this->recurringInvoiceRepository->updateById($id, $this->recurringInvoiceDao);
    }
}
