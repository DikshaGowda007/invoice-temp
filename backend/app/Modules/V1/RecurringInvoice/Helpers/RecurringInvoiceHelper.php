<?php

namespace App\Modules\V1\RecurringInvoice\Helpers;

use App\Constants\RecurringInvoiceConstants;
use App\Http\Requests\V1\RecurringInvoice\Add\DetailsRequest as AddDetailsRequest;
use App\Http\Requests\V1\RecurringInvoice\Edit\DetailsRequest as EditDetailsRequest;
use App\Modules\V1\RecurringInvoice\Bo\Add\DetailsBo as AddDetailsBo;
use App\Modules\V1\RecurringInvoice\Bo\Edit\DetailsBo as EditDetailsBo;
use App\Repositories\DAO\V1\RecurringInvoiceDAO;

class RecurringInvoiceHelper
{
    public function prepareAddBo(AddDetailsRequest $addDetailsRequest): AddDetailsBo
    {
        $addDetailsBo = new AddDetailsBo;

        $addDetailsBo->setClientId((int) $addDetailsRequest->input('client_id'));
        $addDetailsBo->setFrequency($addDetailsRequest->input('frequency'));
        $addDetailsBo->setNextRunAt($addDetailsRequest->input('next_run_at'));

        if ($addDetailsRequest->has('tax_rate')) {
            $addDetailsBo->setTaxRate((float) $addDetailsRequest->input('tax_rate'));
        }
        if ($addDetailsRequest->has('discount_amount')) {
            $addDetailsBo->setDiscountAmount((float) $addDetailsRequest->input('discount_amount'));
        }
        if ($addDetailsRequest->has('notes')) {
            $addDetailsBo->setNotes($addDetailsRequest->input('notes'));
        }

        return $addDetailsBo;
    }

    public function prepareEditBo(EditDetailsRequest $editDetailsRequest): EditDetailsBo
    {
        $editDetailsBo = new EditDetailsBo;

        $editDetailsBo->setId((int) $editDetailsRequest->input('id'));
        $editDetailsBo->setClientId((int) $editDetailsRequest->input('client_id'));
        $editDetailsBo->setFrequency($editDetailsRequest->input('frequency'));
        $editDetailsBo->setNextRunAt($editDetailsRequest->input('next_run_at'));

        if ($editDetailsRequest->has('tax_rate')) {
            $editDetailsBo->setTaxRate((float) $editDetailsRequest->input('tax_rate'));
        }
        if ($editDetailsRequest->has('discount_amount')) {
            $editDetailsBo->setDiscountAmount((float) $editDetailsRequest->input('discount_amount'));
        }
        if ($editDetailsRequest->has('notes')) {
            $editDetailsBo->setNotes($editDetailsRequest->input('notes'));
        }

        return $editDetailsBo;
    }

    public function prepareAddDao(AddDetailsBo $addDetailsBo, int $userId): RecurringInvoiceDAO
    {
        $recurringInvoiceDao = new RecurringInvoiceDAO;

        $recurringInvoiceDao->setUserId($userId);
        $recurringInvoiceDao->setClientId($addDetailsBo->getClientId());
        $recurringInvoiceDao->setFrequency($addDetailsBo->getFrequency());
        $recurringInvoiceDao->setNextRunAt($addDetailsBo->getNextRunAt());
        $recurringInvoiceDao->setTaxRate($addDetailsBo->getTaxRate() ?? RecurringInvoiceConstants::DEFAULT_AMOUNT);
        $recurringInvoiceDao->setDiscountAmount($addDetailsBo->getDiscountAmount() ?? RecurringInvoiceConstants::DEFAULT_AMOUNT);
        $recurringInvoiceDao->setNotes($addDetailsBo->getNotes());
        $recurringInvoiceDao->setStatus(RecurringInvoiceConstants::STATUS_ACTIVE);
        $recurringInvoiceDao->setSubtotal(RecurringInvoiceConstants::DEFAULT_AMOUNT);
        $recurringInvoiceDao->setTaxAmount(RecurringInvoiceConstants::DEFAULT_AMOUNT);
        $recurringInvoiceDao->setTotal(RecurringInvoiceConstants::DEFAULT_AMOUNT);

        return $recurringInvoiceDao;
    }

    public function prepareEditDao(EditDetailsBo $editDetailsBo): RecurringInvoiceDAO
    {
        $recurringInvoiceDao = new RecurringInvoiceDAO;

        $recurringInvoiceDao->setClientId($editDetailsBo->getClientId());
        $recurringInvoiceDao->setFrequency($editDetailsBo->getFrequency());
        $recurringInvoiceDao->setNextRunAt($editDetailsBo->getNextRunAt());
        $recurringInvoiceDao->setTaxRate($editDetailsBo->getTaxRate() ?? RecurringInvoiceConstants::DEFAULT_AMOUNT);
        $recurringInvoiceDao->setDiscountAmount($editDetailsBo->getDiscountAmount() ?? RecurringInvoiceConstants::DEFAULT_AMOUNT);
        $recurringInvoiceDao->setNotes($editDetailsBo->getNotes());

        return $recurringInvoiceDao;
    }

    public function prepareUpdateStatusDao(string $status): RecurringInvoiceDAO
    {
        $recurringInvoiceDao = new RecurringInvoiceDAO;
        $recurringInvoiceDao->setStatus($status);

        return $recurringInvoiceDao;
    }
}
