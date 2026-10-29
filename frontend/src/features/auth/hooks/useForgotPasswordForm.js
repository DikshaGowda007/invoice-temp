import { zodResolver } from '@hookform/resolvers/zod'
import { useMutation } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import { authApi } from '@/api/auth.api'

const forgotPasswordSchema = z.object({
  email: z.email('Enter a valid email'),
})

export function useForgotPasswordForm() {
  const {
    register,
    handleSubmit: handleFormSubmit,
    formState: { errors },
  } = useForm({ resolver: zodResolver(forgotPasswordSchema) })

  const mutation = useMutation({
    mutationFn: (values) => authApi.forgotPassword(values).then((res) => res.data.data),
  })

  return {
    register,
    errors,
    handleSubmit: handleFormSubmit((values) => mutation.mutate(values)),
    isPending: mutation.isPending,
    isSuccess: mutation.isSuccess,
    successMessage: mutation.data ?? null,
    errorMessage: mutation.isError
      ? (mutation.error?.message ?? 'Something went wrong. Please try again.')
      : null,
  }
}
