<?php

namespace App\Repositories\V1;

use App\Models\InvoiceActivity;
use App\Repositories\DAO\V1\InvoiceActivityDAO;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceActivityRepository
{
    public function insert(InvoiceActivityDAO $invoiceActivityDao): InvoiceActivity;

    public function findByInvoiceId(int $invoiceId): Collection;
}
