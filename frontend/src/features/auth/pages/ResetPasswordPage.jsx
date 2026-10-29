import { Link } from 'react-router-dom'
import { FormAlert } from '@/components/common/FormAlert'
import { AuthField } from '@/components/forms/AuthField'
import { LockIcon } from '@/components/forms/AuthFieldIcons'
import { useResetPasswordForm } from '@/features/auth/hooks/useResetPasswordForm'
import { AuthLayout } from '@/layouts/AuthLayout'
import { Button } from '@/components/ui/button'
import { ROUTES } from '@/utils/routePaths'

export default function ResetPasswordPage() {
  const { register, errors, handleSubmit, isPending, isLinkMissing, errorMessage } =
    useResetPasswordForm()

  if (isLinkMissing) {
    return (
      <AuthLayout title="Reset your password" description="This reset link is incomplete.">
        <p className="mb-6 text-sm text-muted-foreground">
          Please use the link from the password reset email, or request a new one.
        </p>
        <Link to={ROUTES.FORGOT_PASSWORD} className="text-sm font-bold text-primary hover:underline">
          Request a new link
        </Link>
      </AuthLayout>
    )
  }

  return (
    <AuthLayout title="Choose a new password" description="Enter a new password for your account.">
      <form noValidate onSubmit={handleSubmit} className="flex flex-col gap-6.5">
        <AuthField
          id="password"
          label="New password"
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

        <FormAlert>{errorMessage}</FormAlert>

        <Button
          type="submit"
          size="lg"
          disabled={isPending}
          className="mt-1 gap-1.5 rounded-full font-bold shadow-[0_10px_24px_rgba(217,119,87,0.38)]"
        >
          {isPending ? 'Resetting…' : 'Reset password'}
        </Button>

        <p className="text-[13.5px] text-muted-foreground">
          <Link to={ROUTES.LOGIN} className="font-bold text-primary hover:underline">
            Back to log in
          </Link>
        </p>
      </form>
    </AuthLayout>
  )
}
