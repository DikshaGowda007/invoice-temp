import { Copy, FileText, Plus, Search, Trash2, X } from 'lucide-react'
import { useMemo, useState } from 'react'
import { Link } from 'react-router-dom'
import { ConfirmDialog } from '@/components/common/ConfirmDialog'
import { INVOICE_STATUSES, STATUS_ACCENT_STYLES, StatusBadge } from '@/components/common/StatusBadge'
import { DateRangePickerField } from '@/components/forms/DateRangePickerField'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { useInvoiceList } from '@/features/invoices/hooks/useInvoiceList'
import { formatCurrency, formatDate } from '@/utils/format'
import { invoiceDetailPath, ROUTES } from '@/utils/routePaths'

const STATUS_FILTER_OPTIONS = [
  { value: 'ALL', label: 'All statuses' },
  ...INVOICE_STATUSES.map((status) => ({ value: status, label: status })),
]

export default function InvoiceListPage() {
  const {
    invoices,
    isLoading,
    isError,
    errorMessage,
    pendingDeleteInvoice,
    requestDelete,
    cancelDelete,
    confirmDelete,
    isDeleting,
    cloneInvoice,
    cloningInvoiceId,
  } = useInvoiceList()
  const [search, setSearch] = useState('')
  const [statusFilter, setStatusFilter] = useState('ALL')
  const [dueFrom, setDueFrom] = useState('')
  const [dueTo, setDueTo] = useState('')

  const [prevDeleteInvoice, setPrevDeleteInvoice] = useState(pendingDeleteInvoice)
  const [deleteTargetNumber, setDeleteTargetNumber] = useState(pendingDeleteInvoice?.invoice_number ?? null)
  if (pendingDeleteInvoice !== prevDeleteInvoice) {
    setPrevDeleteInvoice(pendingDeleteInvoice)
    if (pendingDeleteInvoice) setDeleteTargetNumber(pendingDeleteInvoice.invoice_number)
  }

  const hasActiveFilters = statusFilter !== 'ALL' || Boolean(dueFrom) || Boolean(dueTo)

  const clearFilters = () => {
    setStatusFilter('ALL')
    setDueFrom('')
    setDueTo('')
  }

  const setDueRange = (from, to) => {
    setDueFrom(from)
    setDueTo(to)
  }

  const filteredInvoices = useMemo(() => {
    const query = search.trim().toLowerCase()

    return invoices.filter((invoice) => {
      if (query) {
        const matchesQuery =
          invoice.invoice_number?.toLowerCase().includes(query) ||
          invoice.client_name?.toLowerCase().includes(query)
        if (!matchesQuery) return false
      }

      if (statusFilter !== 'ALL' && invoice.status !== statusFilter) return false
      if (dueFrom && (!invoice.due_date || invoice.due_date < dueFrom)) return false
      if (dueTo && (!invoice.due_date || invoice.due_date > dueTo)) return false

      return true
    })
  }, [invoices, search, statusFilter, dueFrom, dueTo])

  return (
    <div>
      <div className="mb-6 flex items-center justify-between">
        <h1 className="text-lg font-semibold">Invoices</h1>
        <Button render={<Link to={ROUTES.INVOICE_NEW} />}>
          <Plus data-icon="inline-start" size={14} />
          New Invoice
        </Button>
      </div>

      {invoices.length > 0 && (
        <div className="mb-4 flex flex-wrap items-center gap-2">
          <div className="relative w-64">
            <Search
              size={14}
              className="pointer-events-none absolute top-1/2 left-2.5 -translate-y-1/2 text-muted-foreground"
            />
            <Input
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              placeholder="Search invoices..."
              aria-label="Search invoices"
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

          <DateRangePickerField label="Due" from={dueFrom} to={dueTo} onChange={setDueRange} />

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

      {!isLoading && !isError && invoices.length === 0 && (
        <Card>
          <CardContent className="flex flex-col items-center gap-1.5 py-14 text-center">
            <div className="mb-2 flex h-14 w-14 items-center justify-center rounded-full bg-accent text-primary">
              <FileText size={26} strokeWidth={1.7} />
            </div>
            <p className="text-[15px] font-semibold">No invoices yet</p>
            <p className="max-w-xs text-[13px] leading-relaxed text-muted-foreground">
              Create your first invoice to start billing clients.
            </p>
            <Button render={<Link to={ROUTES.INVOICE_NEW} />} className="mt-2.5">
              <Plus data-icon="inline-start" size={14} />
              New Invoice
            </Button>
          </CardContent>
        </Card>
      )}

      {!isLoading && !isError && invoices.length > 0 && (
        <Card size="sm" className="py-1.5">
          <CardContent className="px-0">
            {filteredInvoices.length === 0 ? (
              <p className="px-4 py-8 text-center text-sm text-muted-foreground">
                No invoices match your filters.
              </p>
            ) : (
              <table className="w-full border-collapse text-sm">
                <thead>
                  <tr>
                    <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                      Invoice
                    </th>
                    <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                      Client
                    </th>
                    <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                      Status
                    </th>
                    <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                      Due
                    </th>
                    <th className="border-b border-border px-4 pb-2.5 text-right text-xs font-medium text-muted-foreground">
                      Total
                    </th>
                    <th className="border-b border-border px-4 pb-2.5" />
                  </tr>
                </thead>
                <tbody>
                  {filteredInvoices.map((invoice) => (
                    <tr key={invoice.id} className="group">
                      <td
                        className={`border-b border-b-border border-l-2 px-3.5 py-3 group-last:border-b-0 ${
                          STATUS_ACCENT_STYLES[invoice.status] ?? 'border-l-transparent'
                        }`}
                      >
                        <Link
                          to={invoiceDetailPath(invoice.id)}
                          className="font-medium hover:text-primary hover:underline"
                        >
                          {invoice.invoice_number}
                        </Link>
                      </td>
                      <td className="border-b border-border px-4 py-3 text-muted-foreground group-last:border-0">
                        {invoice.client_name || '—'}
                      </td>
                      <td className="border-b border-border px-4 py-3 group-last:border-0">
                        <StatusBadge status={invoice.status} />
                      </td>
                      <td className="border-b border-border px-4 py-3 text-muted-foreground group-last:border-0">
                        {formatDate(invoice.due_date)}
                      </td>
                      <td className="border-b border-border px-4 py-3 text-right font-medium group-last:border-0">
                        {formatCurrency(invoice.total)}
                      </td>
                      <td className="border-b border-border px-2 py-3 text-right group-last:border-0">
                        <Button
                          variant="ghost"
                          size="icon-sm"
                          aria-label={`Clone ${invoice.invoice_number}`}
                          disabled={cloningInvoiceId === invoice.id}
                          onClick={() => cloneInvoice(invoice.id)}
                        >
                          <Copy size={14} />
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon-sm"
                          aria-label={`Delete ${invoice.invoice_number}`}
                          onClick={() => requestDelete(invoice)}
                        >
                          <Trash2 size={14} />
                        </Button>
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
        open={pendingDeleteInvoice != null}
        onOpenChange={(open) => !open && cancelDelete()}
        title={`Delete ${deleteTargetNumber}?`}
        description="This can't be undone. The invoice and its line items will be removed from your records."
        confirmLabel="Delete"
        onConfirm={confirmDelete}
        isConfirming={isDeleting}
      />
    </div>
  )
}
