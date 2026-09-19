<?php

namespace App\Modules\V1\Invoice\Services\LineItem\Update;

use App\Exceptions\DataNotFoundException;
use App\Http\Requests\V1\Invoice\LineItem\Update\DetailsRequest;
use App\Http\Services\AuthService;
use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use App\Modules\V1\Invoice\Bo\LineItem\Update\DetailsBo;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use App\Modules\V1\Invoice\Helpers\LineItemHelper;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private LineItemHelper $lineItemHelper,
        private InvoiceHelper $invoiceHelper,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceLineItemRepository $invoiceLineItemRepository,
        private readonly AuthService $authService,
    ) {}

    public function prepareBo(DetailsRequest $detailsRequest): DetailsBo
    {
        return $this->lineItemHelper->prepareUpdateBo($detailsRequest);
    }

    public function update(DetailsBo $detailsBo): array
    {
        $userId = $this->authService->getData()->get('userId');
        $lineItem = $this->findLineItemOrFail($detailsBo->getLineItemId());
        $invoice = $this->findInvoiceOrFail($lineItem->invoice_id, $userId);

        $lineItemDao = $this->lineItemHelper->prepareUpdateDao($detailsBo);
        $this->invoiceLineItemRepository->updateById($detailsBo->getLineItemId(), $lineItemDao);

        $this->recalculateInvoiceTotals($invoice);
        $updatedLineItem = $this->findLineItemOrFail($detailsBo->getLineItemId());

        return CommonUtils::successDataResponse([
            'line_item' => $this->formatLineItem(collect($updatedLineItem)),
        ]);
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

    private function formatLineItem(Collection $lineItem): array
    {
        return [
            'id' => $lineItem->get('id'),
            'invoice_id' => $lineItem->get('invoice_id'),
            'description' => $lineItem->get('description'),
            'quantity' => $lineItem->get('quantity'),
            'unit_price' => $lineItem->get('unit_price'),
            'line_total' => $lineItem->get('line_total'),
        ];
    }
}
