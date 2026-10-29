import { zodResolver } from '@hookform/resolvers/zod'
import { useMutation } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { useNavigate, useSearchParams } from 'react-router-dom'
import { z } from 'zod'
import { authApi } from '@/api/auth.api'
import { ROUTES } from '@/utils/routePaths'

const resetPasswordSchema = z
  .object({
    password: z.string().min(8, 'Must be at least 8 characters'),
    password_confirmation: z.string(),
  })
  .refine((data) => data.password === data.password_confirmation, {
    message: 'Passwords do not match',
    path: ['password_confirmation'],
  })

export function useResetPasswordForm() {
  const navigate = useNavigate()
  const [searchParams] = useSearchParams()
  const email = searchParams.get('email') ?? ''
  const token = searchParams.get('token') ?? ''

  const {
    register,
    handleSubmit: handleFormSubmit,
    formState: { errors },
  } = useForm({ resolver: zodResolver(resetPasswordSchema) })

  const mutation = useMutation({
    mutationFn: (values) =>
      authApi.resetPassword({ ...values, email, token }).then((res) => res.data.data),
    onSuccess: () => {
      navigate(ROUTES.LOGIN, { replace: true })
    },
  })

  return {
    register,
    errors,
    handleSubmit: handleFormSubmit((values) => mutation.mutate(values)),
    isPending: mutation.isPending,
    isLinkMissing: !email || !token,
    errorMessage: mutation.isError
      ? (mutation.error?.message ?? 'Something went wrong. Please try again.')
      : null,
  }
}
