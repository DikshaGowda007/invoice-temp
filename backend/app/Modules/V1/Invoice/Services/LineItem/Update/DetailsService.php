<?php

namespace App\Modules\V1\Invoice\Services\LineItem\Update;

use App\Constants\InvoiceActivityConstants;
use App\Exceptions\DataNotFoundException;
use App\Http\Requests\V1\Invoice\LineItem\Update\DetailsRequest;
use App\Http\Services\AuthService;
use App\Modules\V1\Invoice\Bo\LineItem\Update\DetailsBo;
use App\Modules\V1\Invoice\Helpers\InvoiceActivityHelper;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use App\Modules\V1\Invoice\Helpers\LineItemHelper;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\V1\InvoiceActivityRepository;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private LineItemHelper $lineItemHelper,
        private InvoiceHelper $invoiceHelper,
        private InvoiceActivityHelper $invoiceActivityHelper,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceLineItemRepository $invoiceLineItemRepository,
        private readonly InvoiceActivityRepository $invoiceActivityRepository,
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
        $invoice = $this->findInvoiceOrFail($lineItem->get('invoice_id'), $userId);

        $lineItemDao = $this->lineItemHelper->prepareUpdateDao($detailsBo);
        $this->invoiceLineItemRepository->updateById($detailsBo->getLineItemId(), $lineItemDao);

        $this->recalculateInvoiceTotals($invoice);
        $updatedLineItem = $this->findLineItemOrFail($detailsBo->getLineItemId());

        $this->invoiceActivityRepository->insert($this->invoiceActivityHelper->prepareDao(
            $invoice->get('id'),
            $userId,
            InvoiceActivityConstants::TYPE_LINE_ITEM_UPDATED,
            'Line item updated: '.$updatedLineItem->get('description').'.',
        ));

        return CommonUtils::successDataResponse([
            'line_item' => $this->formatLineItem($updatedLineItem),
        ]);
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
