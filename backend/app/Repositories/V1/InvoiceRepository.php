<?php

namespace App\Repositories\V1;

use App\Models\Invoice;
use App\Repositories\DAO\V1\InvoiceDAO;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepository
{
    public function insert(InvoiceDAO $invoiceDao): Invoice;

    public function findByUserId(int $userId): Collection;

    public function findByIdAndUserId(int $id, int $userId): Collection;

    public function findByRecurringInvoiceIdAndUserId(int $recurringInvoiceId, int $userId): Collection;

    public function updateById(int $id, InvoiceDAO $invoiceDao): bool;
}
