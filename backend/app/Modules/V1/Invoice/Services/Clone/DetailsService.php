<?php

namespace App\Modules\V1\Invoice\Services\Clone;

use App\Constants\InvoiceConstants;
use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Models\Invoice;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\DAO\V1\InvoiceLineItemDAO;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private InvoiceHelper $invoiceHelper,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceLineItemRepository $invoiceLineItemRepository,
        private readonly AuthService $authService,
    ) {}

    public function clone(int $id): array
    {
        $userId = $this->authService->getData()->get('userId');
        $original = $this->findInvoiceOrFail($id, $userId);
        $originalLineItems = $this->invoiceLineItemRepository->findByInvoiceId($id);

        $clonedInvoice = $this->cloneInvoice($original, $userId);
        $this->cloneLineItems($originalLineItems, $clonedInvoice->id);

        return CommonUtils::successDataResponse([
            'invoice' => $this->formatInvoice(collect($clonedInvoice)),
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

    private function cloneInvoice(Invoice $original, int $userId): Invoice
    {
        $invoiceDao = new InvoiceDAO;
        $invoiceDao->setUserId($userId);
        $invoiceDao->setClientId($original->client_id);
        $invoiceDao->setInvoiceNumber($this->invoiceHelper->generateInvoiceNumber($userId));
        $invoiceDao->setIssueDate(now()->toDateString());
        $invoiceDao->setTaxRate((float) $original->tax_rate);
        $invoiceDao->setDiscountAmount((float) $original->discount_amount);
        $invoiceDao->setNotes($original->notes);
        $invoiceDao->setStatus(InvoiceConstants::STATUS_DRAFT);
        $invoiceDao->setSubtotal((float) $original->subtotal);
        $invoiceDao->setTaxAmount((float) $original->tax_amount);
        $invoiceDao->setTotal((float) $original->total);

        return $this->invoiceRepository->insert($invoiceDao);
    }

    private function cloneLineItems(Collection $originalLineItems, int $newInvoiceId): void
    {
        foreach ($originalLineItems as $lineItem) {
            $lineItemDao = new InvoiceLineItemDAO;
            $lineItemDao->setInvoiceId($newInvoiceId);
            $lineItemDao->setDescription($lineItem->description);
            $lineItemDao->setQuantity((float) $lineItem->quantity);
            $lineItemDao->setUnitPrice((float) $lineItem->unit_price);
            $lineItemDao->setLineTotal((float) $lineItem->line_total);

            $this->invoiceLineItemRepository->insert($lineItemDao);
        }
    }

    private function formatInvoice(Collection $invoice): array
    {
        return [
            'id' => $invoice->get('id'),
            'client_id' => $invoice->get('client_id'),
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
        ];
    }
}
