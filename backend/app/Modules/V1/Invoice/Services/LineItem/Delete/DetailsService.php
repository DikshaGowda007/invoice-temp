<?php

namespace App\Modules\V1\Invoice\Services\LineItem\Delete;

use App\Constants\CommonConstant;
use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\DAO\V1\InvoiceLineItemDAO;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;

class DetailsService
{
    public function __construct(
        private InvoiceLineItemDAO $invoiceLineItemDao,
        private InvoiceHelper $invoiceHelper,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceLineItemRepository $invoiceLineItemRepository,
        private readonly AuthService $authService,
    ) {}

    public function delete(int $lineItemId): array
    {
        $userId = $this->authService->getData()->get('userId');
        $lineItem = $this->findLineItemOrFail($lineItemId);
        $invoice = $this->findInvoiceOrFail($lineItem->invoice_id, $userId);

        $this->invoiceLineItemDao->setIsDeleted(CommonConstant::IS_DELETED_YES);
        $this->invoiceLineItemRepository->updateById($lineItemId, $this->invoiceLineItemDao);

        $this->recalculateInvoiceTotals($invoice);

        return CommonUtils::successResponse('Line item deleted successfully.');
    }

    private function findLineItemOrFail(int $id): InvoiceLineItem
    {
        $lineItem = $this->invoiceLineItemRepository->findById($id)->first();

        if (! $lineItem) {
            throw DataNotFoundException::withMessage('Line item not found.');
        }

        return $lineItem;
    }

    private function findInvoiceOrFail(int $invoiceId, int $userId): Invoice
    {
        $invoice = $this->invoiceRepository->findByIdAndUserId($invoiceId, $userId)->first();

        if (! $invoice) {
            throw DataNotFoundException::withMessage('Invoice not found.');
        }

        return $invoice;
    }

    private function recalculateInvoiceTotals(Invoice $invoice): void
    {
        $subtotal = (float) $this->invoiceLineItemRepository->findByInvoiceId($invoice->id)->sum('line_total');
        $totals = $this->invoiceHelper->computeTotals($subtotal, (float) $invoice->tax_rate, (float) $invoice->discount_amount);

        $invoiceDao = new InvoiceDAO;
        $invoiceDao->setSubtotal($totals['subtotal']);
        $invoiceDao->setTaxAmount($totals['tax_amount']);
        $invoiceDao->setTotal($totals['total']);

        $this->invoiceRepository->updateById($invoice->id, $invoiceDao);
    }
}
