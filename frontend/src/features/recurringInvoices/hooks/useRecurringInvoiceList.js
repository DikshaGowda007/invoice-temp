import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { recurringInvoiceApi } from '@/api/recurringInvoice.api'
import { recurringInvoiceEditPath } from '@/utils/routePaths'

export function useRecurringInvoiceList() {
  const queryClient = useQueryClient()
  const navigate = useNavigate()
  const [pendingDeleteRecurringInvoice, setPendingDeleteRecurringInvoice] = useState(null)

  const query = useQuery({
    queryKey: ['recurring-invoices'],
    queryFn: () => recurringInvoiceApi.list().then((res) => res.data.data.recurring_invoices),
  })

  const deleteMutation = useMutation({
    mutationFn: (id) => recurringInvoiceApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['recurring-invoices'] })
      setPendingDeleteRecurringInvoice(null)
    },
  })

  const cloneMutation = useMutation({
    mutationFn: (id) => recurringInvoiceApi.clone(id),
    onSuccess: (res) => {
      queryClient.invalidateQueries({ queryKey: ['recurring-invoices'] })
      navigate(recurringInvoiceEditPath(res.data.data.recurring_invoice.id))
    },
  })

  return {
    recurringInvoices: query.data ?? [],
    isLoading: query.isLoading,
    isError: query.isError,
    errorMessage: query.error?.message ?? 'Something went wrong. Please try again.',
    pendingDeleteRecurringInvoice,
    requestDelete: setPendingDeleteRecurringInvoice,
    cancelDelete: () => setPendingDeleteRecurringInvoice(null),
    confirmDelete: () => deleteMutation.mutate(pendingDeleteRecurringInvoice.id),
    isDeleting: deleteMutation.isPending,
    cloneRecurringInvoice: cloneMutation.mutate,
    cloningRecurringInvoiceId: cloneMutation.isPending ? cloneMutation.variables : null,
  }
}
