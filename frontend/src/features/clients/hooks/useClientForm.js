import { zodResolver } from '@hookform/resolvers/zod'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useEffect } from 'react'
import { useForm } from 'react-hook-form'
import { useNavigate, useParams } from 'react-router-dom'
import { z } from 'zod'
import { clientApi } from '@/api/client.api'
import { ROUTES } from '@/utils/routePaths'

const clientSchema = z.object({
  name: z.string().min(1, 'Name is required').max(255),
  email: z.union([z.email('Enter a valid email'), z.literal('')]).optional(),
  phone: z.string().max(20).optional(),
  address: z.string().max(500).optional(),
  notes: z.string().max(1000).optional(),
})

export function useClientForm() {
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const { id } = useParams()
  const isEditing = Boolean(id)

  const {
    register,
    handleSubmit: handleFormSubmit,
    reset,
    watch,
    formState: { errors },
  } = useForm({ resolver: zodResolver(clientSchema) })

  const values = watch()

  const clientQuery = useQuery({
    queryKey: ['clients', id],
    queryFn: () => clientApi.get(id).then((res) => res.data.data.client),
    enabled: isEditing,
  })

  useEffect(() => {
    if (!clientQuery.data) return

    reset({
      name: clientQuery.data.name ?? '',
      email: clientQuery.data.email ?? '',
      phone: clientQuery.data.phone ?? '',
      address: clientQuery.data.address ?? '',
      notes: clientQuery.data.notes ?? '',
    })
  }, [clientQuery.data, reset])

  const mutation = useMutation({
    mutationFn: (values) => (isEditing ? clientApi.edit({ id, ...values }) : clientApi.add(values)),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['clients'] })
      navigate(ROUTES.CLIENTS)
    },
  })

  return {
    isEditing,
    register,
    errors,
    values,
    handleSubmit: handleFormSubmit((formValues) => mutation.mutate(formValues)),
    isPending: mutation.isPending,
    isLoadingClient: isEditing && clientQuery.isLoading,
    isLoadError: isEditing && clientQuery.isError,
    errorMessage: mutation.isError
      ? (mutation.error?.message ?? 'Something went wrong. Please try again.')
      : null,
  }
}
