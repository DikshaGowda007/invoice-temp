<?php

namespace App\Modules\V1\Invoice\Services\Delete;

use App\Constants\CommonConstant;
use App\Constants\InvoiceActivityConstants;
use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Modules\V1\Invoice\Helpers\InvoiceActivityHelper;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\V1\InvoiceActivityRepository;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private InvoiceDAO $invoiceDao,
        private InvoiceActivityHelper $invoiceActivityHelper,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceActivityRepository $invoiceActivityRepository,
        private readonly AuthService $authService,
    ) {}

    public function delete(int $id): array
    {
        $userId = $this->authService->getData()->get('userId');

        $this->findInvoiceOrFail($id, $userId);
        $this->updateInvoice($id);

        $this->invoiceActivityRepository->insert($this->invoiceActivityHelper->prepareDao(
            $id,
            $userId,
            InvoiceActivityConstants::TYPE_DELETED,
            'Invoice deleted.',
        ));

        return CommonUtils::successResponse('Invoice deleted successfully.');
    }

    private function findInvoiceOrFail(int $id, int $userId): Collection
    {
        $invoice = $this->invoiceRepository->findByIdAndUserId($id, $userId)->first();

        if (! $invoice) {
            throw DataNotFoundException::withMessage('Invoice not found.');
        }

        return collect($invoice);
    }

    private function updateInvoice(int $id): void
    {
        $this->invoiceDao->setIsDeleted(CommonConstant::IS_DELETED_YES);
        $this->invoiceRepository->updateById($id, $this->invoiceDao);
    }
}
