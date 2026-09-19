<?php

namespace App\Modules\V1\Invoice\Services\Get;

use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Models\Invoice;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceLineItemRepository $invoiceLineItemRepository,
        private readonly AuthService $authService,
    ) {}

    public function get(int $id): array
    {
        $userId = $this->authService->getData()->get('userId');
        $invoice = $this->findInvoiceOrFail($id, $userId);
        $lineItems = $this->invoiceLineItemRepository->findByInvoiceId($id);

        return CommonUtils::successDataResponse([
            'invoice' => $this->formatInvoice(collect($invoice), $lineItems),
        ]);
    }

    private function findInvoiceOrFail(int $id, int $userId): Invoice
    {
        $invoice = $this->invoiceRepository->findByIdAndUserId($id, $userId)->first();

        if (! $invoice) {
            throw DataNotFoundException::withMessage('Invoice not found.');
        }

        return $invoice;
    }

    private function formatInvoice(Collection $invoice, Collection $lineItems): array
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
            'line_items' => $lineItems->map(fn ($lineItem) => [
                'id' => $lineItem->id,
                'description' => $lineItem->description,
                'quantity' => $lineItem->quantity,
                'unit_price' => $lineItem->unit_price,
                'line_total' => $lineItem->line_total,
            ])->values()->toArray(),
        ];
    }
}
