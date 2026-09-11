import { zodResolver } from '@hookform/resolvers/zod'
import { useMutation } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { useNavigate } from 'react-router-dom'
import { z } from 'zod'
import { authApi } from '@/api/auth.api'
import { useAuth } from '@/context/AuthContext'
import { ROUTES } from '@/utils/routePaths'

const registerSchema = z
  .object({
    first_name: z.string().min(1, 'First name is required'),
    last_name: z.string().min(1, 'Last name is required'),
    email: z.email('Enter a valid email'),
    password: z.string().min(8, 'Must be at least 8 characters'),
    password_confirmation: z.string(),
  })
  .refine((data) => data.password === data.password_confirmation, {
    message: 'Passwords do not match',
    path: ['password_confirmation'],
  })

export function useRegisterForm() {
  const navigate = useNavigate()
  const { login } = useAuth()

  const {
    register,
    handleSubmit: handleFormSubmit,
    formState: { errors },
  } = useForm({ resolver: zodResolver(registerSchema) })

  const mutation = useMutation({
    mutationFn: (values) => authApi.register(values).then((res) => res.data.data),
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
