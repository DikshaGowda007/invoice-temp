import { Copy, Pencil, Plus, Repeat, Search, Trash2, X } from 'lucide-react'
import { useMemo, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { ConfirmDialog } from '@/components/common/ConfirmDialog'
import { RECURRING_STATUSES, StatusBadge } from '@/components/common/StatusBadge'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { useRecurringInvoiceList } from '@/features/recurringInvoices/hooks/useRecurringInvoiceList'
import { formatCurrency, formatDateTime } from '@/utils/format'
import { recurringInvoiceEditPath, recurringInvoiceDetailPath, ROUTES } from '@/utils/routePaths'

const STATUS_FILTER_OPTIONS = [
  { value: 'ALL', label: 'All statuses' },
  ...RECURRING_STATUSES.map((status) => ({ value: status, label: status })),
]

export default function RecurringInvoiceListPage() {
  const {
    recurringInvoices,
    isLoading,
    isError,
    errorMessage,
    pendingDeleteRecurringInvoice,
    requestDelete,
    cancelDelete,
    confirmDelete,
    isDeleting,
    cloneRecurringInvoice,
    cloningRecurringInvoiceId,
  } = useRecurringInvoiceList()
  const navigate = useNavigate()
  const [search, setSearch] = useState('')
  const [statusFilter, setStatusFilter] = useState('ALL')

  const hasActiveFilters = statusFilter !== 'ALL' || Boolean(search)

  const clearFilters = () => {
    setSearch('')
    setStatusFilter('ALL')
  }

  const filteredRecurringInvoices = useMemo(() => {
    const query = search.trim().toLowerCase()

    return recurringInvoices.filter((recurringInvoice) => {
      if (query && !recurringInvoice.client_name?.toLowerCase().includes(query)) return false
      if (statusFilter !== 'ALL' && recurringInvoice.status !== statusFilter) return false

      return true
    })
  }, [recurringInvoices, search, statusFilter])

  return (
    <div>
      <div className="mb-6 flex items-center justify-between">
        <h1 className="text-lg font-semibold">Recurring Invoices</h1>
        <Button render={<Link to={ROUTES.RECURRING_INVOICE_NEW} />}>
          <Plus data-icon="inline-start" size={14} />
          New Recurring Invoice
        </Button>
      </div>

      {recurringInvoices.length > 0 && (
        <div className="mb-4 flex flex-wrap items-center gap-2">
          <div className="relative w-64">
            <Search
              size={14}
              className="pointer-events-none absolute top-1/2 left-2.5 -translate-y-1/2 text-muted-foreground"
            />
            <Input
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              placeholder="Search by client..."
              aria-label="Search recurring invoices"
              className="h-8 pl-7"
            />
          </div>

          <Select value={statusFilter} onValueChange={setStatusFilter} items={STATUS_FILTER_OPTIONS}>
            <SelectTrigger aria-label="Filter by status" className="h-8 w-auto gap-1.5 px-3">
              <span className="text-muted-foreground">Status:</span>
              <SelectValue />
            </SelectTrigger>
            <SelectContent align="start" alignItemWithTrigger={false}>
              {STATUS_FILTER_OPTIONS.map((option) => (
                <SelectItem key={option.value} value={option.value}>
                  {option.label}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>

          {hasActiveFilters && (
            <Button variant="ghost" size="sm" onClick={clearFilters} className="text-muted-foreground">
              <X data-icon="inline-start" size={13} />
              Clear
            </Button>
          )}
        </div>
      )}

      {isLoading && (
        <Card size="sm" className="py-1.5">
          <CardContent className="animate-pulse px-4">
            {[0, 1, 2].map((i) => (
              <div key={i} className="flex items-center gap-3 border-b border-border py-3.5 last:border-0">
                <div className="h-3.5 w-24 rounded bg-muted" />
                <div className="h-3.5 w-32 flex-1 rounded bg-muted" />
                <div className="h-3.5 w-16 rounded bg-muted" />
              </div>
            ))}
          </CardContent>
        </Card>
      )}

      {!isLoading && isError && (
        <Card>
          <CardContent className="py-10 text-center">
            <p className="text-sm text-destructive">{errorMessage}</p>
          </CardContent>
        </Card>
      )}

      {!isLoading && !isError && recurringInvoices.length === 0 && (
        <Card>
          <CardContent className="flex flex-col items-center gap-1.5 py-14 text-center">
            <div className="mb-2 flex h-14 w-14 items-center justify-center rounded-full bg-accent text-primary">
              <Repeat size={26} strokeWidth={1.7} />
            </div>
            <p className="text-[15px] font-semibold">No recurring invoices yet</p>
            <p className="max-w-xs text-[13px] leading-relaxed text-muted-foreground">
              Set up a recurring schedule to automatically bill a client on a regular basis.
            </p>
            <Button render={<Link to={ROUTES.RECURRING_INVOICE_NEW} />} className="mt-2.5">
              <Plus data-icon="inline-start" size={14} />
              New Recurring Invoice
            </Button>
          </CardContent>
        </Card>
      )}

      {!isLoading && !isError && recurringInvoices.length > 0 && (
        <Card size="sm" className="py-1.5">
          <CardContent className="px-0">
            {filteredRecurringInvoices.length === 0 ? (
              <p className="px-4 py-8 text-center text-sm text-muted-foreground">
                No recurring invoices match your filters.
              </p>
            ) : (
              <table className="w-full border-collapse text-sm">
                <thead>
                  <tr>
                    <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                      Client
                    </th>
                    <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                      Frequency
                    </th>
                    <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                      Status
                    </th>
                    <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                      Next run
                    </th>
                    <th className="border-b border-border px-4 pb-2.5 text-right text-xs font-medium text-muted-foreground">
                      Total
                    </th>
                    <th className="border-b border-border px-4 pb-2.5" />
                  </tr>
                </thead>
                <tbody>
                  {filteredRecurringInvoices.map((recurringInvoice) => (
                    <tr
                      key={recurringInvoice.id}
                      className="group cursor-pointer transition-colors hover:bg-muted/60"
                      onClick={() => navigate(recurringInvoiceDetailPath(recurringInvoice.id))}
                    >
                      <td className="border-b border-border px-4 py-3 group-last:border-0">
                        <span className="font-medium">{recurringInvoice.client_name || '—'}</span>
                      </td>
                      <td className="border-b border-border px-4 py-3 text-muted-foreground group-last:border-0">
                        {recurringInvoice.frequency}
                      </td>
                      <td className="border-b border-border px-4 py-3 group-last:border-0">
                        <StatusBadge status={recurringInvoice.status} />
                      </td>
                      <td className="border-b border-border px-4 py-3 text-muted-foreground group-last:border-0">
                        {formatDateTime(recurringInvoice.next_run_at)}
                      </td>
                      <td className="border-b border-border px-4 py-3 text-right font-medium group-last:border-0">
                        {formatCurrency(recurringInvoice.total)}
                      </td>
                      <td className="border-b border-border px-2 py-3 text-right group-last:border-0">
                        <div className="flex justify-end gap-0.5">
                          <Button
                            variant="ghost"
                            size="icon-sm"
                            aria-label={`Clone recurring invoice for ${recurringInvoice.client_name}`}
                            disabled={cloningRecurringInvoiceId === recurringInvoice.id}
                            onClick={(e) => {
                              e.stopPropagation()
                              cloneRecurringInvoice(recurringInvoice.id)
                            }}
                          >
                            <Copy size={14} />
                          </Button>
                          <Button
                            variant="ghost"
                            size="icon-sm"
                            onClick={(e) => e.stopPropagation()}
                            render={
                              <Link
                                to={recurringInvoiceEditPath(recurringInvoice.id)}
                                aria-label={`Edit recurring invoice for ${recurringInvoice.client_name}`}
                              />
                            }
                          >
                            <Pencil size={14} />
                          </Button>
                          <Button
                            variant="ghost"
                            size="icon-sm"
                            aria-label={`Delete recurring invoice for ${recurringInvoice.client_name}`}
                            onClick={(e) => {
                              e.stopPropagation()
                              requestDelete(recurringInvoice)
                            }}
                          >
                            <Trash2 size={14} />
                          </Button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </CardContent>
        </Card>
      )}

      <ConfirmDialog
        open={pendingDeleteRecurringInvoice != null}
        onOpenChange={(open) => !open && cancelDelete()}
        title={`Delete this recurring invoice for ${pendingDeleteRecurringInvoice?.client_name}?`}
        description="This can't be undone. Invoices already generated from this schedule are kept, but no new ones will be created."
        confirmLabel="Delete"
        onConfirm={confirmDelete}
        isConfirming={isDeleting}
      />
    </div>
  )
}
