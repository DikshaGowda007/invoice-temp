import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useNavigate, useParams } from 'react-router-dom'
import { invoiceApi } from '@/api/invoice.api'
import { downloadInvoicePdf } from '@/features/invoices/utils/downloadInvoicePdf'
import { ROUTES } from '@/utils/routePaths'

export function useInvoiceDetail() {
  const { id } = useParams()
  const navigate = useNavigate()
  const queryClient = useQueryClient()

  const query = useQuery({
    queryKey: ['invoices', id],
    queryFn: () => invoiceApi.get(id).then((res) => res.data.data.invoice),
  })

  const invalidate = () => {
    queryClient.invalidateQueries({ queryKey: ['invoices', id] })
    queryClient.invalidateQueries({ queryKey: ['invoices'] })
  }

  const statusMutation = useMutation({
    mutationFn: (status) => invoiceApi.updateStatus({ id, status }),
    onSuccess: invalidate,
  })

  const deleteMutation = useMutation({
    mutationFn: () => invoiceApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['invoices'] })
      navigate(ROUTES.INVOICES)
    },
  })

  const downloadMutation = useMutation({
    mutationFn: () => downloadInvoicePdf(id, `${query.data?.invoice_number ?? 'invoice'}.pdf`),
  })

  return {
    invoiceId: id,
    invoice: query.data,
    isLoading: query.isLoading,
    isError: query.isError,
    errorMessage: query.error?.message ?? 'Something went wrong. Please try again.',
    updateStatus: statusMutation.mutate,
    isUpdatingStatus: statusMutation.isPending,
    statusError: statusMutation.isError
      ? (statusMutation.error?.message ?? 'Could not update status.')
      : null,
    deleteInvoice: deleteMutation.mutate,
    isDeleting: deleteMutation.isPending,
    downloadPdf: downloadMutation.mutate,
    isDownloading: downloadMutation.isPending,
    downloadErrorMessage: downloadMutation.isError
      ? (downloadMutation.error?.message ?? 'Could not download the PDF.')
      : null,
  }
}
