import { AlertCircle } from 'lucide-react'

export function FormAlert({ children }) {
  if (!children) return null

  return (
    <div className="animate-in fade-in slide-in-from-top-1 flex items-start gap-2 rounded-lg border border-destructive/20 bg-destructive/10 px-3 py-2.5 text-sm text-destructive duration-200">
      <AlertCircle size={15} className="mt-0.5 shrink-0" />
      <p>{children}</p>
    </div>
  )
}
