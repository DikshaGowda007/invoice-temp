import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { invoiceApi } from '@/api/invoice.api'
import { downloadInvoicePdf } from '@/features/invoices/utils/downloadInvoicePdf'
import { invoiceEditPath } from '@/utils/routePaths'

export function useInvoiceList() {
  const queryClient = useQueryClient()
  const navigate = useNavigate()
  const [pendingDeleteInvoice, setPendingDeleteInvoice] = useState(null)

  const query = useQuery({
    queryKey: ['invoices'],
    queryFn: () => invoiceApi.list().then((res) => res.data.data.invoices),
  })

  const deleteMutation = useMutation({
    mutationFn: (id) => invoiceApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['invoices'] })
      setPendingDeleteInvoice(null)
    },
  })

  const cloneMutation = useMutation({
    mutationFn: (id) => invoiceApi.clone(id),
    onSuccess: (res) => {
      queryClient.invalidateQueries({ queryKey: ['invoices'] })
      navigate(invoiceEditPath(res.data.data.invoice.id))
    },
  })

  const downloadMutation = useMutation({
    mutationFn: (invoice) => downloadInvoicePdf(invoice.id, `${invoice.invoice_number}.pdf`),
  })

  return {
    invoices: query.data ?? [],
    isLoading: query.isLoading,
    isError: query.isError,
    errorMessage: query.error?.message ?? 'Something went wrong. Please try again.',
    pendingDeleteInvoice,
    requestDelete: setPendingDeleteInvoice,
    cancelDelete: () => setPendingDeleteInvoice(null),
    confirmDelete: () => deleteMutation.mutate(pendingDeleteInvoice.id),
    isDeleting: deleteMutation.isPending,
    cloneInvoice: cloneMutation.mutate,
    cloningInvoiceId: cloneMutation.isPending ? cloneMutation.variables : null,
    downloadInvoice: downloadMutation.mutate,
    downloadingInvoiceId: downloadMutation.isPending ? downloadMutation.variables?.id : null,
    downloadErrorMessage: downloadMutation.isError
      ? (downloadMutation.error?.message ?? 'Could not download the PDF.')
      : null,
  }
}
