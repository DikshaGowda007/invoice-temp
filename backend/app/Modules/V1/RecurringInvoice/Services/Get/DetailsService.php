<?php

namespace App\Modules\V1\RecurringInvoice\Services\Get;

use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Repositories\V1\InvoiceRepository;
use App\Repositories\V1\RecurringInvoiceLineItemRepository;
use App\Repositories\V1\RecurringInvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private readonly RecurringInvoiceRepository $recurringInvoiceRepository,
        private readonly RecurringInvoiceLineItemRepository $recurringInvoiceLineItemRepository,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly AuthService $authService,
    ) {}

    public function get(int $id): array
    {
        $userId = $this->authService->getData()->get('userId');
        $recurringInvoice = $this->findRecurringInvoiceOrFail($id, $userId);
        $lineItems = $this->findLineItems($id);
        $generatedInvoices = $this->findGeneratedInvoices($id, $userId);

        return CommonUtils::successDataResponse($this->formatResponse($recurringInvoice, $lineItems, $generatedInvoices));
    }

    private function findRecurringInvoiceOrFail(int $id, int $userId): Collection
    {
        $recurringInvoice = $this->recurringInvoiceRepository->findByIdAndUserId($id, $userId)->first();

        if (! $recurringInvoice) {
            throw DataNotFoundException::withMessage('Recurring invoice not found.');
        }

        return collect($recurringInvoice);
    }

    private function findLineItems(int $id): Collection
    {
        return $this->recurringInvoiceLineItemRepository->findByRecurringInvoiceId($id);
    }

    private function findGeneratedInvoices(int $id, int $userId): Collection
    {
        return $this->invoiceRepository->findByRecurringInvoiceIdAndUserId($id, $userId);
    }

    private function formatResponse(Collection $recurringInvoice, Collection $lineItems, Collection $generatedInvoices): array
    {
        return [
            'recurring_invoice' => $this->formatRecurringInvoice($recurringInvoice, $lineItems, $generatedInvoices),
        ];
    }

    private function formatRecurringInvoice(Collection $recurringInvoice, Collection $lineItems, Collection $generatedInvoices): array
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
            'subtotal' => $recurringInvoice->get('subtotal'),
            'tax_rate' => $recurringInvoice->get('tax_rate'),
            'tax_amount' => $recurringInvoice->get('tax_amount'),
            'discount_amount' => $recurringInvoice->get('discount_amount'),
            'total' => $recurringInvoice->get('total'),
            'notes' => $recurringInvoice->get('notes'),
            'line_items' => $lineItems->map(fn ($lineItem) => [
                'id' => $lineItem->id,
                'description' => $lineItem->description,
                'quantity' => $lineItem->quantity,
                'unit_price' => $lineItem->unit_price,
                'line_total' => $lineItem->line_total,
            ])->values()->toArray(),
            'generated_invoices' => $generatedInvoices->sortByDesc('id')->map(fn ($invoice) => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'issue_date' => $invoice->issue_date,
                'total' => $invoice->total,
            ])->values()->toArray(),
        ];
    }
}
