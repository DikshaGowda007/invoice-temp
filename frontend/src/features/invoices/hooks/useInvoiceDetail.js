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

  const downloadMutation = useMutation({
    mutationFn: async () => {
      const dispatchRes = await invoiceApi.download(id)
      const requestId = dispatchRes.data.data.request_id

      const maxAttempts = 30
      const pollIntervalMs = 800

      for (let attempt = 0; attempt < maxAttempts; attempt += 1) {
        await new Promise((resolve) => setTimeout(resolve, pollIntervalMs))

        const statusRes = await invoiceApi.downloadStatus(requestId)
        const status = statusRes.data.data.status

        if (status === 'ready') {
          return invoiceApi.downloadResult(requestId)
        }
        if (status === 'failed') {
          throw new Error('PDF generation failed. Please try again.')
        }
      }

      throw new Error('PDF is taking longer than expected. Please try again shortly.')
    },
    onSuccess: (res) => {
      const filenameMatch = res.headers['content-disposition']?.match(/filename="(.+)"/)
      const filename = filenameMatch?.[1] ?? `${query.data?.invoice_number ?? 'invoice'}.pdf`

      const url = window.URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }))
      const link = document.createElement('a')
      link.href = url
      link.download = filename
      document.body.appendChild(link)
      link.click()
      link.remove()
      window.URL.revokeObjectURL(url)
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
    downloadPdf: downloadMutation.mutate,
    isDownloading: downloadMutation.isPending,
    downloadErrorMessage: downloadMutation.isError
      ? (downloadMutation.error?.message ?? 'Could not download the PDF.')
      : null,
  }
}
