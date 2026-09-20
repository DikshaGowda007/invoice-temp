import { useMutation, useQueryClient } from '@tanstack/react-query'
import { useState } from 'react'
import { recurringInvoiceApi } from '@/api/recurringInvoice.api'

export function useRecurringLineItems(recurringInvoiceId) {
  const queryClient = useQueryClient()
  const [editingLineItemId, setEditingLineItemId] = useState(null)

  const invalidate = () => {
    queryClient.invalidateQueries({ queryKey: ['recurring-invoices', recurringInvoiceId] })
    queryClient.invalidateQueries({ queryKey: ['recurring-invoices'] })
  }

  const addMutation = useMutation({
    mutationFn: (values) =>
      recurringInvoiceApi.addLineItem({ recurring_invoice_id: recurringInvoiceId, ...values }),
    onSuccess: invalidate,
  })

  const updateMutation = useMutation({
    mutationFn: ({ lineItemId, values }) =>
      recurringInvoiceApi.updateLineItem({ line_item_id: lineItemId, ...values }),
    onSuccess: () => {
      invalidate()
      setEditingLineItemId(null)
    },
  })

  const deleteMutation = useMutation({
    mutationFn: (lineItemId) => recurringInvoiceApi.deleteLineItem(lineItemId),
    onSuccess: invalidate,
  })

  return {
    addLineItem: addMutation.mutate,
    isAdding: addMutation.isPending,
    addErrorMessage: addMutation.isError
      ? (addMutation.error?.message ?? 'Could not add line item.')
      : null,
    editingLineItemId,
    startEditing: setEditingLineItemId,
    cancelEditing: () => setEditingLineItemId(null),
    updateLineItem: (lineItemId, values) => updateMutation.mutate({ lineItemId, values }),
    isUpdating: updateMutation.isPending,
    deleteLineItem: deleteMutation.mutate,
    isDeleting: deleteMutation.isPending,
  }
}
