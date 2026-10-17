<?php

namespace App\Modules\V1\Invoice\Services\LineItem\Add;

use App\Constants\InvoiceActivityConstants;
use App\Exceptions\DataNotFoundException;
use App\Http\Requests\V1\Invoice\LineItem\Add\DetailsRequest;
use App\Http\Services\AuthService;
use App\Modules\V1\Invoice\Bo\LineItem\Add\DetailsBo;
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
        return $this->lineItemHelper->prepareAddBo($detailsRequest);
    }

    public function add(DetailsBo $detailsBo): array
    {
        $userId = $this->authService->getData()->get('userId');
        $invoice = $this->findInvoiceOrFail($detailsBo->getInvoiceId(), $userId);

        $lineItemDao = $this->lineItemHelper->prepareAddDao($detailsBo);
        $lineItem = collect($this->invoiceLineItemRepository->insert($lineItemDao));

        $this->recalculateInvoiceTotals($invoice);

        $this->invoiceActivityRepository->insert($this->invoiceActivityHelper->prepareDao(
            $invoice->get('id'),
            $userId,
            InvoiceActivityConstants::TYPE_LINE_ITEM_ADDED,
            'Line item added: '.$lineItem->get('description').'.',
        ));

        return CommonUtils::successDataResponse([
            'line_item' => $this->formatLineItem($lineItem),
        ]);
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
