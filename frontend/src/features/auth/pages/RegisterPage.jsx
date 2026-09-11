import { Link } from 'react-router-dom'
import { AuthField } from '@/components/forms/AuthField'
import { LockIcon, MailIcon, UserIcon } from '@/components/forms/AuthFieldIcons'
import { useRegisterForm } from '@/features/auth/hooks/useRegisterForm'
import { AuthLayout } from '@/layouts/AuthLayout'
import { Button } from '@/components/ui/button'
import { ROUTES } from '@/utils/routePaths'

export default function RegisterPage() {
  const { register, errors, handleSubmit, isPending, errorMessage } = useRegisterForm()

  return (
    <AuthLayout
      title="Create your account"
      description="Start creating and sending invoices in minutes."
    >
      <form noValidate onSubmit={handleSubmit} className="flex flex-col gap-6.5">
        <AuthField
          id="first_name"
          label="First name"
          icon={<UserIcon />}
          autoComplete="given-name"
          error={errors.first_name?.message}
          {...register('first_name')}
        />
        <AuthField
          id="last_name"
          label="Last name"
          icon={<UserIcon />}
          autoComplete="family-name"
          error={errors.last_name?.message}
          {...register('last_name')}
        />
        <AuthField
          id="email"
          label="Email"
          icon={<MailIcon />}
          type="email"
          autoComplete="email"
          error={errors.email?.message}
          {...register('email')}
        />
        <AuthField
          id="password"
          label="Password"
          icon={<LockIcon />}
          type="password"
          autoComplete="new-password"
          error={errors.password?.message}
          {...register('password')}
        />
        <AuthField
          id="password_confirmation"
          label="Confirm password"
          icon={<LockIcon />}
          type="password"
          autoComplete="new-password"
          error={errors.password_confirmation?.message}
          {...register('password_confirmation')}
        />

        {errorMessage && <p className="text-sm text-destructive">{errorMessage}</p>}

        <Button
          type="submit"
          size="lg"
          disabled={isPending}
          className="group mt-1 gap-1.5 rounded-full font-bold shadow-[0_10px_24px_rgba(217,119,87,0.38)]"
        >
          {isPending ? (
            <>
              <svg
                className="animate-spin"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
              >
                <circle
                  className="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  strokeWidth="3"
                />
                <path
                  className="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"
                />
              </svg>
              Creating account…
            </>
          ) : (
            <>
              Create account
              <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2.6"
                strokeLinecap="round"
                strokeLinejoin="round"
                className="transition-transform duration-200 group-hover:translate-x-0.5"
              >
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
              </svg>
            </>
          )}
        </Button>
      </form>

      <p className="mt-6 text-[13.5px] text-muted-foreground">
        Already have an account?{' '}
        <Link to={ROUTES.LOGIN} className="font-bold text-primary hover:underline">
          Log in
        </Link>
      </p>
    </AuthLayout>
  )
}
