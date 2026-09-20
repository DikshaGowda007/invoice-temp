<?php

namespace App\Repositories\V1;

use App\Models\RecurringInvoiceLineItem;
use App\Repositories\DAO\V1\RecurringInvoiceLineItemDAO;
use Illuminate\Database\Eloquent\Collection;

interface RecurringInvoiceLineItemRepository
{
    public function insert(RecurringInvoiceLineItemDAO $recurringInvoiceLineItemDao): RecurringInvoiceLineItem;

    public function findById(int $id): Collection;

    public function findByRecurringInvoiceId(int $recurringInvoiceId): Collection;

    public function updateById(int $id, RecurringInvoiceLineItemDAO $recurringInvoiceLineItemDao): bool;
}
