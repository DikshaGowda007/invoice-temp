import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useState } from 'react'
import { clientApi } from '@/api/client.api'

export function useClientList() {
  const queryClient = useQueryClient()
  const [pendingDeleteClient, setPendingDeleteClient] = useState(null)

  const query = useQuery({
    queryKey: ['clients'],
    queryFn: () => clientApi.list().then((res) => res.data.data.clients),
  })

  const deleteMutation = useMutation({
    mutationFn: (id) => clientApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['clients'] })
      setPendingDeleteClient(null)
    },
  })

  return {
    clients: query.data ?? [],
    isLoading: query.isLoading,
    isError: query.isError,
    errorMessage: query.error?.message ?? 'Something went wrong. Please try again.',
    pendingDeleteClient,
    requestDelete: setPendingDeleteClient,
    cancelDelete: () => setPendingDeleteClient(null),
    confirmDelete: () => deleteMutation.mutate(pendingDeleteClient.id),
    isDeleting: deleteMutation.isPending,
  }
}
