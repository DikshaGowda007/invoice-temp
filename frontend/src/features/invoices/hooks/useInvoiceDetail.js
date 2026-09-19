import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useNavigate, useParams } from 'react-router-dom'
import { invoiceApi } from '@/api/invoice.api'
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
  }
}
