<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class InvoiceSent
{
    use Dispatchable;

    public function __construct(
        public readonly int $invoiceId,
        public readonly int $userId,
    ) {}
}
