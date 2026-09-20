<?php

namespace App\Modules\V1\RecurringInvoice\Helpers;

use App\Http\Requests\V1\RecurringInvoice\LineItem\Add\DetailsRequest as AddDetailsRequest;
use App\Http\Requests\V1\RecurringInvoice\LineItem\Update\DetailsRequest as UpdateDetailsRequest;
use App\Modules\V1\RecurringInvoice\Bo\LineItem\Add\DetailsBo as AddDetailsBo;
use App\Modules\V1\RecurringInvoice\Bo\LineItem\Update\DetailsBo as UpdateDetailsBo;
use App\Repositories\DAO\V1\RecurringInvoiceLineItemDAO;

class RecurringLineItemHelper
{
    public function prepareAddBo(AddDetailsRequest $addDetailsRequest): AddDetailsBo
    {
        $addDetailsBo = new AddDetailsBo;

        $addDetailsBo->setRecurringInvoiceId((int) $addDetailsRequest->input('recurring_invoice_id'));
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

    public function prepareAddDao(AddDetailsBo $addDetailsBo): RecurringInvoiceLineItemDAO
    {
        $lineItemDao = new RecurringInvoiceLineItemDAO;

        $lineItemDao->setRecurringInvoiceId($addDetailsBo->getRecurringInvoiceId());
        $lineItemDao->setDescription($addDetailsBo->getDescription());
        $lineItemDao->setQuantity($addDetailsBo->getQuantity());
        $lineItemDao->setUnitPrice($addDetailsBo->getUnitPrice());
        $lineItemDao->setLineTotal(round($addDetailsBo->getQuantity() * $addDetailsBo->getUnitPrice(), 2));

        return $lineItemDao;
    }

    public function prepareUpdateDao(UpdateDetailsBo $updateDetailsBo): RecurringInvoiceLineItemDAO
    {
        $lineItemDao = new RecurringInvoiceLineItemDAO;

        $lineItemDao->setDescription($updateDetailsBo->getDescription());
        $lineItemDao->setQuantity($updateDetailsBo->getQuantity());
        $lineItemDao->setUnitPrice($updateDetailsBo->getUnitPrice());
        $lineItemDao->setLineTotal(round($updateDetailsBo->getQuantity() * $updateDetailsBo->getUnitPrice(), 2));

        return $lineItemDao;
    }
}
