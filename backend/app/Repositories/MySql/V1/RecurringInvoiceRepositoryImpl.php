<?php

namespace App\Repositories\MySql\V1;

use App\Constants\CommonConstant;
use App\Models\RecurringInvoice;
use App\Repositories\DAO\V1\RecurringInvoiceDAO;
use App\Repositories\V1\RecurringInvoiceRepository;
use Illuminate\Database\Eloquent\Collection;

class RecurringInvoiceRepositoryImpl implements RecurringInvoiceRepository
{
    public function insert(RecurringInvoiceDAO $recurringInvoiceDao): RecurringInvoice
    {
        return RecurringInvoice::create($recurringInvoiceDao->toArray());
    }

    public function findByUserId(int $userId): Collection
    {
        return RecurringInvoice::with('client')
            ->where('user_id', $userId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function findByIdAndUserId(int $id, int $userId): Collection
    {
        return RecurringInvoice::with('client')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function updateById(int $id, RecurringInvoiceDAO $recurringInvoiceDao): bool
    {
        return RecurringInvoice::where('id', $id)->update($recurringInvoiceDao->toArray());
    }

    public function findByStatusAndNextRunAt(string $status, string $asOf): Collection
    {
        return RecurringInvoice::where('status', $status)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->where('next_run_at', '<=', $asOf)
            ->get();
    }
}
