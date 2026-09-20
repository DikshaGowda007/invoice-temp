import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useNavigate, useParams } from 'react-router-dom'
import { recurringInvoiceApi } from '@/api/recurringInvoice.api'
import { ROUTES } from '@/utils/routePaths'

export function useRecurringInvoiceDetail() {
  const { id } = useParams()
  const navigate = useNavigate()
  const queryClient = useQueryClient()

  const query = useQuery({
    queryKey: ['recurring-invoices', id],
    queryFn: () => recurringInvoiceApi.get(id).then((res) => res.data.data.recurring_invoice),
  })

  const invalidate = () => {
    queryClient.invalidateQueries({ queryKey: ['recurring-invoices', id] })
    queryClient.invalidateQueries({ queryKey: ['recurring-invoices'] })
  }

  const statusMutation = useMutation({
    mutationFn: (status) => recurringInvoiceApi.updateStatus({ id, status }),
    onSuccess: invalidate,
  })

  const deleteMutation = useMutation({
    mutationFn: () => recurringInvoiceApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['recurring-invoices'] })
      navigate(ROUTES.RECURRING_INVOICES)
    },
  })

  return {
    recurringInvoiceId: id,
    recurringInvoice: query.data,
    isLoading: query.isLoading,
    isError: query.isError,
    errorMessage: query.error?.message ?? 'Something went wrong. Please try again.',
    updateStatus: statusMutation.mutate,
    isUpdatingStatus: statusMutation.isPending,
    statusError: statusMutation.isError
      ? (statusMutation.error?.message ?? 'Could not update status.')
      : null,
    deleteRecurringInvoice: deleteMutation.mutate,
    isDeleting: deleteMutation.isPending,
  }
}
