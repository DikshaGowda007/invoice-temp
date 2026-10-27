<?php

namespace App\Modules\V1\Dashboard\Services\Summary;

use App\Constants\DashboardConstants;
use App\Constants\InvoiceConstants;
use App\Http\Requests\V1\Dashboard\Summary\DetailsRequest;
use App\Http\Services\AuthService;
use App\Models\Invoice;
use App\Modules\V1\Dashboard\Bo\Summary\DetailsBo;
use App\Modules\V1\Dashboard\Helpers\DashboardHelper;
use App\Repositories\V1\ClientRepository;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DetailsService
{
    private const CACHE_TTL_MINUTES = 5;

    private const TOP_CLIENTS_LIMIT = 5;

    private const RECENT_INVOICES_LIMIT = 5;

    public function __construct(
        private DashboardHelper $dashboardHelper,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly ClientRepository $clientRepository,
        private readonly AuthService $authService,
    ) {}

    public function prepareBo(DetailsRequest $detailsRequest): DetailsBo
    {
        return $this->dashboardHelper->prepareSummaryBo($detailsRequest);
    }

    public function summary(DetailsBo $detailsBo): array
    {
        $range = $detailsBo->getRange();
        $userId = $this->authService->getData()->get('userId');
        $months = DashboardConstants::RANGE_TO_MONTHS[$range]
            ?? DashboardConstants::RANGE_TO_MONTHS[DashboardConstants::DEFAULT_RANGE];

        $data = Cache::remember(
            "dashboard:summary:{$userId}:{$range}",
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn () => $this->build($userId, $months),
        );

        return CommonUtils::successDataResponse($data);
    }

    private function build(int $userId, int $months): array
    {
        $invoices = $this->invoiceRepository->findByUserId($userId)
            ->map(fn (Invoice $invoice) => collect($invoice));

        $totalsByMonth = $this->monthlyPaidTotals($userId, $months);
        $thisMonthKey = Carbon::now()->format('Y-m');
        $lastMonthKey = Carbon::now()->subMonthNoOverflow()->format('Y-m');
        $thisMonthPaid = $totalsByMonth->get($thisMonthKey, 0);
        $lastMonthPaid = $totalsByMonth->get($lastMonthKey, 0);

        return [
            'stats' => $this->formatStats($invoices, $userId, $thisMonthPaid, $lastMonthPaid),
            'revenue_over_time' => $this->formatRevenueOverTime($totalsByMonth, $months),
            'recent_invoices' => $this->formatRecentInvoices($userId),
            'top_clients' => $this->formatTopClients($invoices),
        ];
    }

    private function monthlyPaidTotals(int $userId, int $months): Collection
    {
        $since = Carbon::now()->subMonths(max($months, 2) - 1)->startOfMonth();
        $rows = $this->invoiceRepository->findByUserIdAndStatusAndSince(
            $userId,
            InvoiceConstants::STATUS_PAID,
            $since->toDateTimeString(),
        );

        return $rows
            ->groupBy(fn ($row) => Carbon::parse($row->paid_at)->format('Y-m'))
            ->map(fn (Collection $group) => (float) $group->sum('total'));
    }

    private function formatStats(Collection $invoices, int $userId, float $thisMonthPaid, float $lastMonthPaid): array
    {
        $totalRevenue = (float) $invoices
            ->filter(fn (Collection $invoice) => $invoice->get('status') === InvoiceConstants::STATUS_PAID)
            ->sum(fn (Collection $invoice) => (float) $invoice->get('total'));

        $outstandingStatuses = [InvoiceConstants::STATUS_SENT, InvoiceConstants::STATUS_OVERDUE];
        $outstandingInvoices = $invoices->filter(
            fn (Collection $invoice) => in_array($invoice->get('status'), $outstandingStatuses, true)
        );
        $outstandingTotal = (float) $outstandingInvoices->sum(fn (Collection $invoice) => (float) $invoice->get('total'));
        $outstandingCount = $outstandingInvoices->count();

        $priorCumulative = $totalRevenue - $thisMonthPaid;

        $clients = $this->clientRepository->findByUserId($userId);
        $newClientsThisMonth = $clients->filter(
            fn ($client) => Carbon::parse(collect($client)->get('created_at'))->isSameMonth(Carbon::now())
        )->count();

        return [
            'total_revenue' => round($totalRevenue, 2),
            'total_revenue_change_pct' => $priorCumulative > 0 ? round($thisMonthPaid / $priorCumulative * 100, 1) : null,
            'outstanding_total' => round($outstandingTotal, 2),
            'outstanding_count' => $outstandingCount,
            'paid_this_month' => round($thisMonthPaid, 2),
            'paid_this_month_change_pct' => $lastMonthPaid > 0 ? round(($thisMonthPaid - $lastMonthPaid) / $lastMonthPaid * 100, 1) : null,
            'active_clients' => $clients->count(),
            'active_clients_new_this_month' => $newClientsThisMonth,
        ];
    }

    private function formatRevenueOverTime(Collection $totalsByMonth, int $months): array
    {
        $since = Carbon::now()->subMonths($months - 1)->startOfMonth();

        return collect(range(0, $months - 1))
            ->map(fn (int $offset) => $since->copy()->addMonths($offset)->format('Y-m'))
            ->map(fn (string $month) => [
                'month' => $month,
                'total' => round($totalsByMonth->get($month, 0), 2),
            ])
            ->values()
            ->toArray();
    }

    private function formatRecentInvoices(int $userId): array
    {
        return $this->invoiceRepository->findRecentByUserIdAndLimit($userId, self::RECENT_INVOICES_LIMIT)
            ->map(function ($invoice) {
                $invoice = collect($invoice);
                $client = collect($invoice->get('client'));

                return [
                    'id' => $invoice->get('id'),
                    'invoice_number' => $invoice->get('invoice_number'),
                    'client_name' => $client->get('name'),
                    'total' => $invoice->get('total'),
                    'status' => $invoice->get('status'),
                    'issue_date' => $invoice->get('issue_date'),
                ];
            })
            ->values()
            ->toArray();
    }

    private function formatTopClients(Collection $invoices): array
    {
        return $invoices
            ->filter(fn (Collection $invoice) => $invoice->get('status') === InvoiceConstants::STATUS_PAID)
            ->groupBy(fn (Collection $invoice) => $invoice->get('client_id'))
            ->map(function (Collection $group) {
                $client = collect($group->first()->get('client'));

                return [
                    'client_id' => $client->get('id'),
                    'client_name' => $client->get('name'),
                    'total' => round((float) $group->sum(fn (Collection $invoice) => (float) $invoice->get('total')), 2),
                    'invoice_count' => $group->count(),
                ];
            })
            ->sortByDesc('total')
            ->take(self::TOP_CLIENTS_LIMIT)
            ->values()
            ->toArray();
    }
}
