import { zodResolver } from '@hookform/resolvers/zod'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useEffect } from 'react'
import { useForm } from 'react-hook-form'
import { useNavigate, useParams } from 'react-router-dom'
import { z } from 'zod'
import { clientApi } from '@/api/client.api'
import { invoiceApi } from '@/api/invoice.api'
import { invoiceDetailPath } from '@/utils/routePaths'

const invoiceSchema = z.object({
  client_id: z.coerce.number({ error: 'Select a client' }).int().positive('Select a client'),
  issue_date: z.string().min(1, 'Issue date is required'),
  due_date: z.string().optional(),
  tax_rate: z.coerce.number().min(0).max(100).optional(),
  discount_amount: z.coerce.number().min(0).optional(),
  notes: z.string().max(1000).optional(),
})

export function useInvoiceForm() {
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const { id } = useParams()
  const isEditing = Boolean(id)

  const {
    register,
    handleSubmit: handleFormSubmit,
    reset,
    watch,
    control,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(invoiceSchema),
    defaultValues: { issue_date: new Date().toISOString().slice(0, 10) },
  })

  const values = watch()

  const clientsQuery = useQuery({
    queryKey: ['clients'],
    queryFn: () => clientApi.list().then((res) => res.data.data.clients),
  })

  const invoiceQuery = useQuery({
    queryKey: ['invoices', id],
    queryFn: () => invoiceApi.get(id).then((res) => res.data.data.invoice),
    enabled: isEditing,
  })

  useEffect(() => {
    if (!invoiceQuery.data) return

    reset({
      client_id: invoiceQuery.data.client_id,
      issue_date: invoiceQuery.data.issue_date ?? '',
      due_date: invoiceQuery.data.due_date ?? '',
      tax_rate: invoiceQuery.data.tax_rate ?? 0,
      discount_amount: invoiceQuery.data.discount_amount ?? 0,
      notes: invoiceQuery.data.notes ?? '',
    })
  }, [invoiceQuery.data, reset])

  const selectedClient = clientsQuery.data?.find(
    (client) => String(client.id) === String(values.client_id),
  )

  const mutation = useMutation({
    mutationFn: (values) => (isEditing ? invoiceApi.edit({ id, ...values }) : invoiceApi.add(values)),
    onSuccess: (res) => {
      queryClient.invalidateQueries({ queryKey: ['invoices'] })
      navigate(invoiceDetailPath(res.data.data.invoice.id))
    },
  })

  return {
    isEditing,
    register,
    control,
    errors,
    values,
    selectedClient,
    clients: clientsQuery.data ?? [],
    isLoadingClients: clientsQuery.isLoading,
    isLoadingInvoice: isEditing && invoiceQuery.isLoading,
    isLoadError: isEditing && invoiceQuery.isError,
    handleSubmit: handleFormSubmit((values) => mutation.mutate(values)),
    isPending: mutation.isPending,
    errorMessage: mutation.isError
      ? (mutation.error?.message ?? 'Something went wrong. Please try again.')
      : null,
  }
}
