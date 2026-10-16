import {
  AlertTriangle,
  ArrowLeft,
  ArrowRightLeft,
  CheckCircle2,
  Download,
  FilePlus,
  ListChecks,
  ListMinus,
  ListPlus,
  Mail,
  MailWarning,
  Pencil,
  Trash2,
} from 'lucide-react'
import { useState } from 'react'
import { Link } from 'react-router-dom'
import { ConfirmDialog } from '@/components/common/ConfirmDialog'
import { LineItemsCard } from '@/components/common/LineItemsCard'
import { StatusBadge } from '@/components/common/StatusBadge'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { useInvoiceDetail } from '@/features/invoices/hooks/useInvoiceDetail'
import { useLineItems } from '@/features/invoices/hooks/useLineItems'
import { formatCurrency, formatDate, formatDateTime } from '@/utils/format'
import { invoiceEditPath, ROUTES } from '@/utils/routePaths'

const ACTIVITY_STYLES = {
  STATUS_CHANGED: { icon: ArrowRightLeft, badge: 'bg-accent text-primary' },
  PAID: { icon: CheckCircle2, badge: 'bg-success-bg text-success' },
  RECEIPT_SENT: { icon: Mail, badge: 'bg-info-bg text-info' },
  RECEIPT_FAILED: { icon: MailWarning, badge: 'bg-danger-bg text-danger' },
  INVOICE_EMAILED: { icon: Mail, badge: 'bg-info-bg text-info' },
  INVOICE_EMAIL_FAILED: { icon: MailWarning, badge: 'bg-danger-bg text-danger' },
  OVERDUE_REMINDER_SENT: { icon: AlertTriangle, badge: 'bg-danger-bg text-danger' },
  OVERDUE_REMINDER_FAILED: { icon: MailWarning, badge: 'bg-danger-bg text-danger' },
  CREATED: { icon: FilePlus, badge: 'bg-accent text-primary' },
  EDITED: { icon: Pencil, badge: 'bg-muted text-muted-foreground' },
  DELETED: { icon: Trash2, badge: 'bg-danger-bg text-danger' },
  LINE_ITEM_ADDED: { icon: ListPlus, badge: 'bg-accent text-primary' },
  LINE_ITEM_UPDATED: { icon: ListChecks, badge: 'bg-muted text-muted-foreground' },
  LINE_ITEM_DELETED: { icon: ListMinus, badge: 'bg-muted text-muted-foreground' },
}

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

      <Card className="mt-5">
        <CardContent className="px-0 pt-0">
          <p className="px-4 pt-4 pb-1 text-[13px] font-semibold">Activity</p>
          {(invoice.activity ?? []).length === 0 ? (
            <p className="px-4 py-8 text-center text-sm text-muted-foreground">No activity yet.</p>
          ) : (
            <ul className="px-4 pt-2 pb-4">
              {invoice.activity.map((entry, index) => {
                const meta = ACTIVITY_STYLES[entry.type] ?? ACTIVITY_STYLES.STATUS_CHANGED
                const Icon = meta.icon
                const isLast = index === invoice.activity.length - 1

                return (
                  <li key={`${entry.type}-${entry.created_at}-${index}`} className="flex gap-3">
                    <div className="flex flex-col items-center">
                      <span className={`flex h-6 w-6 shrink-0 items-center justify-center rounded-full ${meta.badge}`}>
                        <Icon size={12} />
                      </span>
                      {!isLast && <span className="mt-1 w-px flex-1 bg-border" />}
                    </div>
                    <div className={isLast ? 'pb-0.5' : 'pb-5'}>
                      <p className="text-sm font-medium">{entry.description}</p>
                      <p className="mt-0.5 text-xs text-muted-foreground">{formatDateTime(entry.created_at)}</p>
                    </div>
                  </li>
                )
              })}
            </ul>
          )}
        </CardContent>
      </Card>

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
