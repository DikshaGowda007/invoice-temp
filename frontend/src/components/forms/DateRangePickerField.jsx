import { ChevronDown } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Calendar } from '@/components/ui/calendar'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { formatDate } from '@/utils/format'

function toDate(value) {
  return value ? new Date(`${value}T00:00:00`) : undefined
}

function toIso(date) {
  return date ? date.toLocaleDateString('en-CA') : ''
}

export function DateRangePickerField({ label, from, to, onChange }) {
  const range = { from: toDate(from), to: toDate(to) }

  const summary =
    from && to
      ? `${formatDate(from)} – ${formatDate(to)}`
      : from
        ? formatDate(from)
        : 'Any date'

  return (
    <Popover>
      <PopoverTrigger
        render={
          <Button
            type="button"
            variant="outline"
            className="h-8 gap-1.5 rounded-lg border-input bg-transparent px-3 text-sm font-normal shadow-none"
          />
        }
      >
        <span className="text-muted-foreground">{label}:</span>
        {summary}
        <ChevronDown size={13} className="text-muted-foreground" />
      </PopoverTrigger>
      <PopoverContent className="w-auto rounded-xl p-0" align="start" sideOffset={8}>
        <Calendar
          mode="range"
          selected={range}
          onSelect={(nextRange) => onChange(toIso(nextRange?.from), toIso(nextRange?.to))}
          className="rounded-xl p-3 [--cell-size:--spacing(8)]"
          classNames={{
            month_caption: 'mb-2 flex h-8 w-full items-center justify-center border-b border-border pb-3 text-sm font-semibold',
            weekday: 'flex-1 text-[11px] font-semibold tracking-wide text-muted-foreground uppercase',
          }}
        />
      </PopoverContent>
    </Popover>
  )
}
