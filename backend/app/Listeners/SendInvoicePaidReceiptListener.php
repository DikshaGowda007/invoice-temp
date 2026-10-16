<?php

namespace App\Listeners;

use App\Constants\InvoiceActivityConstants;
use App\Events\InvoicePaid;
use App\Mail\InvoiceReceiptMail;
use App\Modules\V1\Invoice\Helpers\InvoiceActivityHelper;
use App\Repositories\V1\InvoiceActivityRepository;
use App\Repositories\V1\InvoiceRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendInvoicePaidReceiptListener implements ShouldQueue
{
    public function __construct(
        private InvoiceActivityHelper $invoiceActivityHelper,
        private readonly InvoiceActivityRepository $invoiceActivityRepository,
        private readonly InvoiceRepository $invoiceRepository,
    ) {}

    public function handle(InvoicePaid $event): void
    {
        $invoice = $this->invoiceRepository->findByIdAndUserId($event->invoiceId, $event->userId)->first();

        if (! $invoice || ! $invoice->client?->email) {
            Log::warning('Invoice receipt not sent — client has no email on file.', [
                'invoice_id' => $event->invoiceId,
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_RECEIPT_FAILED, 'Receipt not sent — client has no email on file.');

            return;
        }

        try {
            Mail::to($invoice->client->email)->send(new InvoiceReceiptMail($invoice, $invoice->client));
            Log::info('Invoice receipt email sent.', [
                'invoice_id' => $event->invoiceId,
                'client_email' => $invoice->client->email,
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_RECEIPT_SENT, 'Receipt emailed to '.$invoice->client->email.'.');
        } catch (Throwable $e) {
            report($e);
            Log::error('Invoice receipt email failed to send.', [
                'invoice_id' => $event->invoiceId,
                'client_email' => $invoice->client->email,
                'exception' => $e->getMessage(),
            ]);
            $this->logOutcome($event, InvoiceActivityConstants::TYPE_RECEIPT_FAILED, 'Receipt email failed to send.');
        }
    }

    private function logOutcome(InvoicePaid $event, string $type, string $description): void
    {
        $this->invoiceActivityRepository->insert($this->invoiceActivityHelper->prepareDao(
            $event->invoiceId,
            $event->userId,
            $type,
            $description,
        ));
    }
}
