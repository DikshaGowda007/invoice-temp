<?php

namespace App\Listeners;

use App\Constants\InvoiceActivityConstants;
use App\Events\InvoicePaid;
use App\Modules\V1\Invoice\Helpers\InvoiceActivityHelper;
use App\Repositories\V1\InvoiceActivityRepository;

class LogInvoicePaidActivityListener
{
    public function __construct(
        private InvoiceActivityHelper $invoiceActivityHelper,
        private readonly InvoiceActivityRepository $invoiceActivityRepository,
    ) {}

    public function handle(InvoicePaid $event): void
    {
        $this->invoiceActivityRepository->insert($this->invoiceActivityHelper->prepareDao(
            $event->invoiceId,
            $event->userId,
            InvoiceActivityConstants::TYPE_PAID,
            'Invoice marked as paid.',
        ));
    }
}
