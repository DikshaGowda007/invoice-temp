import { useEffect, useState } from 'react'
import { Eye, EyeOff } from 'lucide-react'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const PASSWORD_REVEAL_MS = 3000

export function AuthField({ id, label, icon, error, action, type, ...inputProps }) {
  const [showPassword, setShowPassword] = useState(false)
  const isPassword = type === 'password'

  useEffect(() => {
    if (!showPassword) return

    const timer = setTimeout(() => setShowPassword(false), PASSWORD_REVEAL_MS)

    return () => clearTimeout(timer)
  }, [showPassword])

  return (
    <div className="group">
      <div className="mb-2.5 flex items-center justify-between">
        <Label
          htmlFor={id}
          className="flex items-center gap-1.75 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
        >
          {icon && (
            <span className="flex h-4 w-4 items-center justify-center rounded-full bg-accent text-primary transition-transform duration-200 group-focus-within:scale-110">
              {icon}
            </span>
          )}
          {label}
        </Label>
        {action}
      </div>
      <div className={isPassword ? 'relative' : undefined}>
        <Input
          id={id}
          type={isPassword ? (showPassword ? 'text' : 'password') : type}
          className={`h-auto rounded-sm border-x-0 border-t-0 border-b-[1.5px] border-input bg-transparent px-0.5 py-2.5 text-[15.5px] shadow-none transition-shadow focus-visible:border-primary focus-visible:shadow-[0_8px_16px_-8px_rgba(217,119,87,0.45)] focus-visible:ring-0 dark:bg-transparent ${isPassword ? 'pr-7' : ''}`}
          {...inputProps}
        />
        {isPassword && (
          <button
            type="button"
            onClick={() => setShowPassword((v) => !v)}
            tabIndex={-1}
            className="absolute right-0.5 top-1/2 -translate-y-1/2 text-muted-foreground transition-colors hover:text-foreground"
          >
            {showPassword ? <EyeOff size={15} /> : <Eye size={15} />}
          </button>
        )}
      </div>
      {error && (
        <p className="animate-in fade-in slide-in-from-top-1 mt-1.5 text-sm text-destructive duration-200">
          {error}
        </p>
      )}
    </div>
  )
}
