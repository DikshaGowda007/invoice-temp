import { zodResolver } from '@hookform/resolvers/zod'
import { ArrowLeft, Check, Pencil, Plus, Trash2, X } from 'lucide-react'
import { Link } from 'react-router-dom'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import { ConfirmDialog } from '@/components/common/ConfirmDialog'
import { StatusBadge } from '@/components/common/StatusBadge'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { useInvoiceDetail } from '@/features/invoices/hooks/useInvoiceDetail'
import { useLineItems } from '@/features/invoices/hooks/useLineItems'
import { useState } from 'react'
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

const lineItemSchema = z.object({
  description: z.string().min(1, 'Required').max(255),
  quantity: z.coerce.number().min(0.01, 'Must be > 0'),
  unit_price: z.coerce.number().min(0, 'Must be ≥ 0'),
})

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
            </div>
          </div>
        </CardContent>
      </Card>

      <Card className="mt-5">
        <CardContent className="px-0 pt-0">
          <table className="w-full border-collapse text-sm">
            <thead>
              <tr>
                <th className="border-b border-border px-4 py-3 text-left text-xs font-medium text-muted-foreground">
                  Description
                </th>
                <th className="w-20 border-b border-border px-4 py-3 text-right text-xs font-medium text-muted-foreground">
                  Qty
                </th>
                <th className="w-28 border-b border-border px-4 py-3 text-right text-xs font-medium text-muted-foreground">
                  Unit price
                </th>
                <th className="w-28 border-b border-border px-4 py-3 text-right text-xs font-medium text-muted-foreground">
                  Line total
                </th>
                {!isFinalized && <th className="w-20 border-b border-border px-4 py-3" />}
              </tr>
            </thead>
            <tbody>
              {(invoice.line_items ?? []).length === 0 && (
                <tr>
                  <td
                    colSpan={isFinalized ? 4 : 5}
                    className="px-4 py-8 text-center text-sm text-muted-foreground"
                  >
                    No line items yet. Add one below.
                  </td>
                </tr>
              )}
              {(invoice.line_items ?? []).map((lineItem) =>
                lineItems.editingLineItemId === lineItem.id ? (
                  <EditLineItemRow
                    key={lineItem.id}
                    lineItem={lineItem}
                    onCancel={lineItems.cancelEditing}
                    onSave={(values) => lineItems.updateLineItem(lineItem.id, values)}
                    isSaving={lineItems.isUpdating}
                  />
                ) : (
                  <tr key={lineItem.id} className="group">
                    <td className="border-b border-border px-4 py-3 group-last:border-0">
                      {lineItem.description}
                    </td>
                    <td className="border-b border-border px-4 py-3 text-right text-muted-foreground group-last:border-0">
                      {lineItem.quantity}
                    </td>
                    <td className="border-b border-border px-4 py-3 text-right text-muted-foreground group-last:border-0">
                      {formatCurrency(lineItem.unit_price)}
                    </td>
                    <td className="border-b border-border px-4 py-3 text-right font-medium group-last:border-0">
                      {formatCurrency(lineItem.line_total)}
                    </td>
                    {!isFinalized && (
                      <td className="border-b border-border px-2 py-3 text-right group-last:border-0">
                        <div className="flex justify-end gap-0.5">
                          <Button
                            variant="ghost"
                            size="icon-sm"
                            aria-label={`Edit ${lineItem.description}`}
                            onClick={() => lineItems.startEditing(lineItem.id)}
                          >
                            <Pencil size={13} />
                          </Button>
                          <Button
                            variant="ghost"
                            size="icon-sm"
                            aria-label={`Remove ${lineItem.description}`}
                            disabled={lineItems.isDeleting}
                            onClick={() => lineItems.deleteLineItem(lineItem.id)}
                          >
                            <Trash2 size={13} />
                          </Button>
                        </div>
                      </td>
                    )}
                  </tr>
                ),
              )}
            </tbody>
            {!isFinalized && (
              <tfoot>
                <AddLineItemRow onAdd={lineItems.addLineItem} isAdding={lineItems.isAdding} />
              </tfoot>
            )}
          </table>
          {lineItems.addErrorMessage && (
            <p className="px-4 pt-2 pb-3 text-sm text-destructive">{lineItems.addErrorMessage}</p>
          )}

          <div className="flex justify-end border-t border-border px-4 py-4">
            <div className="w-56 space-y-1.5 text-sm">
              <div className="flex justify-between text-muted-foreground">
                <span>Subtotal</span>
                <span>{formatCurrency(invoice.subtotal)}</span>
              </div>
              <div className="flex justify-between text-muted-foreground">
                <span>Discount</span>
                <span>−{formatCurrency(invoice.discount_amount)}</span>
              </div>
              <div className="flex justify-between text-muted-foreground">
                <span>Tax ({invoice.tax_rate}%)</span>
                <span>{formatCurrency(invoice.tax_amount)}</span>
              </div>
              <div className="mt-1.5 flex justify-between border-t border-border pt-1.5 text-[15px] font-semibold">
                <span>Total</span>
                <span className="text-primary">{formatCurrency(invoice.total)}</span>
              </div>
            </div>
          </div>
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

function AddLineItemRow({ onAdd, isAdding }) {
  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(lineItemSchema),
    defaultValues: { description: '', quantity: 1, unit_price: '' },
  })

  const submit = handleSubmit((values) => {
    onAdd(values)
    reset({ description: '', quantity: 1, unit_price: '' })
  })

  return (
    <tr>
      <td className="px-4 py-3">
        <Input placeholder="Item description" {...register('description')} />
        {errors.description && <p className="mt-1 text-xs text-destructive">{errors.description.message}</p>}
      </td>
      <td className="px-4 py-3">
        <Input type="number" step="0.01" min="0.01" className="text-right" {...register('quantity')} />
      </td>
      <td className="px-4 py-3">
        <Input type="number" step="0.01" min="0" placeholder="0" className="text-right" {...register('unit_price')} />
      </td>
      <td />
      <td className="px-2 py-3 text-right">
        <Button size="icon-sm" disabled={isAdding} onClick={submit} aria-label="Add line item">
          <Plus size={14} />
        </Button>
      </td>
    </tr>
  )
}

function EditLineItemRow({ lineItem, onCancel, onSave, isSaving }) {
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(lineItemSchema),
    defaultValues: {
      description: lineItem.description,
      quantity: lineItem.quantity,
      unit_price: lineItem.unit_price,
    },
  })

  const submit = handleSubmit((values) => onSave(values))

  return (
    <tr>
      <td className="px-4 py-3">
        <Input {...register('description')} />
        {errors.description && <p className="mt-1 text-xs text-destructive">{errors.description.message}</p>}
      </td>
      <td className="px-4 py-3">
        <Input type="number" step="0.01" min="0.01" className="text-right" {...register('quantity')} />
      </td>
      <td className="px-4 py-3">
        <Input type="number" step="0.01" min="0" className="text-right" {...register('unit_price')} />
      </td>
      <td />
      <td className="px-2 py-3">
        <div className="flex justify-end gap-0.5">
          <Button size="icon-sm" disabled={isSaving} onClick={submit} aria-label="Save line item">
            <Check size={14} />
          </Button>
          <Button variant="ghost" size="icon-sm" onClick={onCancel} aria-label="Cancel edit">
            <X size={14} />
          </Button>
        </div>
      </td>
    </tr>
  )
}
