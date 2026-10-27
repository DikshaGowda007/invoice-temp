import { keepPreviousData, useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { dashboardApi } from '@/api/dashboard.api'

export function useDashboardSummary() {
  const [range, setRange] = useState('6M')

  const query = useQuery({
    queryKey: ['dashboard', 'summary', range],
    queryFn: () => dashboardApi.summary(range).then((res) => res.data.data),
    placeholderData: keepPreviousData,
  })

  return {
    range,
    setRange,
    stats: query.data?.stats ?? null,
    revenueOverTime: query.data?.revenue_over_time ?? [],
    recentInvoices: query.data?.recent_invoices ?? [],
    topClients: query.data?.top_clients ?? [],
    isLoading: query.isLoading,
    isFetching: query.isFetching,
    isError: query.isError,
    errorMessage: query.error?.message ?? 'Something went wrong. Please try again.',
    refetch: query.refetch,
  }
}
