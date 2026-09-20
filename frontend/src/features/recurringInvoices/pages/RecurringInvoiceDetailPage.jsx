import { useMutation } from '@tanstack/react-query'
import { ArrowLeft, Download, FileText, Pencil, Trash2 } from 'lucide-react'
import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { ConfirmDialog } from '@/components/common/ConfirmDialog'
import { LineItemsCard } from '@/components/common/LineItemsCard'
import { StatusBadge } from '@/components/common/StatusBadge'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { downloadInvoicePdf } from '@/features/invoices/utils/downloadInvoicePdf'
import { useRecurringInvoiceDetail } from '@/features/recurringInvoices/hooks/useRecurringInvoiceDetail'
import { useRecurringLineItems } from '@/features/recurringInvoices/hooks/useRecurringLineItems'
import { formatCurrency, formatDate, formatDateTime } from '@/utils/format'
import { invoiceDetailPath, recurringInvoiceEditPath, ROUTES } from '@/utils/routePaths'

const STATUS_ACTIONS = {
  ACTIVE: [
    { target: 'PAUSED', label: 'Pause' },
    { target: 'CANCELLED', label: 'Cancel' },
  ],
  PAUSED: [
    { target: 'ACTIVE', label: 'Resume' },
    { target: 'CANCELLED', label: 'Cancel' },
  ],
  CANCELLED: [],
}

