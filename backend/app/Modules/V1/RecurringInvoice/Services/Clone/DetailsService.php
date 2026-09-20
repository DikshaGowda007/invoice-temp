<?php

namespace App\Modules\V1\RecurringInvoice\Services\Clone;

use App\Constants\RecurringInvoiceConstants;
use App\Exceptions\DataNotFoundException;
use App\Http\Services\AuthService;
use App\Repositories\DAO\V1\RecurringInvoiceDAO;
use App\Repositories\DAO\V1\RecurringInvoiceLineItemDAO;
use App\Repositories\V1\RecurringInvoiceLineItemRepository;
use App\Repositories\V1\RecurringInvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;

class DetailsService
{
    public function __construct(
        private readonly RecurringInvoiceRepository $recurringInvoiceRepository,
        private readonly RecurringInvoiceLineItemRepository $recurringInvoiceLineItemRepository,
        private readonly AuthService $authService,
    ) {}

    public function clone(int $id): array
    {
        $userId = $this->authService->getData()->get('userId');
        $original = $this->findRecurringInvoiceOrFail($id, $userId);
        $originalLineItems = $this->findOriginalLineItems($id);

        $recurringInvoiceDao = $this->prepareCloneDao($original, $userId);
        $cloned = $this->recurringInvoiceRepository->insert($recurringInvoiceDao);
        $this->cloneLineItems($originalLineItems, $cloned->id);

        return CommonUtils::successDataResponse($this->formatResponse(collect($cloned)));
    }

    private function findRecurringInvoiceOrFail(int $id, int $userId): Collection
    {
        $recurringInvoice = $this->recurringInvoiceRepository->findByIdAndUserId($id, $userId)->first();

        if (! $recurringInvoice) {
            throw DataNotFoundException::withMessage('Recurring invoice not found.');
        }

        return collect($recurringInvoice);
    }

    private function findOriginalLineItems(int $id): Collection
    {
        return $this->recurringInvoiceLineItemRepository->findByRecurringInvoiceId($id);
    }

    private function prepareCloneDao(Collection $original, int $userId): RecurringInvoiceDAO
    {
        $recurringInvoiceDao = new RecurringInvoiceDAO;
        $recurringInvoiceDao->setUserId($userId);
        $recurringInvoiceDao->setClientId($original->get('client_id'));
        $recurringInvoiceDao->setFrequency($original->get('frequency'));
        $recurringInvoiceDao->setNextRunAt(now()->toDateTimeString());
        $recurringInvoiceDao->setTaxRate((float) $original->get('tax_rate'));
        $recurringInvoiceDao->setDiscountAmount((float) $original->get('discount_amount'));
        $recurringInvoiceDao->setNotes($original->get('notes'));
        $recurringInvoiceDao->setStatus(RecurringInvoiceConstants::STATUS_ACTIVE);
        $recurringInvoiceDao->setSubtotal((float) $original->get('subtotal'));
        $recurringInvoiceDao->setTaxAmount((float) $original->get('tax_amount'));
        $recurringInvoiceDao->setTotal((float) $original->get('total'));

        return $recurringInvoiceDao;
    }

    private function cloneLineItems(Collection $originalLineItems, int $newRecurringInvoiceId): void
    {
        foreach ($originalLineItems as $lineItem) {
            $lineItemDao = $this->prepareCloneLineItemDao(collect($lineItem), $newRecurringInvoiceId);
            $this->recurringInvoiceLineItemRepository->insert($lineItemDao);
        }
    }

    private function prepareCloneLineItemDao(Collection $lineItem, int $newRecurringInvoiceId): RecurringInvoiceLineItemDAO
    {
        $lineItemDao = new RecurringInvoiceLineItemDAO;
        $lineItemDao->setRecurringInvoiceId($newRecurringInvoiceId);
        $lineItemDao->setDescription($lineItem->get('description'));
        $lineItemDao->setQuantity((float) $lineItem->get('quantity'));
        $lineItemDao->setUnitPrice((float) $lineItem->get('unit_price'));
        $lineItemDao->setLineTotal((float) $lineItem->get('line_total'));

        return $lineItemDao;
    }

    private function formatResponse(Collection $recurringInvoice): array
    {
        return [
            'recurring_invoice' => $this->formatRecurringInvoice($recurringInvoice),
        ];
    }

    private function formatRecurringInvoice(Collection $recurringInvoice): array
    {
        return [
            'id' => $recurringInvoice->get('id'),
            'client_id' => $recurringInvoice->get('client_id'),
            'frequency' => $recurringInvoice->get('frequency'),
            'status' => $recurringInvoice->get('status'),
            'next_run_at' => $recurringInvoice->get('next_run_at'),
            'last_generated_at' => $recurringInvoice->get('last_generated_at'),
            'subtotal' => $recurringInvoice->get('subtotal'),
            'tax_rate' => $recurringInvoice->get('tax_rate'),
            'tax_amount' => $recurringInvoice->get('tax_amount'),
            'discount_amount' => $recurringInvoice->get('discount_amount'),
            'total' => $recurringInvoice->get('total'),
            'notes' => $recurringInvoice->get('notes'),
        ];
    }
}
