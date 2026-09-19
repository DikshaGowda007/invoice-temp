<?php

namespace App\Modules\V1\Invoice\Services\List;

use App\Http\Services\AuthService;
use App\Models\Invoice;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly AuthService $authService,
    ) {}

    public function list(): array
    {
        $userId = $this->authService->getData()->get('userId');
        $invoices = $this->invoiceRepository->findByUserId($userId);

        return CommonUtils::successDataResponse([
            'invoices' => $invoices->map(fn (Invoice $invoice) => $this->formatInvoice(collect($invoice)))->values()->toArray(),
        ]);
    }

    private function formatInvoice(Collection $invoice): array
    {
        $client = collect($invoice->get('client'));

        return [
            'id' => $invoice->get('id'),
            'client_id' => $invoice->get('client_id'),
            'client_name' => $client->get('name'),
            'invoice_number' => $invoice->get('invoice_number'),
            'status' => $invoice->get('status'),
            'issue_date' => $invoice->get('issue_date'),
            'due_date' => $invoice->get('due_date'),
            'total' => $invoice->get('total'),
        ];
    }
}
