<?php

namespace App\Modules\V1\Invoice\Services\WebhookMarkPaid;

use App\Constants\InvoiceConstants;
use App\Events\InvoicePaid;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\DAO\V1\InvoiceStatusHistoryDAO;
use App\Repositories\V1\InvoiceRepository;
use App\Repositories\V1\InvoiceStatusHistoryRepository;
use Illuminate\Support\Facades\Log;

class DetailsService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceStatusHistoryRepository $invoiceStatusHistoryRepository,
    ) {}

    public function markPaidFromOrderId(string $razorpayOrderId, string $razorpayPaymentId): void
    {
        $invoice = $this->invoiceRepository->findByRazorpayOrderId($razorpayOrderId)->first();

        if (! $invoice) {
            Log::warning('razorpay.webhook.invoice_not_found', ['razorpay_order_id' => $razorpayOrderId]);

            return;
        }

        $invoice = collect($invoice);
        $currentStatus = $invoice->get('status');

        if ($currentStatus === InvoiceConstants::STATUS_PAID) {
            Log::info('razorpay.webhook.duplicate', ['razorpay_order_id' => $razorpayOrderId]);

            return;
        }

        if (! in_array($currentStatus, [InvoiceConstants::STATUS_SENT, InvoiceConstants::STATUS_OVERDUE], true)) {
            Log::warning('razorpay.webhook.unexpected_status', [
                'razorpay_order_id' => $razorpayOrderId,
                'status' => $currentStatus,
            ]);

            return;
        }

        $id = (int) $invoice->get('id');
        $userId = (int) $invoice->get('user_id');

        $invoiceDao = new InvoiceDAO;
        $invoiceDao->setStatus(InvoiceConstants::STATUS_PAID);
        $invoiceDao->setRazorpayPaymentId($razorpayPaymentId);
        $this->invoiceRepository->updateById($id, $invoiceDao);

        $historyDao = new InvoiceStatusHistoryDAO;
        $historyDao->setInvoiceId($id);
        $historyDao->setPreviousStatus($currentStatus);
        $historyDao->setNewStatus(InvoiceConstants::STATUS_PAID);
        $historyDao->setNotes('Marked paid via Razorpay payment '.$razorpayPaymentId.'.');
        $this->invoiceStatusHistoryRepository->insert($historyDao);

        InvoicePaid::dispatch($id, $userId);
    }
}
