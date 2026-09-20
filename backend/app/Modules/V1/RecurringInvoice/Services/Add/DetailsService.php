<?php

namespace App\Modules\V1\RecurringInvoice\Services\Add;

use App\Http\Requests\V1\RecurringInvoice\Add\DetailsRequest;
use App\Http\Services\AuthService;
use App\Modules\V1\RecurringInvoice\Bo\Add\DetailsBo;
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

    public function prepareBo(DetailsRequest $detailsRequest): DetailsBo
    {
        return $this->recurringInvoiceHelper->prepareAddBo($detailsRequest);
    }

    public function add(DetailsBo $detailsBo): array
    {
        $userId = $this->authService->getData()->get('userId');

        $recurringInvoiceDao = $this->recurringInvoiceHelper->prepareAddDao($detailsBo, $userId);
        $recurringInvoice = $this->recurringInvoiceRepository->insert($recurringInvoiceDao);

        return CommonUtils::successDataResponse($this->formatResponse(collect($recurringInvoice)));
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
