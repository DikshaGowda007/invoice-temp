<?php

namespace App\Modules\V1\Invoice\Services\PublicGet;

use App\Exceptions\DataNotFoundException;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceLineItemRepository $invoiceLineItemRepository,
    ) {}

    public function get(string $token): array
    {
        $invoice = $this->findInvoiceOrFail($token);
        $lineItems = $this->invoiceLineItemRepository->findByInvoiceId($invoice->get('id'));

        return CommonUtils::successDataResponse([
            'invoice' => $this->formatInvoice($invoice, $lineItems),
        ]);
    }

    private function findInvoiceOrFail(string $token): Collection
    {
        $invoice = $this->invoiceRepository->findByPublicToken($token)->first();

        if (! $invoice || $this->isExpired($invoice)) {
            throw DataNotFoundException::withMessage('This payment link is invalid or has expired.');
        }

        return collect($invoice);
    }

    private function isExpired(object $invoice): bool
    {
        if (! $invoice->public_token_expires_at) {
            return false;
        }

        return Carbon::parse($invoice->public_token_expires_at)->isPast();
    }

    private function formatInvoice(Collection $invoice, Collection $lineItems): array
    {
        $client = collect($invoice->get('client'));

        return [
            'invoice_number' => $invoice->get('invoice_number'),
            'client_name' => $client->get('name'),
            'status' => $invoice->get('status'),
            'issue_date' => $invoice->get('issue_date'),
            'due_date' => $invoice->get('due_date'),
            'subtotal' => $invoice->get('subtotal'),
            'tax_rate' => $invoice->get('tax_rate'),
            'tax_amount' => $invoice->get('tax_amount'),
            'discount_amount' => $invoice->get('discount_amount'),
            'total' => $invoice->get('total'),
            'line_items' => $lineItems->map(fn ($lineItem) => [
                'description' => $lineItem->description,
                'quantity' => $lineItem->quantity,
                'unit_price' => $lineItem->unit_price,
                'line_total' => $lineItem->line_total,
            ])->values()->toArray(),
        ];
    }
}
