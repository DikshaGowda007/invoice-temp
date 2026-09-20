<?php

namespace App\Repositories\MySql\V1;

use App\Constants\CommonConstant;
use App\Models\Invoice;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\V1\InvoiceRepository;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepositoryImpl implements InvoiceRepository
{
    public function insert(InvoiceDAO $invoiceDao): Invoice
    {
        return Invoice::create($invoiceDao->toArray());
    }

    public function findByUserId(int $userId): Collection
    {
        return Invoice::with('client')
            ->where('user_id', $userId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function findByIdAndUserId(int $id, int $userId): Collection
    {
        return Invoice::with('client')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function findByRecurringInvoiceIdAndUserId(int $recurringInvoiceId, int $userId): Collection
    {
        return Invoice::where('recurring_invoice_id', $recurringInvoiceId)
            ->where('user_id', $userId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function updateById(int $id, InvoiceDAO $invoiceDao): bool
    {
        return Invoice::where('id', $id)->update($invoiceDao->toArray());
    }
}
