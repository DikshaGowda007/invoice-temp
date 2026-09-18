import { Label } from '@/components/ui/label'

export function FormField({ id, label, icon, error, children }) {
  return (
    <div>
      <Label
        htmlFor={id}
        className="mb-2 flex items-center gap-1.75 text-[11.5px] font-semibold tracking-wide text-muted-foreground uppercase"
      >
        <span className="flex h-4 w-4 items-center justify-center rounded-full bg-accent text-primary">
          {icon}
        </span>
        {label}
      </Label>
      {children}
      {error && <p className="mt-1.5 text-sm text-destructive">{error}</p>}
    </div>
  )
}
