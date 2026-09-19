<?php

namespace App\Repositories\MySql\V1;

use App\Constants\CommonConstant;
use App\Models\InvoiceLineItem;
use App\Repositories\DAO\V1\InvoiceLineItemDAO;
use App\Repositories\V1\InvoiceLineItemRepository;
use Illuminate\Database\Eloquent\Collection;

class InvoiceLineItemRepositoryImpl implements InvoiceLineItemRepository
{
    public function insert(InvoiceLineItemDAO $invoiceLineItemDao): InvoiceLineItem
    {
        return InvoiceLineItem::create($invoiceLineItemDao->toArray());
    }

    public function findById(int $id): Collection
    {
        return InvoiceLineItem::where('id', $id)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function findByInvoiceId(int $invoiceId): Collection
    {
        return InvoiceLineItem::where('invoice_id', $invoiceId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function updateById(int $id, InvoiceLineItemDAO $invoiceLineItemDao): bool
    {
        return InvoiceLineItem::where('id', $id)->update($invoiceLineItemDao->toArray());
    }
}
