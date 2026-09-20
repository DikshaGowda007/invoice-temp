import { ArrowLeft, Download, Pencil, Trash2 } from 'lucide-react'
import { useState } from 'react'
import { Link } from 'react-router-dom'
import { ConfirmDialog } from '@/components/common/ConfirmDialog'
import { LineItemsCard } from '@/components/common/LineItemsCard'
import { StatusBadge } from '@/components/common/StatusBadge'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { useInvoiceDetail } from '@/features/invoices/hooks/useInvoiceDetail'
import { useLineItems } from '@/features/invoices/hooks/useLineItems'
import { formatCurrency, formatDate } from '@/utils/format'
import { invoiceEditPath, ROUTES } from '@/utils/routePaths'

const STATUS_ACTIONS = {
  DRAFT: [
    { target: 'SENT', label: 'Mark as Sent' },
    { target: 'CANCELLED', label: 'Cancel Invoice' },
  ],
  SENT: [
    { target: 'PAID', label: 'Mark as Paid' },
    { target: 'OVERDUE', label: 'Mark as Overdue' },
    { target: 'CANCELLED', label: 'Cancel Invoice' },
  ],
  OVERDUE: [
    { target: 'PAID', label: 'Mark as Paid' },
    { target: 'CANCELLED', label: 'Cancel Invoice' },
  ],
  PAID: [],
  CANCELLED: [],
}

export default function InvoiceDetailPage() {
  const {
    invoiceId,
    invoice,
    isLoading,
    isError,
    errorMessage,
    updateStatus,
    isUpdatingStatus,
    statusError,
    deleteInvoice,
    isDeleting,
    downloadPdf,
    isDownloading,
    downloadErrorMessage,
  } = useInvoiceDetail()
  const lineItems = useLineItems(invoiceId)
  const [confirmingCancel, setConfirmingCancel] = useState(false)
  const [confirmingDelete, setConfirmingDelete] = useState(false)

  if (isLoading) {
    return (
      <Card className="max-w-3xl">
        <CardContent className="py-10 text-center text-sm text-muted-foreground">Loading invoice…</CardContent>
      </Card>
    )
  }

  if (isError || !invoice) {
    return (
      <Card className="max-w-3xl">
        <CardContent className="py-10 text-center text-sm text-destructive">{errorMessage}</CardContent>
      </Card>
    )
  }

  const availableActions = STATUS_ACTIONS[invoice.status] ?? []
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
          to={ROUTES.INVOICES}
          className="flex items-center gap-1.5 font-medium text-muted-foreground hover:text-foreground"
        >
          <ArrowLeft size={15} />
          Invoices
        </Link>
        <span className="h-4 w-px bg-border" />
        <h1 className="text-lg font-semibold">{invoice.invoice_number}</h1>
        <StatusBadge status={invoice.status} />
      </div>

      <Card>
        <CardContent>
          <div className="flex flex-wrap items-start justify-between gap-4">
            <div className="grid grid-cols-2 gap-x-10 gap-y-3 text-sm">
              <div>
                <p className="text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">Client</p>
                <p className="mt-0.5 font-medium">{invoice.client_name || '—'}</p>
              </div>
              <div>
                <p className="text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">Due date</p>
                <p className="mt-0.5 font-medium">{formatDate(invoice.due_date)}</p>
              </div>
              <div>
                <p className="text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">Issue date</p>
                <p className="mt-0.5 font-medium">{formatDate(invoice.issue_date)}</p>
              </div>
              <div>
                <p className="text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">Total</p>
                <p className="mt-0.5 font-semibold text-primary">{formatCurrency(invoice.total)}</p>
              </div>
            </div>

            <div className="flex flex-col items-end gap-2">
              <div className="flex flex-wrap justify-end gap-2">
                <Button size="sm" variant="outline" disabled={isDownloading} onClick={() => downloadPdf()}>
                  <Download data-icon="inline-start" size={13} />
                  {isDownloading ? 'Preparing…' : 'Download PDF'}
                </Button>
                <Button size="sm" variant="outline" render={<Link to={invoiceEditPath(invoice.id)} />}>
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
              {downloadErrorMessage && <p className="text-xs text-destructive">{downloadErrorMessage}</p>}
            </div>
          </div>
        </CardContent>
      </Card>

      <LineItemsCard
        lineItems={invoice.line_items}
        isFinalized={isFinalized}
        subtotal={invoice.subtotal}
        taxRate={invoice.tax_rate}
        taxAmount={invoice.tax_amount}
        discountAmount={invoice.discount_amount}
        total={invoice.total}
        totalLabel="Total"
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

      <ConfirmDialog
        open={confirmingCancel}
        onOpenChange={setConfirmingCancel}
        title="Cancel this invoice?"
        description="This marks the invoice as cancelled. You won't be able to change its status again after that."
        confirmLabel="Cancel Invoice"
        onConfirm={() => {
          updateStatus('CANCELLED')
          setConfirmingCancel(false)
        }}
        isConfirming={isUpdatingStatus}
      />

      <ConfirmDialog
        open={confirmingDelete}
        onOpenChange={setConfirmingDelete}
        title={`Delete ${invoice.invoice_number}?`}
        description="This can't be undone. The invoice and its line items will be removed from your records."
        confirmLabel="Delete"
        onConfirm={deleteInvoice}
        isConfirming={isDeleting}
      />
    </div>
  )
}
