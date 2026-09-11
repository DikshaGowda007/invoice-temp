import { Link } from 'react-router-dom'
import { AuthField } from '@/components/forms/AuthField'
import { LockIcon, MailIcon } from '@/components/forms/AuthFieldIcons'
import { useLoginForm } from '@/features/auth/hooks/useLoginForm'
import { AuthLayout } from '@/layouts/AuthLayout'
import { Button } from '@/components/ui/button'
import { ROUTES } from '@/utils/routePaths'

export default function LoginPage() {
  const { register, errors, handleSubmit, isPending, errorMessage } = useLoginForm()

  return (
    <AuthLayout title="Welcome back" description="Log in to your InvoiceHub account.">
      <form noValidate onSubmit={handleSubmit} className="flex flex-col gap-6.5">
        <AuthField
          id="email"
          label="Email"
          icon={<MailIcon />}
          type="email"
          autoComplete="email"
          error={errors.email?.message}
          {...register('email')}
        />

        <div>
          <AuthField
            id="password"
            label="Password"
            icon={<LockIcon />}
            type="password"
            autoComplete="current-password"
            error={errors.password?.message}
            {...register('password')}
          />
          <div className="mt-4 flex items-center justify-between">
            <span className="cursor-pointer text-sm text-muted-foreground">
              Forgot password?
            </span>
          </div>
        </div>

        {errorMessage && <p className="text-sm text-destructive">{errorMessage}</p>}

        <Button
          type="submit"
          size="lg"
          disabled={isPending}
          className="mt-1 gap-1.5 rounded-full font-bold shadow-[0_10px_24px_rgba(217,119,87,0.38)]"
        >
          {isPending ? (
            'Logging in…'
          ) : (
            <>
              Log in
              <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2.6"
                strokeLinecap="round"
                strokeLinejoin="round"
              >
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
              </svg>
            </>
          )}
        </Button>
      </form>

      <p className="mt-6 text-[13.5px] text-muted-foreground">
        Don&apos;t have an account?{' '}
        <Link to={ROUTES.REGISTER} className="font-bold text-primary hover:underline">
          Create one
        </Link>
      </p>
    </AuthLayout>
  )
}
