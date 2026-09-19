<?php

namespace App\Repositories\V1;

use App\Models\InvoiceStatusHistory;
use App\Repositories\DAO\V1\InvoiceStatusHistoryDAO;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceStatusHistoryRepository
{
    public function insert(InvoiceStatusHistoryDAO $invoiceStatusHistoryDao): InvoiceStatusHistory;

    public function findByInvoiceId(int $invoiceId): Collection;
}
