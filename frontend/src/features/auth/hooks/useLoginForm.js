import { zodResolver } from '@hookform/resolvers/zod'
import { useMutation } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { useNavigate } from 'react-router-dom'
import { z } from 'zod'
import { authApi } from '@/api/auth.api'
import { useAuth } from '@/context/AuthContext'
import { ROUTES } from '@/utils/routePaths'

const loginSchema = z.object({
  email: z.email('Enter a valid email'),
  password: z.string().min(1, 'Password is required'),
})

export function useLoginForm() {
  const navigate = useNavigate()
  const { login } = useAuth()

  const {
    register,
    handleSubmit: handleFormSubmit,
    formState: { errors },
  } = useForm({ resolver: zodResolver(loginSchema) })

  const mutation = useMutation({
    mutationFn: (values) => authApi.login(values).then((res) => res.data.data),
    onSuccess: (data) => {
      login(data.user, data.token)
      navigate(ROUTES.HOME)
    },
  })

  return {
    register,
    errors,
    handleSubmit: handleFormSubmit((values) => mutation.mutate(values)),
    isPending: mutation.isPending,
    errorMessage: mutation.isError
      ? (mutation.error?.message ?? 'Something went wrong. Please try again.')
      : null,
  }
}
