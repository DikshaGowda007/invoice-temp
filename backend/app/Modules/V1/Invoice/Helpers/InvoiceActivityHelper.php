<?php

namespace App\Modules\V1\Invoice\Helpers;

use App\Repositories\DAO\V1\InvoiceActivityDAO;

class InvoiceActivityHelper
{
    public function prepareDao(int $invoiceId, ?int $userId, string $type, string $description): InvoiceActivityDAO
    {
        $invoiceActivityDao = new InvoiceActivityDAO;
        $invoiceActivityDao->setInvoiceId($invoiceId);
        $invoiceActivityDao->setUserId($userId);
        $invoiceActivityDao->setType($type);
        $invoiceActivityDao->setDescription($description);

        return $invoiceActivityDao;
    }
}
