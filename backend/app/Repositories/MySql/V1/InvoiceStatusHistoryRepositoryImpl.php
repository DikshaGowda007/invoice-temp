<?php

namespace App\Repositories\MySql\V1;

use App\Models\InvoiceStatusHistory;
use App\Repositories\DAO\V1\InvoiceStatusHistoryDAO;
use App\Repositories\V1\InvoiceStatusHistoryRepository;
use Illuminate\Database\Eloquent\Collection;

class InvoiceStatusHistoryRepositoryImpl implements InvoiceStatusHistoryRepository
{
    public function insert(InvoiceStatusHistoryDAO $invoiceStatusHistoryDao): InvoiceStatusHistory
    {
        return InvoiceStatusHistory::create($invoiceStatusHistoryDao->toArray());
    }

    public function findByInvoiceId(int $invoiceId): Collection
    {
        return InvoiceStatusHistory::where('invoice_id', $invoiceId)
            ->orderBy('created_at')
            ->get();
    }
}
