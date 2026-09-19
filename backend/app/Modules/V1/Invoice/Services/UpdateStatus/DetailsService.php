<?php

namespace App\Modules\V1\Invoice\Services\UpdateStatus;

use App\Constants\InvoiceConstants;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\InvalidDataException;
use App\Http\Requests\V1\Invoice\UpdateStatus\DetailsRequest;
use App\Http\Services\AuthService;
use App\Models\Invoice;
use App\Modules\V1\Invoice\Bo\UpdateStatus\DetailsBo;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use App\Repositories\DAO\V1\InvoiceStatusHistoryDAO;
use App\Repositories\V1\InvoiceRepository;
use App\Repositories\V1\InvoiceStatusHistoryRepository;
use App\Utils\CommonUtils;

class DetailsService
{
    private string $oldStatus = '';

    public function __construct(
        private InvoiceHelper $invoiceHelper,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceStatusHistoryRepository $invoiceStatusHistoryRepository,
        private readonly AuthService $authService,
    ) {}

    public function prepareBo(DetailsRequest $detailsRequest): DetailsBo
    {
        return $this->invoiceHelper->prepareUpdateStatusBo($detailsRequest);
    }

    public function updateStatus(DetailsBo $detailsBo): array
    {
        $userId = $this->authService->getData()->get('userId');
        $id = $detailsBo->getId();

        $invoice = $this->findInvoiceOrFail($id, $userId);
        $this->oldStatus = $invoice->status;

        $this->validateStatusTransition($detailsBo->getStatus());
        $this->updateInvoiceStatus($id, $detailsBo->getStatus());
        $this->logStatusHistory($id, $detailsBo->getStatus(), $detailsBo->getNotes(), $userId);

        return CommonUtils::successResponse('Invoice status updated successfully.');
    }

    private function findInvoiceOrFail(int $id, int $userId): Invoice
    {
        $invoice = $this->invoiceRepository->findByIdAndUserId($id, $userId)->first();

        if (! $invoice) {
            throw DataNotFoundException::withMessage('Invoice not found.');
        }

        return $invoice;
    }

    private function validateStatusTransition(string $newStatus): void
    {
        $validTransitions = [
            InvoiceConstants::STATUS_DRAFT => [
                InvoiceConstants::STATUS_SENT,
                InvoiceConstants::STATUS_CANCELLED,
            ],
            InvoiceConstants::STATUS_SENT => [
                InvoiceConstants::STATUS_PAID,
                InvoiceConstants::STATUS_OVERDUE,
                InvoiceConstants::STATUS_CANCELLED,
            ],
            InvoiceConstants::STATUS_OVERDUE => [
                InvoiceConstants::STATUS_PAID,
                InvoiceConstants::STATUS_CANCELLED,
            ],
        ];

        if ($this->oldStatus === $newStatus) {
            throw InvalidDataException::withMessage('New status must be different from current status.');
        }

        if ($this->oldStatus === InvoiceConstants::STATUS_PAID || $this->oldStatus === InvoiceConstants::STATUS_CANCELLED) {
            throw InvalidDataException::withMessage('Cannot change status of a finalized invoice.');
        }

        if (! isset($validTransitions[$this->oldStatus]) || ! in_array($newStatus, $validTransitions[$this->oldStatus], true)) {
            throw InvalidDataException::withMessage('Invalid status transition from '.$this->oldStatus.' to '.$newStatus.'.');
        }
    }

    private function updateInvoiceStatus(int $id, string $status): void
    {
        $invoiceDao = $this->invoiceHelper->prepareUpdateStatusDao($status);
        $this->invoiceRepository->updateById($id, $invoiceDao);
    }

    private function logStatusHistory(int $invoiceId, string $newStatus, ?string $notes, int $changedBy): void
    {
        $historyDao = new InvoiceStatusHistoryDAO;
        $historyDao->setInvoiceId($invoiceId);
        $historyDao->setPreviousStatus($this->oldStatus);
        $historyDao->setNewStatus($newStatus);
        $historyDao->setChangedBy($changedBy);
        $historyDao->setNotes($notes ?? 'Status updated from '.$this->oldStatus.' to '.$newStatus.'.');

        $this->invoiceStatusHistoryRepository->insert($historyDao);
    }
}
