<?php

namespace App\Modules\V1\Invoice\Services\Delete;

use App\Constants\CommonConstant;
use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Models\Invoice;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;

class DetailsService
{
    public function __construct(
        private InvoiceDAO $invoiceDao,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly AuthService $authService,
    ) {}

    public function delete(int $id): array
    {
        $userId = $this->authService->getData()->get('userId');

        $this->findInvoiceOrFail($id, $userId);
        $this->updateInvoice($id);

        return CommonUtils::successResponse('Invoice deleted successfully.');
    }

    private function findInvoiceOrFail(int $id, int $userId): Invoice
    {
        $invoice = $this->invoiceRepository->findByIdAndUserId($id, $userId)->first();

        if (! $invoice) {
            throw DataNotFoundException::withMessage('Invoice not found.');
        }

        return $invoice;
    }

    private function updateInvoice(int $id): void
    {
        $this->invoiceDao->setIsDeleted(CommonConstant::IS_DELETED_YES);
        $this->invoiceRepository->updateById($id, $this->invoiceDao);
    }
}
