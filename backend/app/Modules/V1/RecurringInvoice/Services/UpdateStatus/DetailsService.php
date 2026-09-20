<?php

namespace App\Modules\V1\RecurringInvoice\Services\UpdateStatus;

use App\Constants\RecurringInvoiceConstants;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\InvalidDataException;
use App\Http\Services\AuthService;
use App\Modules\V1\RecurringInvoice\Helpers\RecurringInvoiceHelper;
use App\Repositories\V1\RecurringInvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private RecurringInvoiceHelper $recurringInvoiceHelper,
        private readonly RecurringInvoiceRepository $recurringInvoiceRepository,
        private readonly AuthService $authService,
    ) {}

    public function updateStatus(int $id, string $newStatus): array
    {
        $userId = $this->authService->getData()->get('userId');
        $recurringInvoice = $this->findRecurringInvoiceOrFail($id, $userId);
        $oldStatus = $recurringInvoice->get('status');

        $this->validateStatusTransition($oldStatus, $newStatus);

        $recurringInvoiceDao = $this->recurringInvoiceHelper->prepareUpdateStatusDao($newStatus);
        $this->recurringInvoiceRepository->updateById($id, $recurringInvoiceDao);

        return CommonUtils::successResponse('Recurring invoice status updated successfully.');
    }

    private function findRecurringInvoiceOrFail(int $id, int $userId): Collection
    {
        $recurringInvoice = $this->recurringInvoiceRepository->findByIdAndUserId($id, $userId)->first();

        if (! $recurringInvoice) {
            throw DataNotFoundException::withMessage('Recurring invoice not found.');
        }

        return collect($recurringInvoice);
    }

    private function validateStatusTransition(string $oldStatus, string $newStatus): void
    {
        $validTransitions = [
            RecurringInvoiceConstants::STATUS_ACTIVE => [
                RecurringInvoiceConstants::STATUS_PAUSED,
                RecurringInvoiceConstants::STATUS_CANCELLED,
            ],
            RecurringInvoiceConstants::STATUS_PAUSED => [
                RecurringInvoiceConstants::STATUS_ACTIVE,
                RecurringInvoiceConstants::STATUS_CANCELLED,
            ],
        ];

        if ($oldStatus === $newStatus) {
            throw InvalidDataException::withMessage('New status must be different from current status.');
        }

        if ($oldStatus === RecurringInvoiceConstants::STATUS_CANCELLED) {
            throw InvalidDataException::withMessage('Cannot change status of a cancelled recurring invoice.');
        }

        if (! isset($validTransitions[$oldStatus]) || ! in_array($newStatus, $validTransitions[$oldStatus], true)) {
            throw InvalidDataException::withMessage('Invalid status transition from '.$oldStatus.' to '.$newStatus.'.');
        }
    }
}
