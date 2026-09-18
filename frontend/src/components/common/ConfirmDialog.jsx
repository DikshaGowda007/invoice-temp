import { AlertDialog } from '@base-ui/react/alert-dialog'
import { AlertTriangle } from 'lucide-react'
import { cn } from 'cn'
import { Button } from '@/components/ui/button'

export function ConfirmDialog({
  open,
  onOpenChange,
  title,
  description,
  confirmLabel = 'Confirm',
  onConfirm,
  isConfirming,
}) {
  return (
    <AlertDialog.Root open={open} onOpenChange={onOpenChange}>
      <AlertDialog.Portal>
        <AlertDialog.Backdrop className="fixed inset-0 z-50 bg-foreground/40 backdrop-blur-[2px] data-[ending-style]:opacity-0 data-[starting-style]:opacity-0" />
        <AlertDialog.Popup
          className={cn(
            'fixed top-1/2 left-1/2 z-50 w-full max-w-sm -translate-x-1/2 -translate-y-1/2 rounded-2xl bg-card p-6 text-card-foreground shadow-2xl ring-1 ring-foreground/10',
            'data-[ending-style]:scale-95 data-[ending-style]:opacity-0 data-[starting-style]:scale-95 data-[starting-style]:opacity-0',
            'transition-all duration-150',
          )}
        >
          <div className="flex h-11 w-11 items-center justify-center rounded-full bg-danger-bg text-danger">
            <AlertTriangle size={20} strokeWidth={2} />
          </div>

          <AlertDialog.Title className="mt-3.5 text-[15px] font-semibold">{title}</AlertDialog.Title>
          <AlertDialog.Description className="mt-1.5 text-[13.5px] leading-relaxed text-muted-foreground">
            {description}
          </AlertDialog.Description>

          <div className="mt-6 flex justify-end gap-2.5">
            <AlertDialog.Close render={<Button variant="outline" disabled={isConfirming} />}>
              Cancel
            </AlertDialog.Close>
            <Button variant="destructive" onClick={onConfirm} disabled={isConfirming}>
              {isConfirming ? 'Deleting…' : confirmLabel}
            </Button>
          </div>
        </AlertDialog.Popup>
      </AlertDialog.Portal>
    </AlertDialog.Root>
  )
}
