<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class InvoicePaid
{
    use Dispatchable;

    public function __construct(
        public readonly int $invoiceId,
        public readonly int $userId,
    ) {}
}
