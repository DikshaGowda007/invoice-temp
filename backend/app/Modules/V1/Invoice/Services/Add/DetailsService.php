<?php

namespace App\Modules\V1\Invoice\Services\Add;

use App\Constants\InvoiceConstants;
use App\Http\Requests\V1\Invoice\Add\DetailsRequest;
use App\Http\Services\AuthService;
use App\Modules\V1\Invoice\Bo\Add\DetailsBo;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private InvoiceHelper $invoiceHelper,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly AuthService $authService,
    ) {}

    public function prepareBo(DetailsRequest $detailsRequest): DetailsBo
    {
        return $this->invoiceHelper->prepareAddBo($detailsRequest);
    }

    public function add(DetailsBo $detailsBo): array
    {
        $userId = $this->authService->getData()->get('userId');
        $invoiceNumber = $this->invoiceHelper->generateInvoiceNumber($userId);

        $invoiceDao = $this->invoiceHelper->prepareAddDao($detailsBo, $userId, $invoiceNumber);
        $invoiceDao->setStatus(InvoiceConstants::STATUS_DRAFT);
        $invoiceDao->setSubtotal(0);
        $invoiceDao->setTaxAmount(0);
        $invoiceDao->setTotal(0);

        $invoice = $this->invoiceRepository->insert($invoiceDao);

        return CommonUtils::successDataResponse([
            'invoice' => $this->formatInvoice(collect($invoice)),
        ]);
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
