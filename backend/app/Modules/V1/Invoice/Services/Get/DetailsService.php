<?php

namespace App\Modules\V1\Invoice\Services\Get;

use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Repositories\V1\InvoiceActivityRepository;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use App\Repositories\V1\InvoiceStatusHistoryRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceLineItemRepository $invoiceLineItemRepository,
        private readonly InvoiceStatusHistoryRepository $invoiceStatusHistoryRepository,
        private readonly InvoiceActivityRepository $invoiceActivityRepository,
        private readonly AuthService $authService,
    ) {}

    public function get(int $id): array
    {
        $userId = $this->authService->getData()->get('userId');
        $invoice = $this->findInvoiceOrFail($id, $userId);
        $lineItems = $this->invoiceLineItemRepository->findByInvoiceId($id);
        $statusHistory = $this->findStatusHistory($id);
        $activities = $this->findActivities($id);

        return CommonUtils::successDataResponse([
            'invoice' => $this->formatInvoice($invoice, $lineItems, $statusHistory, $activities),
        ]);
    }

    private function findInvoiceOrFail(int $id, int $userId): Collection
    {
        $invoice = $this->invoiceRepository->findByIdAndUserId($id, $userId)->first();

        if (! $invoice) {
            throw DataNotFoundException::withMessage('Invoice not found.');
        }

        return collect($invoice);
    }

    private function findStatusHistory(int $id): Collection
    {
        return $this->invoiceStatusHistoryRepository->findByInvoiceId($id);
    }

    private function findActivities(int $id): Collection
    {
        return $this->invoiceActivityRepository->findByInvoiceId($id);
    }

    private function formatInvoice(Collection $invoice, Collection $lineItems, Collection $statusHistory, Collection $activities): array
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
            'subtotal' => $invoice->get('subtotal'),
            'tax_rate' => $invoice->get('tax_rate'),
            'tax_amount' => $invoice->get('tax_amount'),
            'discount_amount' => $invoice->get('discount_amount'),
            'total' => $invoice->get('total'),
            'notes' => $invoice->get('notes'),
            'public_token' => $invoice->get('public_token'),
            'line_items' => $lineItems->map(fn ($lineItem) => [
                'id' => $lineItem->id,
                'description' => $lineItem->description,
                'quantity' => $lineItem->quantity,
                'unit_price' => $lineItem->unit_price,
                'line_total' => $lineItem->line_total,
            ])->values()->toArray(),
            'activity' => $this->formatActivity($statusHistory, $activities),
        ];
    }

    private function formatActivity(Collection $statusHistory, Collection $activities): array
    {
        $historyEntries = $statusHistory->map(fn ($entry) => [
            'type' => 'STATUS_CHANGED',
            'description' => $entry->notes,
            'created_at' => $entry->created_at,
        ]);

        $activityEntries = $activities->map(fn ($entry) => [
            'type' => $entry->type,
            'description' => $entry->description,
            'created_at' => $entry->created_at,
        ]);

        return $historyEntries->concat($activityEntries)
            ->sortByDesc('created_at')
            ->values()
            ->toArray();
    }
}
