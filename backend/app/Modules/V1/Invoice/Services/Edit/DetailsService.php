<?php

namespace App\Modules\V1\Invoice\Services\Edit;

use App\Exceptions\DataNotFoundException;
use App\Http\Requests\V1\Invoice\Edit\DetailsRequest;
use App\Http\Services\AuthService;
use App\Models\Invoice;
use App\Modules\V1\Invoice\Bo\Edit\DetailsBo;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
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

    public function prepareBo(DetailsRequest $detailsRequest): DetailsBo
    {
        return $this->invoiceHelper->prepareEditBo($detailsRequest);
    }

    public function edit(DetailsBo $detailsBo): array
    {
        $userId = $this->authService->getData()->get('userId');
        $id = $detailsBo->getId();

        $this->findInvoiceOrFail($id, $userId);

        $invoiceDao = $this->invoiceHelper->prepareEditDao($detailsBo);
        $subtotal = (float) $this->invoiceLineItemRepository->findByInvoiceId($id)->sum('line_total');
        $totals = $this->invoiceHelper->computeTotals($subtotal, $detailsBo->getTaxRate() ?? 0.0, $detailsBo->getDiscountAmount() ?? 0.0);
        $invoiceDao->setSubtotal($totals['subtotal']);
        $invoiceDao->setTaxAmount($totals['tax_amount']);
        $invoiceDao->setTotal($totals['total']);

        $this->invoiceRepository->updateById($id, $invoiceDao);
        $invoice = $this->findInvoiceOrFail($id, $userId);

        return CommonUtils::successDataResponse([
            'invoice' => $this->formatInvoice(collect($invoice)),
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
