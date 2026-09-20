const STATUS_STYLES = {
  DRAFT: 'bg-muted text-muted-foreground',
  SENT: 'bg-info-bg text-info',
  PAID: 'bg-success-bg text-success',
  OVERDUE: 'bg-danger-bg text-danger',
  CANCELLED: 'bg-muted text-muted-foreground line-through',
  ACTIVE: 'bg-success-bg text-success',
  PAUSED: 'bg-muted text-muted-foreground',
}

export const INVOICE_STATUSES = ['DRAFT', 'SENT', 'PAID', 'OVERDUE', 'CANCELLED']

export const RECURRING_STATUSES = ['ACTIVE', 'PAUSED', 'CANCELLED']

export const STATUS_ACCENT_STYLES = {
  DRAFT: 'border-l-muted-foreground/40',
  SENT: 'border-l-info',
  PAID: 'border-l-success',
  OVERDUE: 'border-l-danger',
  CANCELLED: 'border-l-muted-foreground/40',
}

export function StatusBadge({ status }) {
  return (
    <span
      className={`inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold tracking-wide uppercase ${
        STATUS_STYLES[status] ?? 'bg-muted text-muted-foreground'
      }`}
    >
      {status}
    </span>
  )
}