export default function RecurringInvoiceDetailPage() {
  const {
    recurringInvoiceId,
    recurringInvoice,
    isLoading,
    isError,
    errorMessage,
    updateStatus,
    isUpdatingStatus,
    statusError,
    deleteRecurringInvoice,
    isDeleting,
  } = useRecurringInvoiceDetail()
  const lineItems = useRecurringLineItems(recurringInvoiceId)
  const navigate = useNavigate()
  const downloadMutation = useMutation({
    mutationFn: (invoice) => downloadInvoicePdf(invoice.id, `${invoice.invoice_number}.pdf`),
  })
  const [confirmingCancel, setConfirmingCancel] = useState(false)
  const [confirmingDelete, setConfirmingDelete] = useState(false)

  if (isLoading) {
    return (
      <Card className="max-w-3xl">
        <CardContent className="py-10 text-center text-sm text-muted-foreground">Loading…</CardContent>
      </Card>
    )
  }

  if (isError || !recurringInvoice) {
    return (
      <Card className="max-w-3xl">
        <CardContent className="py-10 text-center text-sm text-destructive">{errorMessage}</CardContent>
      </Card>
    )
  }

  const availableActions = STATUS_ACTIONS[recurringInvoice.status] ?? []
  const isFinalized = availableActions.length === 0

  const handleStatusClick = (target) => {
    if (target === 'CANCELLED') {
      setConfirmingCancel(true)
      return
    }
    updateStatus(target)
  }

  return (
    <div className="max-w-3xl">
      <div className="mb-5 flex items-center gap-3 text-sm">
        <Link
          to={ROUTES.RECURRING_INVOICES}
          className="flex items-center gap-1.5 font-medium text-muted-foreground hover:text-foreground"
        >
          <ArrowLeft size={15} />
          Recurring Invoices
        </Link>
        <span className="h-4 w-px bg-border" />
        <h1 className="text-lg font-semibold">{recurringInvoice.client_name}</h1>
        <StatusBadge status={recurringInvoice.status} />
      </div>

      <Card>
        <CardContent>
          <div className="flex flex-wrap items-start justify-between gap-4">
            <div className="grid grid-cols-2 gap-x-10 gap-y-3 text-sm">
              <div>
                <p className="text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">Frequency</p>
                <p className="mt-0.5 font-medium">{recurringInvoice.frequency}</p>
              </div>
              <div>
                <p className="text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">Next run</p>
                <p className="mt-0.5 font-medium">{formatDateTime(recurringInvoice.next_run_at)}</p>
              </div>
              <div>
                <p className="text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">
                  Last generated
                </p>
                <p className="mt-0.5 font-medium">{formatDateTime(recurringInvoice.last_generated_at)}</p>
              </div>
            </div>

            <div className="flex flex-col items-end gap-2">
              <div className="flex flex-wrap justify-end gap-2">
                <Button size="sm" variant="outline" render={<Link to={recurringInvoiceEditPath(recurringInvoice.id)} />}>
                  <Pencil data-icon="inline-start" size={13} />
                  Edit details
                </Button>
                {availableActions.map((action) => (
                  <Button
                    key={action.target}
                    size="sm"
                    variant={action.target === 'CANCELLED' ? 'outline' : 'default'}
                    disabled={isUpdatingStatus}
                    onClick={() => handleStatusClick(action.target)}
                  >
                    {action.label}
                  </Button>
                ))}
                {!isFinalized && (
                  <Button
                    size="sm"
                    variant="ghost"
                    className="text-destructive hover:text-destructive"
                    onClick={() => setConfirmingDelete(true)}
                  >
                    <Trash2 size={14} />
                  </Button>
                )}
              </div>
              {statusError && <p className="text-xs text-destructive">{statusError}</p>}
            </div>
          </div>
        </CardContent>
      </Card>

      <LineItemsCard
        lineItems={recurringInvoice.line_items}
        isFinalized={isFinalized}
        subtotal={recurringInvoice.subtotal}
        taxRate={recurringInvoice.tax_rate}
        taxAmount={recurringInvoice.tax_amount}
        discountAmount={recurringInvoice.discount_amount}
        total={recurringInvoice.total}
        totalLabel="Next invoice total"
        onAdd={lineItems.addLineItem}
        isAdding={lineItems.isAdding}
        addErrorMessage={lineItems.addErrorMessage}
        editingLineItemId={lineItems.editingLineItemId}
        onStartEdit={lineItems.startEditing}
        onCancelEdit={lineItems.cancelEditing}
        onUpdate={lineItems.updateLineItem}
        isUpdating={lineItems.isUpdating}
        onDelete={lineItems.deleteLineItem}
        isDeleting={lineItems.isDeleting}
      />

      <Card className="mt-5">
        <CardContent className="px-0 pt-0">
          <p className="px-4 pt-4 pb-1 text-[13px] font-semibold">Generated invoices</p>
          {(recurringInvoice.generated_invoices ?? []).length === 0 ? (
            <p className="px-4 py-8 text-center text-sm text-muted-foreground">
              No invoices generated yet — the next one will be created automatically around{' '}
              {formatDateTime(recurringInvoice.next_run_at)}.
            </p>
          ) : (
            <table className="mt-2 w-full border-collapse text-sm">
              <thead>
                <tr>
                  <th className="border-b border-border px-4 py-3 text-left text-xs font-medium text-muted-foreground">
                    Invoice
                  </th>
                  <th className="border-b border-border px-4 py-3 text-left text-xs font-medium text-muted-foreground">
                    Status
                  </th>
                  <th className="border-b border-border px-4 py-3 text-left text-xs font-medium text-muted-foreground">
                    Issue date
                  </th>
                  <th className="border-b border-border px-4 py-3 text-right text-xs font-medium text-muted-foreground">
                    Total
                  </th>
                  <th className="border-b border-border px-4 py-3" />
                </tr>
              </thead>
              <tbody>
                {recurringInvoice.generated_invoices.map((invoice) => (
                  <tr
                    key={invoice.id}
                    className="group cursor-pointer transition-colors hover:bg-muted/60"
                    onClick={() => navigate(invoiceDetailPath(invoice.id))}
                  >
                    <td className="border-b border-border px-4 py-3 group-last:border-0">
                      <span className="flex items-center gap-2 font-medium">
                        <FileText size={13} className="text-muted-foreground" />
                        {invoice.invoice_number}
                      </span>
                    </td>
                    <td className="border-b border-border px-4 py-3 group-last:border-0">
                      <StatusBadge status={invoice.status} />
                    </td>
                    <td className="border-b border-border px-4 py-3 text-muted-foreground group-last:border-0">
                      {formatDate(invoice.issue_date)}
                    </td>
                    <td className="border-b border-border px-4 py-3 text-right font-medium group-last:border-0">
                      {formatCurrency(invoice.total)}
                    </td>
                    <td className="border-b border-border px-2 py-3 text-right group-last:border-0">
                      <Button
                        variant="ghost"
                        size="icon-sm"
                        aria-label={`Download ${invoice.invoice_number}`}
                        disabled={downloadMutation.isPending && downloadMutation.variables?.id === invoice.id}
                        onClick={(e) => {
                          e.stopPropagation()
                          downloadMutation.mutate(invoice)
                        }}
                      >
                        <Download size={13} />
                      </Button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
          {downloadMutation.isError && (
            <p className="px-4 pt-2 pb-3 text-sm text-destructive">
              {downloadMutation.error?.message ?? 'Could not download the PDF.'}
            </p>
          )}
        </CardContent>
      </Card>

      <ConfirmDialog
        open={confirmingCancel}
        onOpenChange={setConfirmingCancel}
        title="Cancel this recurring invoice?"
        description="This stops future invoices from being generated. You won't be able to change its status again after that."
        confirmLabel="Cancel"
        onConfirm={() => {
          updateStatus('CANCELLED')
          setConfirmingCancel(false)
        }}
        isConfirming={isUpdatingStatus}
      />

      <ConfirmDialog
        open={confirmingDelete}
        onOpenChange={setConfirmingDelete}
        title={`Delete this recurring invoice for ${recurringInvoice.client_name}?`}
        description="This can't be undone. Invoices already generated from this schedule are kept, but no new ones will be created."
        confirmLabel="Delete"
        onConfirm={deleteRecurringInvoice}
        isConfirming={isDeleting}
      />
    </div>
  )
}
