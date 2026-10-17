<?php

namespace App\Modules\V1\Invoice\Services\LineItem\Delete;

use App\Constants\CommonConstant;
use App\Constants\InvoiceActivityConstants;
use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Modules\V1\Invoice\Helpers\InvoiceActivityHelper;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\DAO\V1\InvoiceLineItemDAO;
use App\Repositories\V1\InvoiceActivityRepository;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private InvoiceLineItemDAO $invoiceLineItemDao,
        private InvoiceHelper $invoiceHelper,
        private InvoiceActivityHelper $invoiceActivityHelper,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceLineItemRepository $invoiceLineItemRepository,
        private readonly InvoiceActivityRepository $invoiceActivityRepository,
        private readonly AuthService $authService,
    ) {}

    public function delete(int $lineItemId): array
    {
        $userId = $this->authService->getData()->get('userId');
        $lineItem = $this->findLineItemOrFail($lineItemId);
        $invoice = $this->findInvoiceOrFail($lineItem->get('invoice_id'), $userId);

        $this->invoiceLineItemDao->setIsDeleted(CommonConstant::IS_DELETED_YES);
        $this->invoiceLineItemRepository->updateById($lineItemId, $this->invoiceLineItemDao);

        $this->recalculateInvoiceTotals($invoice);

        $this->invoiceActivityRepository->insert($this->invoiceActivityHelper->prepareDao(
            $invoice->get('id'),
            $userId,
            InvoiceActivityConstants::TYPE_LINE_ITEM_DELETED,
            'Line item removed: '.$lineItem->get('description').'.',
        ));

        return CommonUtils::successResponse('Line item deleted successfully.');
    }

    private function findLineItemOrFail(int $id): Collection
    {
        $lineItem = $this->invoiceLineItemRepository->findById($id)->first();

        if (! $lineItem) {
            throw DataNotFoundException::withMessage('Line item not found.');
        }

        return collect($lineItem);
    }

    private function findInvoiceOrFail(int $invoiceId, int $userId): Collection
    {
        $invoice = $this->invoiceRepository->findByIdAndUserId($invoiceId, $userId)->first();

        if (! $invoice) {
            throw DataNotFoundException::withMessage('Invoice not found.');
        }

        return collect($invoice);
    }

    private function recalculateInvoiceTotals(Collection $invoice): void
    {
        $subtotal = (float) $this->invoiceLineItemRepository->findByInvoiceId($invoice->get('id'))->sum('line_total');
        $totals = $this->invoiceHelper->computeTotals($subtotal, (float) $invoice->get('tax_rate'), (float) $invoice->get('discount_amount'));

        $invoiceDao = new InvoiceDAO;
        $invoiceDao->setSubtotal($totals['subtotal']);
        $invoiceDao->setTaxAmount($totals['tax_amount']);
        $invoiceDao->setTotal($totals['total']);

        $this->invoiceRepository->updateById($invoice->get('id'), $invoiceDao);
    }
}
