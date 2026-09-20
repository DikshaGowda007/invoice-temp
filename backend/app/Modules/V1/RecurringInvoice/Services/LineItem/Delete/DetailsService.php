<?php

namespace App\Modules\V1\RecurringInvoice\Services\LineItem\Delete;

use App\Constants\CommonConstant;
use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use App\Repositories\DAO\V1\RecurringInvoiceDAO;
use App\Repositories\DAO\V1\RecurringInvoiceLineItemDAO;
use App\Repositories\V1\RecurringInvoiceLineItemRepository;
use App\Repositories\V1\RecurringInvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private RecurringInvoiceLineItemDAO $lineItemDao,
        private InvoiceHelper $invoiceHelper,
        private readonly RecurringInvoiceRepository $recurringInvoiceRepository,
        private readonly RecurringInvoiceLineItemRepository $recurringInvoiceLineItemRepository,
        private readonly AuthService $authService,
    ) {}

    public function delete(int $lineItemId): array
    {
        $userId = $this->authService->getData()->get('userId');
        $lineItem = $this->findLineItemOrFail($lineItemId);
        $recurringInvoice = $this->findRecurringInvoiceOrFail($lineItem->get('recurring_invoice_id'), $userId);

        $this->lineItemDao->setIsDeleted(CommonConstant::IS_DELETED_YES);
        $this->recurringInvoiceLineItemRepository->updateById($lineItemId, $this->lineItemDao);

        $this->recalculateTotals($recurringInvoice);

        return CommonUtils::successResponse('Line item deleted successfully.');
    }

    private function findLineItemOrFail(int $id): Collection
    {
        $lineItem = $this->recurringInvoiceLineItemRepository->findById($id)->first();

        if (! $lineItem) {
            throw DataNotFoundException::withMessage('Line item not found.');
        }

        return collect($lineItem);
    }

    private function findRecurringInvoiceOrFail(int $recurringInvoiceId, int $userId): Collection
    {
        $recurringInvoice = $this->recurringInvoiceRepository->findByIdAndUserId($recurringInvoiceId, $userId)->first();

        if (! $recurringInvoice) {
            throw DataNotFoundException::withMessage('Recurring invoice not found.');
        }

        return collect($recurringInvoice);
    }

    private function recalculateTotals(Collection $recurringInvoice): void
    {
        $subtotal = (float) $this->recurringInvoiceLineItemRepository->findByRecurringInvoiceId($recurringInvoice->get('id'))->sum('line_total');
        $totals = $this->invoiceHelper->computeTotals($subtotal, (float) $recurringInvoice->get('tax_rate'), (float) $recurringInvoice->get('discount_amount'));

        $this->recurringInvoiceRepository->updateById($recurringInvoice->get('id'), $this->prepareTotalsDao($totals));
    }

    private function prepareTotalsDao(array $totals): RecurringInvoiceDAO
    {
        $recurringInvoiceDao = new RecurringInvoiceDAO;
        $recurringInvoiceDao->setSubtotal($totals['subtotal']);
        $recurringInvoiceDao->setTaxAmount($totals['tax_amount']);
        $recurringInvoiceDao->setTotal($totals['total']);

        return $recurringInvoiceDao;
    }
}
