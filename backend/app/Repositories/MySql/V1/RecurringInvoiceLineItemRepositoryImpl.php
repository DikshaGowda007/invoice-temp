<?php

namespace App\Repositories\MySql\V1;

use App\Constants\CommonConstant;
use App\Models\RecurringInvoiceLineItem;
use App\Repositories\DAO\V1\RecurringInvoiceLineItemDAO;
use App\Repositories\V1\RecurringInvoiceLineItemRepository;
use Illuminate\Database\Eloquent\Collection;

class RecurringInvoiceLineItemRepositoryImpl implements RecurringInvoiceLineItemRepository
{
    public function insert(RecurringInvoiceLineItemDAO $recurringInvoiceLineItemDao): RecurringInvoiceLineItem
    {
        return RecurringInvoiceLineItem::create($recurringInvoiceLineItemDao->toArray());
    }

    public function findById(int $id): Collection
    {
        return RecurringInvoiceLineItem::where('id', $id)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function findByRecurringInvoiceId(int $recurringInvoiceId): Collection
    {
        return RecurringInvoiceLineItem::where('recurring_invoice_id', $recurringInvoiceId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function updateById(int $id, RecurringInvoiceLineItemDAO $recurringInvoiceLineItemDao): bool
    {
        return RecurringInvoiceLineItem::where('id', $id)->update($recurringInvoiceLineItemDao->toArray());
    }
}
