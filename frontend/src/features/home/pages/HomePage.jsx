import {
  ArrowRight,
  CircleCheck,
  Clock,
  LayoutDashboard,
  Loader2,
  Plus,
  RotateCcw,
  TrendingDown,
  TrendingUp,
  Users,
  Wallet,
} from 'lucide-react'
import { Link, useNavigate } from 'react-router-dom'
import {
  Area,
  AreaChart,
  CartesianGrid,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from 'recharts'
import { StatusBadge } from '@/components/common/StatusBadge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useDashboardSummary } from '@/features/home/hooks/useDashboardSummary'
import { avatarColorFor, initialsFor } from '@/utils/avatarColor'
import { formatCurrency, formatDate } from '@/utils/format'
import { invoiceDetailPath, ROUTES } from '@/utils/routePaths'

const CARD_SHADOW = 'shadow-[0_1px_2px_rgba(36,33,28,0.04),0_2px_8px_rgba(36,33,28,0.05)]'

const RANGE_OPTIONS = [
  { value: '3M', label: '3M', months: 3 },
  { value: '6M', label: '6M', months: 6 },
  { value: '1Y', label: '1Y', months: 12 },
  { value: '2Y', label: '2Y', months: 24 },
]

function formatMonthLabel(month) {
  if (!month) return ''
  const [year, monthNumber] = month.split('-')
  return new Date(Number(year), Number(monthNumber) - 1, 1).toLocaleDateString('en-IN', {
    month: 'short',
  })
}

function TrendNote({ pct, suffix }) {
  if (pct === null || pct === undefined) {
    return <span className="text-xs text-muted-foreground">{suffix}</span>
  }
  const isUp = pct >= 0
  const Icon = isUp ? TrendingUp : TrendingDown
  return (
    <span className={`inline-flex items-center gap-1 text-xs ${isUp ? 'text-success' : 'text-danger'}`}>
      <Icon size={13} />
      {isUp ? '+' : ''}
      {pct}% {suffix}
    </span>
  )
}

function StatCard({ label, value, icon, children }) {
  return (
    <Card size="sm" className={CARD_SHADOW}>
      <CardContent className="flex flex-col gap-2.5">
        <div className="flex items-center justify-between">
          <span className="text-[13px] text-muted-foreground">{label}</span>
          {icon}
        </div>
        <span className="text-[23px] font-semibold">{value}</span>
        {children}
      </CardContent>
    </Card>
  )
}

function RevenueTooltip({ active, payload, label }) {
  if (!active || !payload?.length) return null
  return (
    <div className="rounded-lg bg-popover px-3 py-2 text-xs ring-1 ring-foreground/10">
      <p className="mb-1 font-medium text-foreground">{formatMonthLabel(label)}</p>
      <p className="text-muted-foreground">{formatCurrency(payload[0].value)}</p>
    </div>
  )
}

function SkeletonBlock({ className }) {
  return <div className={`animate-pulse rounded-lg bg-muted ${className}`} />
}

function SectionLabel({ title, actionLabel, actionTo }) {
  return (
    <div className="flex items-center justify-between">
      <p className="text-[14.5px] font-semibold">{title}</p>
      {actionTo && (
        <Link
          to={actionTo}
          className="inline-flex items-center gap-1 text-xs font-medium text-primary hover:underline"
        >
          {actionLabel}
          <ArrowRight size={12} />
        </Link>
      )}
    </div>
  )
}

export default function HomePage() {
  const navigate = useNavigate()
  const {
    range,
    setRange,
    stats,
    revenueOverTime,
    recentInvoices,
    topClients,
    isLoading,
    isFetching,
    isError,
    errorMessage,
    refetch,
  } = useDashboardSummary()

  const hasRevenue = revenueOverTime.some((entry) => entry.total > 0)
  const isRefreshing = isFetching && !isLoading

  return (
    <div className="flex flex-col gap-5">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-semibold">Dashboard</h1>
          <p className="text-[13px] text-muted-foreground">
            Welcome back, here&apos;s what&apos;s happening with your business.
          </p>
        </div>
        <Button render={<Link to={ROUTES.INVOICE_NEW} />}>
          <Plus data-icon="inline-start" size={14} />
          New Invoice
        </Button>
      </div>

      {isLoading && (
        <div className="flex flex-col gap-5">
          <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            {[0, 1, 2, 3].map((i) => (
              <Card key={i} size="sm" className={CARD_SHADOW}>
                <CardContent className="flex flex-col gap-2.5">
                  <SkeletonBlock className="h-4 w-24" />
                  <SkeletonBlock className="h-6 w-20" />
                  <SkeletonBlock className="h-3 w-28" />
                </CardContent>
              </Card>
            ))}
          </div>
          <Card className={CARD_SHADOW}>
            <CardContent>
              <SkeletonBlock className="h-56 w-full" />
            </CardContent>
          </Card>
          <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <Card className={`lg:col-span-2 ${CARD_SHADOW}`}>
              <CardContent>
                <SkeletonBlock className="h-48 w-full" />
              </CardContent>
            </Card>
            <Card className={CARD_SHADOW}>
              <CardContent>
                <SkeletonBlock className="h-48 w-full" />
              </CardContent>
            </Card>
          </div>
        </div>
      )}

      {!isLoading && isError && (
        <Card>
          <CardContent className="flex flex-col items-center gap-3 py-10 text-center">
            <p className="text-sm text-destructive">{errorMessage}</p>
            <Button variant="outline" size="sm" onClick={() => refetch()}>
              <RotateCcw data-icon="inline-start" size={13} />
              Try again
            </Button>
          </CardContent>
        </Card>
      )}

      {!isLoading && !isError && (
        <>
          <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <StatCard
              label="Total Revenue"
              value={formatCurrency(stats.total_revenue)}
              icon={
                <span className="flex h-[26px] w-[26px] items-center justify-center rounded-lg bg-accent text-primary">
                  <Wallet size={13} />
                </span>
              }
            >
              <TrendNote pct={stats.total_revenue_change_pct} suffix="from last month" />
            </StatCard>

            <StatCard
              label="Outstanding"
              value={formatCurrency(stats.outstanding_total)}
              icon={
                <span className="flex h-[26px] w-[26px] items-center justify-center rounded-lg bg-warning-bg text-warning">
                  <Clock size={13} />
                </span>
              }
            >
              <span className="text-xs text-muted-foreground">
                {stats.outstanding_count} invoice{stats.outstanding_count === 1 ? '' : 's'} pending
              </span>
            </StatCard>

            <StatCard
              label="Paid this month"
              value={formatCurrency(stats.paid_this_month)}
              icon={
                <span className="flex h-[26px] w-[26px] items-center justify-center rounded-lg bg-success-bg text-success">
                  <CircleCheck size={13} />
                </span>
              }
            >
              <TrendNote pct={stats.paid_this_month_change_pct} suffix="from last month" />
            </StatCard>

            <StatCard
              label="Active Clients"
              value={stats.active_clients}
              icon={
                <span className="flex h-[26px] w-[26px] items-center justify-center rounded-lg bg-muted text-foreground">
                  <Users size={13} />
                </span>
              }
            >
              <span className="text-xs text-muted-foreground">
                +{stats.active_clients_new_this_month} this month
              </span>
            </StatCard>
          </div>

          <Card className={CARD_SHADOW}>
            <CardHeader className="flex items-center justify-between">
              <div>
                <CardTitle className="flex items-center gap-2">
                  Revenue Overview
                  {isRefreshing && <Loader2 size={13} className="animate-spin text-muted-foreground" />}
                </CardTitle>
                <p className="text-xs text-muted-foreground">
                  Last {RANGE_OPTIONS.find((option) => option.value === range)?.months ?? ''} months
                </p>
              </div>
              <div className="flex gap-1">
                {RANGE_OPTIONS.map((option) => (
                  <button
                    key={option.value}
                    type="button"
                    onClick={() => setRange(option.value)}
                    className={`rounded-lg px-2.5 py-1 text-xs font-medium ${
                      range === option.value
                        ? 'bg-accent text-primary'
                        : 'text-muted-foreground hover:bg-muted'
                    }`}
                  >
                    {option.label}
                  </button>
                ))}
              </div>
            </CardHeader>
            <CardContent>
              {hasRevenue ? (
                <div className={`h-56 transition-opacity ${isRefreshing ? 'opacity-60' : ''}`}>
                  <ResponsiveContainer width="100%" height="100%">
                    <AreaChart data={revenueOverTime} margin={{ left: 0, right: 8, top: 8, bottom: 0 }}>
                      <defs>
                        <linearGradient id="revenueFill" x1="0" y1="0" x2="0" y2="1">
                          <stop offset="0%" stopColor="var(--color-primary)" stopOpacity={0.22} />
                          <stop offset="100%" stopColor="var(--color-primary)" stopOpacity={0} />
                        </linearGradient>
                      </defs>
                      <CartesianGrid vertical={false} stroke="var(--color-border)" />
                      <XAxis
                        dataKey="month"
                        tickFormatter={formatMonthLabel}
                        axisLine={false}
                        tickLine={false}
                        tick={{ fontSize: 12, fill: 'var(--color-muted-foreground)' }}
                      />
                      <YAxis
                        axisLine={false}
                        tickLine={false}
                        tick={{ fontSize: 12, fill: 'var(--color-muted-foreground)' }}
                        tickFormatter={(value) => formatCurrency(value).replace(/\.00$/, '')}
                        width={72}
                      />
                      <Tooltip content={<RevenueTooltip />} />
                      <Area
                        type="monotone"
                        dataKey="total"
                        stroke="var(--color-primary)"
                        strokeWidth={2.5}
                        fill="url(#revenueFill)"
                        dot={{ r: 4, fill: 'var(--color-primary)', stroke: 'var(--color-card)', strokeWidth: 2 }}
                        activeDot={{ r: 5 }}
                      />
                    </AreaChart>
                  </ResponsiveContainer>
                </div>
              ) : (
                <p className="py-10 text-center text-sm text-muted-foreground">
                  No paid invoices in this period yet.
                </p>
              )}
            </CardContent>
          </Card>

          <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <Card className={`lg:col-span-2 py-1.5 ${CARD_SHADOW}`} size="sm">
              <CardContent className="px-0">
                <div className="px-4 pt-2 pb-3">
                  <SectionLabel title="Recent Invoices" actionLabel="View all" actionTo={ROUTES.INVOICES} />
                </div>
                {recentInvoices.length === 0 ? (
                  <p className="px-4 py-8 text-center text-sm text-muted-foreground">No invoices yet.</p>
                ) : (
                  <div className="overflow-x-auto">
                  <table className="w-full border-collapse text-sm">
                    <thead>
                      <tr>
                        <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                          Invoice
                        </th>
                        <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                          Client
                        </th>
                        <th className="border-b border-border px-4 pb-2.5 text-right text-xs font-medium text-muted-foreground">
                          Amount
                        </th>
                        <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                          Status
                        </th>
                        <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                          Date
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      {recentInvoices.map((invoice) => (
                        <tr
                          key={invoice.id}
                          className="cursor-pointer hover:bg-muted/60"
                          onClick={() => navigate(invoiceDetailPath(invoice.id))}
                        >
                          <td className="border-b border-border px-4 py-3 font-medium">
                            {invoice.invoice_number}
                          </td>
                          <td className="border-b border-border px-4 py-3 text-muted-foreground">
                            {invoice.client_name || '—'}
                          </td>
                          <td className="border-b border-border px-4 py-3 text-right font-medium">
                            {formatCurrency(invoice.total)}
                          </td>
                          <td className="border-b border-border px-4 py-3">
                            <StatusBadge status={invoice.status} />
                          </td>
                          <td className="border-b border-border px-4 py-3 text-muted-foreground">
                            {formatDate(invoice.issue_date)}
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                  </div>
                )}
              </CardContent>
            </Card>

            <Card className={CARD_SHADOW}>
              <CardHeader>
                <SectionLabel title="Top Clients" actionLabel="View all" actionTo={ROUTES.CLIENTS} />
              </CardHeader>
              <CardContent>
                {topClients.length > 0 ? (
                  <div className="flex flex-col gap-3.5">
                    {topClients.map((client) => (
                      <div key={client.client_id} className="flex items-center gap-2.5">
                        <span
                          className={`flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-semibold ${avatarColorFor(client.client_id)}`}
                        >
                          {initialsFor(client.client_name)}
                        </span>
                        <div className="min-w-0 flex-1">
                          <p className="truncate text-[13px] font-medium">{client.client_name}</p>
                          <p className="text-[11.5px] text-muted-foreground">
                            {client.invoice_count} invoice{client.invoice_count === 1 ? '' : 's'}
                          </p>
                        </div>
                        <span className="text-[13px] font-semibold">{formatCurrency(client.total)}</span>
                      </div>
                    ))}
                  </div>
                ) : (
                  <div className="flex flex-col items-center gap-1.5 py-8 text-center">
                    <div className="mb-1 flex h-12 w-12 items-center justify-center rounded-full bg-accent text-primary">
                      <LayoutDashboard size={20} />
                    </div>
                    <p className="text-sm text-muted-foreground">No paid invoices yet to rank clients by.</p>
                  </div>
                )}
              </CardContent>
            </Card>
          </div>
        </>
      )}
    </div>
  )
}
