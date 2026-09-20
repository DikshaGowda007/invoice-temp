<?php

namespace App\Modules\V1\RecurringInvoice\Services\LineItem\Add;

use App\Exceptions\DataNotFoundException;
use App\Http\Requests\V1\RecurringInvoice\LineItem\Add\DetailsRequest;
use App\Http\Services\AuthService;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use App\Modules\V1\RecurringInvoice\Bo\LineItem\Add\DetailsBo;
use App\Modules\V1\RecurringInvoice\Helpers\RecurringLineItemHelper;
use App\Repositories\DAO\V1\RecurringInvoiceDAO;
use App\Repositories\V1\RecurringInvoiceLineItemRepository;
use App\Repositories\V1\RecurringInvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private RecurringLineItemHelper $lineItemHelper,
        private InvoiceHelper $invoiceHelper,
        private readonly RecurringInvoiceRepository $recurringInvoiceRepository,
        private readonly RecurringInvoiceLineItemRepository $recurringInvoiceLineItemRepository,
        private readonly AuthService $authService,
    ) {}

    public function prepareBo(DetailsRequest $detailsRequest): DetailsBo
    {
        return $this->lineItemHelper->prepareAddBo($detailsRequest);
    }

    public function add(DetailsBo $detailsBo): array
    {
        $userId = $this->authService->getData()->get('userId');
        $recurringInvoice = $this->findRecurringInvoiceOrFail($detailsBo->getRecurringInvoiceId(), $userId);

        $lineItemDao = $this->lineItemHelper->prepareAddDao($detailsBo);
        $lineItem = $this->recurringInvoiceLineItemRepository->insert($lineItemDao);

        $this->recalculateTotals($recurringInvoice);

        return CommonUtils::successDataResponse($this->formatResponse(collect($lineItem)));
    }

    private function formatResponse(Collection $lineItem): array
    {
        return [
            'line_item' => $this->formatLineItem($lineItem),
        ];
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

    private function formatLineItem(Collection $lineItem): array
    {
        return [
            'id' => $lineItem->get('id'),
            'recurring_invoice_id' => $lineItem->get('recurring_invoice_id'),
            'description' => $lineItem->get('description'),
            'quantity' => $lineItem->get('quantity'),
            'unit_price' => $lineItem->get('unit_price'),
            'line_total' => $lineItem->get('line_total'),
        ];
    }
}
