<?php

namespace App\Modules\V1\Invoice\Helpers;

use App\Http\Requests\V1\Invoice\LineItem\Add\DetailsRequest as AddDetailsRequest;
use App\Http\Requests\V1\Invoice\LineItem\Update\DetailsRequest as UpdateDetailsRequest;
use App\Modules\V1\Invoice\Bo\LineItem\Add\DetailsBo as AddDetailsBo;
use App\Modules\V1\Invoice\Bo\LineItem\Update\DetailsBo as UpdateDetailsBo;
use App\Repositories\DAO\V1\InvoiceLineItemDAO;

class LineItemHelper
{
    public function prepareAddBo(AddDetailsRequest $addDetailsRequest): AddDetailsBo
    {
        $addDetailsBo = new AddDetailsBo;

        $addDetailsBo->setInvoiceId((int) $addDetailsRequest->input('invoice_id'));
        $addDetailsBo->setDescription($addDetailsRequest->input('description'));
        $addDetailsBo->setQuantity((float) $addDetailsRequest->input('quantity'));
        $addDetailsBo->setUnitPrice((float) $addDetailsRequest->input('unit_price'));

        return $addDetailsBo;
    }

    public function prepareUpdateBo(UpdateDetailsRequest $updateDetailsRequest): UpdateDetailsBo
    {
        $updateDetailsBo = new UpdateDetailsBo;

        $updateDetailsBo->setLineItemId((int) $updateDetailsRequest->input('line_item_id'));
        $updateDetailsBo->setDescription($updateDetailsRequest->input('description'));
        $updateDetailsBo->setQuantity((float) $updateDetailsRequest->input('quantity'));
        $updateDetailsBo->setUnitPrice((float) $updateDetailsRequest->input('unit_price'));

        return $updateDetailsBo;
    }

    public function prepareAddDao(AddDetailsBo $addDetailsBo): InvoiceLineItemDAO
    {
        $invoiceLineItemDao = new InvoiceLineItemDAO;

        $invoiceLineItemDao->setInvoiceId($addDetailsBo->getInvoiceId());
        $invoiceLineItemDao->setDescription($addDetailsBo->getDescription());
        $invoiceLineItemDao->setQuantity($addDetailsBo->getQuantity());
        $invoiceLineItemDao->setUnitPrice($addDetailsBo->getUnitPrice());
        $invoiceLineItemDao->setLineTotal(round($addDetailsBo->getQuantity() * $addDetailsBo->getUnitPrice(), 2));

        return $invoiceLineItemDao;
    }

    public function prepareUpdateDao(UpdateDetailsBo $updateDetailsBo): InvoiceLineItemDAO
    {
        $invoiceLineItemDao = new InvoiceLineItemDAO;

        $invoiceLineItemDao->setDescription($updateDetailsBo->getDescription());
        $invoiceLineItemDao->setQuantity($updateDetailsBo->getQuantity());
        $invoiceLineItemDao->setUnitPrice($updateDetailsBo->getUnitPrice());
        $invoiceLineItemDao->setLineTotal(round($updateDetailsBo->getQuantity() * $updateDetailsBo->getUnitPrice(), 2));

        return $invoiceLineItemDao;
    }
}
