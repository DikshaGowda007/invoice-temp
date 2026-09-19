<?php

namespace App\Repositories\V1;

use App\Models\InvoiceLineItem;
use App\Repositories\DAO\V1\InvoiceLineItemDAO;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceLineItemRepository
{
    public function insert(InvoiceLineItemDAO $invoiceLineItemDao): InvoiceLineItem;

    public function findById(int $id): Collection;

    public function findByInvoiceId(int $invoiceId): Collection;

    public function updateById(int $id, InvoiceLineItemDAO $invoiceLineItemDao): bool;
}
