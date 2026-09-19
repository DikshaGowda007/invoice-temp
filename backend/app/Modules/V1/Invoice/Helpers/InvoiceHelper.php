<?php

namespace App\Modules\V1\Invoice\Helpers;

use App\Http\Requests\V1\Invoice\Add\DetailsRequest as AddDetailsRequest;
use App\Http\Requests\V1\Invoice\Edit\DetailsRequest as EditDetailsRequest;
use App\Http\Requests\V1\Invoice\UpdateStatus\DetailsRequest as UpdateStatusDetailsRequest;
use App\Modules\V1\Invoice\Bo\Add\DetailsBo as AddDetailsBo;
use App\Modules\V1\Invoice\Bo\Edit\DetailsBo as EditDetailsBo;
use App\Modules\V1\Invoice\Bo\UpdateStatus\DetailsBo as UpdateStatusDetailsBo;
use App\Repositories\DAO\V1\InvoiceDAO;

class InvoiceHelper
{
    public function prepareAddBo(AddDetailsRequest $addDetailsRequest): AddDetailsBo
    {
        $addDetailsBo = new AddDetailsBo;

        $addDetailsBo->setClientId((int) $addDetailsRequest->input('client_id'));
        $addDetailsBo->setIssueDate($addDetailsRequest->input('issue_date'));

        if ($addDetailsRequest->has('due_date')) {
            $addDetailsBo->setDueDate($addDetailsRequest->input('due_date'));
        }
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
        $editDetailsBo->setIssueDate($editDetailsRequest->input('issue_date'));

        if ($editDetailsRequest->has('due_date')) {
            $editDetailsBo->setDueDate($editDetailsRequest->input('due_date'));
        }
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

    public function prepareUpdateStatusBo(UpdateStatusDetailsRequest $updateStatusDetailsRequest): UpdateStatusDetailsBo
    {
        $updateStatusDetailsBo = new UpdateStatusDetailsBo;

        $updateStatusDetailsBo->setId((int) $updateStatusDetailsRequest->input('id'));
        $updateStatusDetailsBo->setStatus($updateStatusDetailsRequest->input('status'));

        if ($updateStatusDetailsRequest->has('notes')) {
            $updateStatusDetailsBo->setNotes($updateStatusDetailsRequest->input('notes'));
        }

        return $updateStatusDetailsBo;
    }

    public function prepareAddDao(AddDetailsBo $addDetailsBo, int $userId, string $invoiceNumber): InvoiceDAO
    {
        $invoiceDao = new InvoiceDAO;

        $invoiceDao->setUserId($userId);
        $invoiceDao->setClientId($addDetailsBo->getClientId());
        $invoiceDao->setInvoiceNumber($invoiceNumber);
        $invoiceDao->setIssueDate($addDetailsBo->getIssueDate());
        $invoiceDao->setDueDate($addDetailsBo->getDueDate());
        $invoiceDao->setTaxRate($addDetailsBo->getTaxRate() ?? 0.0);
        $invoiceDao->setDiscountAmount($addDetailsBo->getDiscountAmount() ?? 0.0);
        $invoiceDao->setNotes($addDetailsBo->getNotes());

        return $invoiceDao;
    }

    public function prepareEditDao(EditDetailsBo $editDetailsBo): InvoiceDAO
    {
        $invoiceDao = new InvoiceDAO;

        $invoiceDao->setClientId($editDetailsBo->getClientId());
        $invoiceDao->setIssueDate($editDetailsBo->getIssueDate());
        $invoiceDao->setDueDate($editDetailsBo->getDueDate());
        $invoiceDao->setTaxRate($editDetailsBo->getTaxRate() ?? 0.0);
        $invoiceDao->setDiscountAmount($editDetailsBo->getDiscountAmount() ?? 0.0);
        $invoiceDao->setNotes($editDetailsBo->getNotes());

        return $invoiceDao;
    }

    public function prepareUpdateStatusDao(string $status): InvoiceDAO
    {
        $invoiceDao = new InvoiceDAO;
        $invoiceDao->setStatus($status);

        return $invoiceDao;
    }

    public function generateInvoiceNumber(int $userId): string
    {
        return 'INV-'.str_pad((string) $userId, 3, '0', STR_PAD_LEFT).'-'.strtoupper(substr(uniqid(), -6));
    }

    /**
     * @return array{subtotal: float, tax_amount: float, total: float}
     */
    public function computeTotals(float $subtotal, float $taxRate, float $discountAmount): array
    {
        $discountedSubtotal = max($subtotal - $discountAmount, 0);
        $taxAmount = round($discountedSubtotal * ($taxRate / 100), 2);
        $total = round($discountedSubtotal + $taxAmount, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => $taxAmount,
            'total' => $total,
        ];
    }
}
