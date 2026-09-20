<?php

namespace App\Modules\V1\RecurringInvoice\Services\Edit;

use App\Constants\RecurringInvoiceConstants;
use App\Exceptions\DataNotFoundException;
use App\Http\Requests\V1\RecurringInvoice\Edit\DetailsRequest;
use App\Http\Services\AuthService;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use App\Modules\V1\RecurringInvoice\Bo\Edit\DetailsBo;
use App\Modules\V1\RecurringInvoice\Helpers\RecurringInvoiceHelper;
use App\Repositories\DAO\V1\RecurringInvoiceDAO;
use App\Repositories\V1\RecurringInvoiceLineItemRepository;
use App\Repositories\V1\RecurringInvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private RecurringInvoiceHelper $recurringInvoiceHelper,
        private InvoiceHelper $invoiceHelper,
        private readonly RecurringInvoiceRepository $recurringInvoiceRepository,
        private readonly RecurringInvoiceLineItemRepository $recurringInvoiceLineItemRepository,
        private readonly AuthService $authService,
    ) {}

    public function prepareBo(DetailsRequest $detailsRequest): DetailsBo
    {
        return $this->recurringInvoiceHelper->prepareEditBo($detailsRequest);
    }

    public function edit(DetailsBo $detailsBo): array
    {
        $userId = $this->authService->getData()->get('userId');
        $id = $detailsBo->getId();

        $this->findRecurringInvoiceOrFail($id, $userId);

        $this->recurringInvoiceRepository->updateById($id, $this->prepareEditDaoWithTotals($detailsBo));
        $recurringInvoice = $this->findRecurringInvoiceOrFail($id, $userId);

        return CommonUtils::successDataResponse($this->formatResponse($recurringInvoice));
    }

    private function findRecurringInvoiceOrFail(int $id, int $userId): Collection
    {
        $recurringInvoice = $this->recurringInvoiceRepository->findByIdAndUserId($id, $userId)->first();

        if (! $recurringInvoice) {
            throw DataNotFoundException::withMessage('Recurring invoice not found.');
        }

        return collect($recurringInvoice);
    }

    private function prepareEditDaoWithTotals(DetailsBo $detailsBo): RecurringInvoiceDAO
    {
        $recurringInvoiceDao = $this->recurringInvoiceHelper->prepareEditDao($detailsBo);
        $totals = $this->calculateTotals($detailsBo);

        $recurringInvoiceDao->setSubtotal($totals['subtotal']);
        $recurringInvoiceDao->setTaxAmount($totals['tax_amount']);
        $recurringInvoiceDao->setTotal($totals['total']);

        return $recurringInvoiceDao;
    }

    private function calculateTotals(DetailsBo $detailsBo): array
    {
        $subtotal = (float) $this->recurringInvoiceLineItemRepository->findByRecurringInvoiceId($detailsBo->getId())->sum('line_total');

        return $this->invoiceHelper->computeTotals(
            $subtotal,
            $detailsBo->getTaxRate() ?? RecurringInvoiceConstants::DEFAULT_AMOUNT,
            $detailsBo->getDiscountAmount() ?? RecurringInvoiceConstants::DEFAULT_AMOUNT,
        );
    }

    private function formatResponse(Collection $recurringInvoice): array
    {
        return [
            'recurring_invoice' => $this->formatRecurringInvoice($recurringInvoice),
        ];
    }

    private function formatRecurringInvoice(Collection $recurringInvoice): array
    {
        return [
            'id' => $recurringInvoice->get('id'),
            'client_id' => $recurringInvoice->get('client_id'),
            'frequency' => $recurringInvoice->get('frequency'),
            'status' => $recurringInvoice->get('status'),
            'next_run_at' => $recurringInvoice->get('next_run_at'),
            'last_generated_at' => $recurringInvoice->get('last_generated_at'),
            'subtotal' => $recurringInvoice->get('subtotal'),
            'tax_rate' => $recurringInvoice->get('tax_rate'),
            'tax_amount' => $recurringInvoice->get('tax_amount'),
            'discount_amount' => $recurringInvoice->get('discount_amount'),
            'total' => $recurringInvoice->get('total'),
            'notes' => $recurringInvoice->get('notes'),
        ];
    }
}
