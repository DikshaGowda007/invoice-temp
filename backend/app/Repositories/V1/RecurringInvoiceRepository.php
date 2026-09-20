<?php

namespace App\Repositories\V1;

use App\Models\RecurringInvoice;
use App\Repositories\DAO\V1\RecurringInvoiceDAO;
use Illuminate\Database\Eloquent\Collection;

interface RecurringInvoiceRepository
{
    public function insert(RecurringInvoiceDAO $recurringInvoiceDao): RecurringInvoice;

    public function findByUserId(int $userId): Collection;

    public function findByIdAndUserId(int $id, int $userId): Collection;

    public function updateById(int $id, RecurringInvoiceDAO $recurringInvoiceDao): bool;

    public function findByStatusAndNextRunAt(string $status, string $asOf): Collection;
}
