<?php

namespace App\Modules\V1\RecurringInvoice\Services\List;

use App\Http\Services\AuthService;
use App\Models\RecurringInvoice;
use App\Repositories\V1\RecurringInvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private readonly RecurringInvoiceRepository $recurringInvoiceRepository,
        private readonly AuthService $authService,
    ) {}

    public function list(): array
    {
        $userId = $this->authService->getData()->get('userId');
        $recurringInvoices = $this->recurringInvoiceRepository->findByUserId($userId);

        return CommonUtils::successDataResponse($this->formatResponse($recurringInvoices));
    }

    private function formatResponse(Collection $recurringInvoices): array
    {
        return [
            'recurring_invoices' => $recurringInvoices
                ->map(fn (RecurringInvoice $recurringInvoice) => $this->formatRecurringInvoice(collect($recurringInvoice)))
                ->values()
                ->toArray(),
        ];
    }

    private function formatRecurringInvoice(Collection $recurringInvoice): array
    {
        $client = collect($recurringInvoice->get('client'));

        return [
            'id' => $recurringInvoice->get('id'),
            'client_id' => $recurringInvoice->get('client_id'),
            'client_name' => $client->get('name'),
            'frequency' => $recurringInvoice->get('frequency'),
            'status' => $recurringInvoice->get('status'),
            'next_run_at' => $recurringInvoice->get('next_run_at'),
            'last_generated_at' => $recurringInvoice->get('last_generated_at'),
            'total' => $recurringInvoice->get('total'),
        ];
    }
}
