import { zodResolver } from '@hookform/resolvers/zod'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useEffect } from 'react'
import { useForm } from 'react-hook-form'
import { useNavigate, useParams } from 'react-router-dom'
import { z } from 'zod'
import { clientApi } from '@/api/client.api'
import { recurringInvoiceApi } from '@/api/recurringInvoice.api'
import { recurringInvoiceDetailPath } from '@/utils/routePaths'

const recurringInvoiceSchema = z.object({
  client_id: z.coerce.number({ error: 'Select a client' }).int().positive('Select a client'),
  frequency: z.string().min(1, 'Select a frequency'),
  next_run_date: z.string().min(1, 'Next run date is required'),
  next_run_time: z.string().min(1, 'Next run time is required'),
  tax_rate: z.coerce.number().min(0).max(100).optional(),
  discount_amount: z.coerce.number().min(0).optional(),
  notes: z.string().max(1000).optional(),
})

export function useRecurringInvoiceForm() {
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
    resolver: zodResolver(recurringInvoiceSchema),
    defaultValues: {
      next_run_date: new Date().toISOString().slice(0, 10),
      next_run_time: '09:00',
    },
  })

  const values = watch()

  const clientsQuery = useQuery({
    queryKey: ['clients'],
    queryFn: () => clientApi.list().then((res) => res.data.data.clients),
  })

  const recurringInvoiceQuery = useQuery({
    queryKey: ['recurring-invoices', id],
    queryFn: () => recurringInvoiceApi.get(id).then((res) => res.data.data.recurring_invoice),
    enabled: isEditing,
  })

  useEffect(() => {
    if (!recurringInvoiceQuery.data) return

    const nextRunAt = recurringInvoiceQuery.data.next_run_at ?? ''

    reset({
      client_id: recurringInvoiceQuery.data.client_id,
      frequency: recurringInvoiceQuery.data.frequency ?? '',
      next_run_date: nextRunAt.slice(0, 10),
      next_run_time: nextRunAt.slice(11, 16) || '09:00',
      tax_rate: recurringInvoiceQuery.data.tax_rate ?? 0,
      discount_amount: recurringInvoiceQuery.data.discount_amount ?? 0,
      notes: recurringInvoiceQuery.data.notes ?? '',
    })
  }, [recurringInvoiceQuery.data, reset])

  const selectedClient = clientsQuery.data?.find(
    (client) => String(client.id) === String(values.client_id),
  )

  const mutation = useMutation({
    mutationFn: ({ next_run_date, next_run_time, ...values }) => {
      const payload = { ...values, next_run_at: `${next_run_date} ${next_run_time}:00` }
      return isEditing ? recurringInvoiceApi.edit({ id, ...payload }) : recurringInvoiceApi.add(payload)
    },
    onSuccess: (res) => {
      queryClient.invalidateQueries({ queryKey: ['recurring-invoices'] })
      navigate(recurringInvoiceDetailPath(res.data.data.recurring_invoice.id))
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
    isLoadingRecurringInvoice: isEditing && recurringInvoiceQuery.isLoading,
    isLoadError: isEditing && recurringInvoiceQuery.isError,
    handleSubmit: handleFormSubmit((values) => mutation.mutate(values)),
    isPending: mutation.isPending,
    errorMessage: mutation.isError
      ? (mutation.error?.message ?? 'Something went wrong. Please try again.')
      : null,
  }
}
