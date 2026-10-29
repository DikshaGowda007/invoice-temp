import { Link } from 'react-router-dom'
import { FormAlert } from '@/components/common/FormAlert'
import { AuthField } from '@/components/forms/AuthField'
import { MailIcon } from '@/components/forms/AuthFieldIcons'
import { useForgotPasswordForm } from '@/features/auth/hooks/useForgotPasswordForm'
import { AuthLayout } from '@/layouts/AuthLayout'
import { Button } from '@/components/ui/button'
import { ROUTES } from '@/utils/routePaths'

export default function ForgotPasswordPage() {
  const { register, errors, handleSubmit, isPending, isSuccess, successMessage, errorMessage } =
    useForgotPasswordForm()

  return (
    <AuthLayout
      title="Reset your password"
      description="Enter your account email and we'll send you a reset link."
    >
      {isSuccess ? (
        <div className="flex flex-col gap-6">
          <p className="rounded-lg border border-success/20 bg-success-bg px-3 py-2.5 text-sm text-success">
            {successMessage}
          </p>
          <Link to={ROUTES.LOGIN} className="text-sm font-bold text-primary hover:underline">
            Back to log in
          </Link>
        </div>
      ) : (
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

          <FormAlert>{errorMessage}</FormAlert>

          <Button
            type="submit"
            size="lg"
            disabled={isPending}
            className="mt-1 gap-1.5 rounded-full font-bold shadow-[0_10px_24px_rgba(217,119,87,0.38)]"
          >
            {isPending ? 'Sending…' : 'Send reset link'}
          </Button>

          <p className="text-[13.5px] text-muted-foreground">
            Remembered your password?{' '}
            <Link to={ROUTES.LOGIN} className="font-bold text-primary hover:underline">
              Log in
            </Link>
          </p>
        </form>
      )}
    </AuthLayout>
  )
}
