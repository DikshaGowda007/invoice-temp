<?php

namespace App\Modules\V1\RecurringInvoice\Services\Generate;

use App\Constants\InvoiceConstants;
use App\Constants\RecurringInvoiceConstants;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\DAO\V1\InvoiceLineItemDAO;
use App\Repositories\DAO\V1\RecurringInvoiceDAO;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use App\Repositories\V1\RecurringInvoiceLineItemRepository;
use App\Repositories\V1\RecurringInvoiceRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DetailsService
{
    private const LOCK_KEY = 'recurring-invoices:generate';

    private const LOCK_TTL_SECONDS = 900;

    public function __construct(
        private InvoiceHelper $invoiceHelper,
        private readonly RecurringInvoiceRepository $recurringInvoiceRepository,
        private readonly RecurringInvoiceLineItemRepository $recurringInvoiceLineItemRepository,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceLineItemRepository $invoiceLineItemRepository,
    ) {}

    /**
     * @return array{due: int, generated: int, failed: int, locked: bool}
     */
    public function generateDue(bool $dryRun = false): array
    {
        $lock = Cache::lock(self::LOCK_KEY, self::LOCK_TTL_SECONDS);

        if (! $lock->get()) {
            Log::warning('recurring-invoices.generate.locked', [
                'message' => 'Skipped — a previous run is still holding the lock.',
            ]);

            return ['due' => 0, 'generated' => 0, 'failed' => 0, 'locked' => true];
        }

        try {
            return $this->execute($dryRun);
        } finally {
            $lock->release();
        }
    }

    private function execute(bool $dryRun): array
    {
        $now = Carbon::now()->toDateTimeString();
        $dueRecurringInvoices = $this->recurringInvoiceRepository->findByStatusAndNextRunAt(
            RecurringInvoiceConstants::STATUS_ACTIVE,
            $now,
        );

        $generated = 0;
        $failedIds = [];

        foreach ($dueRecurringInvoices as $recurringInvoice) {
            if ($dryRun) {
                continue;
            }

            try {
                DB::transaction(fn () => $this->generateInvoiceFor(collect($recurringInvoice)));
                $generated++;
            } catch (Throwable $e) {
                $failedIds[] = $recurringInvoice->id;

                Log::warning('recurring-invoices.generate.schedule_failed', [
                    'recurring_invoice_id' => $recurringInvoice->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $summary = [
            'due' => $dueRecurringInvoices->count(),
            'generated' => $generated,
            'failed' => count($failedIds),
            'locked' => false,
        ];

        Log::info('recurring-invoices.generate.summary', $summary + ['dry_run' => $dryRun]);

        return $summary;
    }

    private function generateInvoiceFor(Collection $recurringInvoice): void
    {
        $userId = (int) $recurringInvoice->get('user_id');

        $invoice = $this->invoiceRepository->insert($this->prepareInvoiceDao($recurringInvoice, $userId));

        $lineItems = $this->recurringInvoiceLineItemRepository->findByRecurringInvoiceId((int) $recurringInvoice->get('id'));

        foreach ($lineItems as $lineItem) {
            $this->invoiceLineItemRepository->insert($this->prepareInvoiceLineItemDao($lineItem, $invoice->id));
        }

        $subtotal = (float) $this->invoiceLineItemRepository->findByInvoiceId($invoice->id)->sum('line_total');
        $totals = $this->invoiceHelper->computeTotals($subtotal, (float) $recurringInvoice->get('tax_rate'), (float) $recurringInvoice->get('discount_amount'));
        $this->invoiceRepository->updateById($invoice->id, $this->prepareInvoiceTotalsDao($totals));

        $this->advanceSchedule($recurringInvoice);
    }

    private function prepareInvoiceDao(Collection $recurringInvoice, int $userId): InvoiceDAO
    {
        $invoiceDao = new InvoiceDAO;
        $invoiceDao->setUserId($userId);
        $invoiceDao->setClientId((int) $recurringInvoice->get('client_id'));
        $invoiceDao->setRecurringInvoiceId((int) $recurringInvoice->get('id'));
        $invoiceDao->setInvoiceNumber($this->invoiceHelper->generateInvoiceNumber($userId));
        $invoiceDao->setStatus(InvoiceConstants::STATUS_DRAFT);
        $invoiceDao->setIssueDate(Carbon::now()->toDateString());
        $invoiceDao->setTaxRate((float) $recurringInvoice->get('tax_rate'));
        $invoiceDao->setDiscountAmount((float) $recurringInvoice->get('discount_amount'));
        $invoiceDao->setSubtotal(0);
        $invoiceDao->setTaxAmount(0);
        $invoiceDao->setTotal(0);

        return $invoiceDao;
    }

    private function prepareInvoiceLineItemDao(object $lineItem, int $invoiceId): InvoiceLineItemDAO
    {
        $lineItemDao = new InvoiceLineItemDAO;
        $lineItemDao->setInvoiceId($invoiceId);
        $lineItemDao->setDescription($lineItem->description);
        $lineItemDao->setQuantity($lineItem->quantity);
        $lineItemDao->setUnitPrice($lineItem->unit_price);
        $lineItemDao->setLineTotal($lineItem->line_total);

        return $lineItemDao;
    }

    private function prepareInvoiceTotalsDao(array $totals): InvoiceDAO
    {
        $invoiceDao = new InvoiceDAO;
        $invoiceDao->setSubtotal($totals['subtotal']);
        $invoiceDao->setTaxAmount($totals['tax_amount']);
        $invoiceDao->setTotal($totals['total']);

        return $invoiceDao;
    }

    private function advanceSchedule(Collection $recurringInvoice): void
    {
        $nextRunAt = $this->calculateNextRunAt(
            Carbon::parse($recurringInvoice->get('next_run_at')),
            $recurringInvoice->get('frequency'),
        );

        $this->recurringInvoiceRepository->updateById(
            (int) $recurringInvoice->get('id'),
            $this->prepareAdvanceScheduleDao($nextRunAt),
        );
    }

    private function prepareAdvanceScheduleDao(Carbon $nextRunAt): RecurringInvoiceDAO
    {
        $recurringInvoiceDao = new RecurringInvoiceDAO;
        $recurringInvoiceDao->setNextRunAt($nextRunAt->toDateTimeString());
        $recurringInvoiceDao->setLastGeneratedAt(Carbon::now()->toDateTimeString());

        return $recurringInvoiceDao;
    }

    private function calculateNextRunAt(Carbon $currentNextRunAt, string $frequency): Carbon
    {
        return match ($frequency) {
            RecurringInvoiceConstants::FREQUENCY_WEEKLY => $currentNextRunAt->addWeek(),
            RecurringInvoiceConstants::FREQUENCY_MONTHLY => $currentNextRunAt->addMonthNoOverflow(),
            RecurringInvoiceConstants::FREQUENCY_QUARTERLY => $currentNextRunAt->addMonthsNoOverflow(3),
            RecurringInvoiceConstants::FREQUENCY_YEARLY => $currentNextRunAt->addYear(),
            default => $currentNextRunAt->addMonthNoOverflow(),
        };
    }
}
