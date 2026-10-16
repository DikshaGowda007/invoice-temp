<?php

namespace App\Repositories\MySql\V1;

use App\Models\InvoiceActivity;
use App\Repositories\DAO\V1\InvoiceActivityDAO;
use App\Repositories\V1\InvoiceActivityRepository;
use Illuminate\Database\Eloquent\Collection;

class InvoiceActivityRepositoryImpl implements InvoiceActivityRepository
{
    public function insert(InvoiceActivityDAO $invoiceActivityDao): InvoiceActivity
    {
        return InvoiceActivity::create($invoiceActivityDao->toArray());
    }

    public function findByInvoiceId(int $invoiceId): Collection
    {
        return InvoiceActivity::where('invoice_id', $invoiceId)->get();
    }
}
