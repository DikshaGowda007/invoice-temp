import { CalendarIcon } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Calendar } from '@/components/ui/calendar'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { formatDate } from '@/utils/format'

const underlineTriggerClassName =
  'h-auto w-full justify-between gap-2 rounded-none border-x-0 border-t-0 border-b-[1.5px] border-input bg-transparent px-0.5 py-2 text-[14.5px] font-normal shadow-none hover:bg-transparent focus-visible:border-primary focus-visible:ring-0'

const boxedTriggerClassName =
  'h-8 w-full justify-between gap-1.5 rounded-lg border border-input bg-transparent px-2.5 py-1 text-sm font-normal shadow-none hover:bg-transparent focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50'

export function DatePickerField({ id, value, onChange, disabled, variant = 'underline' }) {
  const selected = value ? new Date(`${value}T00:00:00`) : undefined
  const triggerClassName = variant === 'boxed' ? boxedTriggerClassName : underlineTriggerClassName

  return (
    <Popover>
      <PopoverTrigger
        render={<Button id={id} type="button" variant="outline" disabled={disabled} className={triggerClassName} />}
      >
        <span className="truncate text-left">
          {selected ? formatDate(value) : <span className="text-muted-foreground">Pick a date</span>}
        </span>
        <CalendarIcon size={14} className="shrink-0 text-muted-foreground" />
      </PopoverTrigger>
      <PopoverContent className="w-auto rounded-xl p-0" align="start" sideOffset={8}>
        <Calendar
          mode="single"
          selected={selected}
          onSelect={(date) => onChange(date ? date.toLocaleDateString('en-CA') : '')}
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
