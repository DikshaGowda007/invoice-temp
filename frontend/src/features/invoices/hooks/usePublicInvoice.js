import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useParams } from 'react-router-dom'
import { invoiceApi } from '@/api/invoice.api'
import { payInvoicePublic } from '@/features/invoices/utils/payInvoicePublic'

export function usePublicInvoice() {
  const { token } = useParams()
  const queryClient = useQueryClient()

  const query = useQuery({
    queryKey: ['public-invoice', token],
    queryFn: () => invoiceApi.publicGet(token).then((res) => res.data.data.invoice),
  })

  const payMutation = useMutation({
    mutationFn: () => payInvoicePublic(token),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['public-invoice', token] }),
  })

  return {
    invoice: query.data,
    isLoading: query.isLoading,
    isError: query.isError,
    errorMessage: query.error?.message ?? 'This payment link is invalid or has expired.',
    payNow: payMutation.mutate,
    isPaying: payMutation.isPending,
    payErrorMessage: payMutation.isError ? (payMutation.error?.message ?? 'Payment failed. Please try again.') : null,
  }
}
