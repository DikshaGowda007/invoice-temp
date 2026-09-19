import { useMutation, useQueryClient } from '@tanstack/react-query'
import { useState } from 'react'
import { invoiceApi } from '@/api/invoice.api'

export function useLineItems(invoiceId) {
  const queryClient = useQueryClient()
  const [editingLineItemId, setEditingLineItemId] = useState(null)

  const invalidate = () => {
    queryClient.invalidateQueries({ queryKey: ['invoices', invoiceId] })
    queryClient.invalidateQueries({ queryKey: ['invoices'] })
  }

  const addMutation = useMutation({
    mutationFn: (values) => invoiceApi.addLineItem({ invoice_id: invoiceId, ...values }),
    onSuccess: invalidate,
  })

  const updateMutation = useMutation({
    mutationFn: ({ lineItemId, values }) =>
      invoiceApi.updateLineItem({ line_item_id: lineItemId, ...values }),
    onSuccess: () => {
      invalidate()
      setEditingLineItemId(null)
    },
  })

  const deleteMutation = useMutation({
    mutationFn: (lineItemId) => invoiceApi.deleteLineItem(lineItemId),
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
