import { zodResolver } from '@hookform/resolvers/zod'
import { Check, Pencil, Plus, Receipt, Trash2, X } from 'lucide-react'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { formatCurrency } from '@/utils/format'

const lineItemSchema = z.object({
  description: z.string().min(1, 'Required').max(255),
  quantity: z.coerce.number().min(0.01, 'Must be > 0'),
  unit_price: z.coerce.number().min(0.01, 'Must be > 0'),
})

export function LineItemsCard({
  lineItems,
  isFinalized,
  subtotal,
  taxRate,
  taxAmount,
  discountAmount,
  total,
  totalLabel = 'Total',
  onAdd,
  isAdding,
  addErrorMessage,
  editingLineItemId,
  onStartEdit,
  onCancelEdit,
  onUpdate,
  isUpdating,
  onDelete,
  isDeleting,
}) {
  return (
    <Card className="mt-5">
      <CardContent className="px-0 pt-0">
        <table className="w-full border-collapse text-sm">
          <thead>
            <tr>
              <th className="border-b border-border px-4 py-3 text-left text-xs font-medium text-muted-foreground">
                Description
              </th>
              <th className="w-28 border-b border-border px-4 py-3 text-right text-xs font-medium text-muted-foreground">
                Qty
              </th>
              <th className="w-36 border-b border-border px-4 py-3 text-right text-xs font-medium text-muted-foreground">
                Unit price
              </th>
              <th className="w-28 border-b border-border px-4 py-3 text-right text-xs font-medium text-muted-foreground">
                Line total
              </th>
              {!isFinalized && <th className="w-20 border-b border-border px-4 py-3" />}
            </tr>
          </thead>
          <tbody>
            {(lineItems ?? []).length === 0 && (
              <tr>
                <td colSpan={isFinalized ? 4 : 5} className="px-4 py-8 text-center text-sm text-muted-foreground">
                  No line items yet. Add one below.
                </td>
              </tr>
            )}
            {(lineItems ?? []).map((lineItem) =>
              editingLineItemId === lineItem.id ? (
                <EditLineItemRow
                  key={lineItem.id}
                  lineItem={lineItem}
                  onCancel={onCancelEdit}
                  onSave={(values) => onUpdate(lineItem.id, values)}
                  isSaving={isUpdating}
                />
              ) : (
                <tr key={lineItem.id} className="group">
                  <td className="border-b border-border px-4 py-3 group-last:border-0">
                    <div className="flex items-center gap-2">
                      <span className="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-accent text-primary">
                        <Receipt size={11} />
                      </span>
                      <span>{lineItem.description}</span>
                    </div>
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
                          onClick={() => onStartEdit(lineItem.id)}
                        >
                          <Pencil size={13} />
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon-sm"
                          aria-label={`Remove ${lineItem.description}`}
                          disabled={isDeleting}
                          onClick={() => onDelete(lineItem.id)}
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
              <AddLineItemRow onAdd={onAdd} isAdding={isAdding} />
            </tfoot>
          )}
        </table>
        {addErrorMessage && <p className="px-4 pt-2 pb-3 text-sm text-destructive">{addErrorMessage}</p>}

        <div className="flex justify-end border-t border-border px-4 py-4">
          <div className="w-56 space-y-1.5 text-sm">
            <div className="flex justify-between text-muted-foreground">
              <span>Subtotal</span>
              <span>{formatCurrency(subtotal)}</span>
            </div>
            <div className="flex justify-between text-muted-foreground">
              <span>Discount</span>
              <span>−{formatCurrency(discountAmount)}</span>
            </div>
            <div className="flex justify-between text-muted-foreground">
              <span>Tax ({taxRate}%)</span>
              <span>{formatCurrency(taxAmount)}</span>
            </div>
            <div className="mt-1.5 flex justify-between border-t border-border pt-1.5 text-[15px] font-semibold">
              <span>{totalLabel}</span>
              <span className="text-primary">{formatCurrency(total)}</span>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
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
      <td className="px-1.5 py-3">
        <Input type="number" step="0.01" min="0.01" className="text-right" {...register('quantity')} />
        {errors.quantity && <p className="mt-1 text-xs text-destructive">{errors.quantity.message}</p>}
      </td>
      <td className="px-1.5 py-3">
        <Input type="number" step="0.01" min="0.01" placeholder="0" className="text-right" {...register('unit_price')} />
        {errors.unit_price && <p className="mt-1 text-xs text-destructive">{errors.unit_price.message}</p>}
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
      <td className="px-1.5 py-3">
        <Input type="number" step="0.01" min="0.01" className="text-right" {...register('quantity')} />
        {errors.quantity && <p className="mt-1 text-xs text-destructive">{errors.quantity.message}</p>}
      </td>
      <td className="px-1.5 py-3">
        <Input type="number" step="0.01" min="0.01" className="text-right" {...register('unit_price')} />
        {errors.unit_price && <p className="mt-1 text-xs text-destructive">{errors.unit_price.message}</p>}
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
