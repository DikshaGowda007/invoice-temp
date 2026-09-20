import { Clock as ClockIcon } from 'lucide-react'
import { useRef, useState } from 'react'
import { Button } from '@/components/ui/button'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'

const RADIUS = 90
const CENTER = 100
const HOUR_MARK_RADIUS = 72
const MINUTE_MARK_RADIUS = 72
const HAND_LENGTH = 64

const underlineTriggerClassName =
  'h-auto w-full justify-between gap-2 rounded-none border-x-0 border-t-0 border-b-[1.5px] border-input bg-transparent px-0.5 py-2 text-[14.5px] font-normal shadow-none hover:bg-transparent focus-visible:border-primary focus-visible:ring-0'

const boxedTriggerClassName =
  'h-8 w-full justify-between gap-1.5 rounded-lg border border-input bg-transparent px-2.5 py-1 text-sm font-normal shadow-none hover:bg-transparent focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50'

function to24Hour(hour12, period) {
  if (period === 'AM') return hour12 === 12 ? 0 : hour12
  return hour12 === 12 ? 12 : hour12 + 12
}

function parseValue(value) {
  if (!value) return { hour12: 12, minute: 0, period: 'AM' }

  const [hourStr, minuteStr] = value.split(':')
  const hour24 = Number(hourStr)
  const minute = Number(minuteStr)
  const period = hour24 < 12 ? 'AM' : 'PM'
  const hour12 = hour24 % 12 === 0 ? 12 : hour24 % 12

  return { hour12, minute, period }
}

function formatDisplay(value) {
  if (!value) return null
  const { hour12, minute, period } = parseValue(value)

  return `${hour12}:${String(minute).padStart(2, '0')} ${period}`
}

function angleToValue(clientX, clientY, rect, positions) {
  const cx = rect.left + rect.width / 2
  const cy = rect.top + rect.height / 2
  const dx = clientX - cx
  const dy = clientY - cy
  let deg = Math.atan2(dy, dx) * (180 / Math.PI) + 90
  if (deg < 0) deg += 360

  const step = 360 / positions
  return Math.round(deg / step) % positions
}

function polarPoint(index, positions, radius) {
  const angleDeg = (index / positions) * 360 - 90
  const angleRad = (angleDeg * Math.PI) / 180

  return {
    x: CENTER + radius * Math.cos(angleRad),
    y: CENTER + radius * Math.sin(angleRad),
  }
}

function ClockFace({ mode, hour12, minute, onSelectHour, onSelectMinute }) {
  const svgRef = useRef(null)
  const [dragging, setDragging] = useState(false)

  const handleValue = mode === 'hour' ? hour12 % 12 : minute / 5
  const positions = 12
  const markRadius = mode === 'hour' ? HOUR_MARK_RADIUS : MINUTE_MARK_RADIUS
  const handPoint = polarPoint(handleValue, positions, HAND_LENGTH)

  const applyPointer = (clientX, clientY) => {
    const rect = svgRef.current.getBoundingClientRect()
    const index = angleToValue(clientX, clientY, rect, positions)

    if (mode === 'hour') {
      onSelectHour(index === 0 ? 12 : index)
    } else {
      onSelectMinute(index * 5)
    }
  }

  const handlePointerDown = (e) => {
    setDragging(true)
    applyPointer(e.clientX, e.clientY)
  }

  const handlePointerMove = (e) => {
    if (!dragging) return
    applyPointer(e.clientX, e.clientY)
  }

  const stopDragging = () => setDragging(false)

  return (
    <svg
      ref={svgRef}
      viewBox={`0 0 ${CENTER * 2} ${CENTER * 2}`}
      width={220}
      height={220}
      className="touch-none select-none"
      onPointerDown={handlePointerDown}
      onPointerMove={handlePointerMove}
      onPointerUp={stopDragging}
      onPointerLeave={stopDragging}
    >
      <circle cx={CENTER} cy={CENTER} r={RADIUS} className="fill-muted" />
      <line
        x1={CENTER}
        y1={CENTER}
        x2={handPoint.x}
        y2={handPoint.y}
        strokeWidth={2}
        className="stroke-primary"
      />
      <circle cx={CENTER} cy={CENTER} r={3} className="fill-primary" />
      <circle cx={handPoint.x} cy={handPoint.y} r={16} className="fill-primary" />

      {Array.from({ length: positions }, (_, i) => {
        const point = polarPoint(i, positions, markRadius)
        const label = mode === 'hour' ? (i === 0 ? 12 : i) : String(i * 5).padStart(2, '0')
        const isSelected = i === handleValue

        return (
          <text
            key={i}
            x={point.x}
            y={point.y}
            textAnchor="middle"
            dominantBaseline="central"
            className={`pointer-events-none text-[13px] font-medium ${
              isSelected ? 'fill-primary-foreground' : 'fill-foreground'
            }`}
          >
            {label}
          </text>
        )
      })}
    </svg>
  )
}

export function TimePickerField({ id, value, onChange, disabled, variant = 'underline' }) {
  const { hour12, minute, period } = parseValue(value)
  const [mode, setMode] = useState('hour')
  const triggerClassName = variant === 'boxed' ? boxedTriggerClassName : underlineTriggerClassName

  const commit = (nextHour12, nextMinute, nextPeriod) => {
    onChange(`${String(to24Hour(nextHour12, nextPeriod)).padStart(2, '0')}:${String(nextMinute).padStart(2, '0')}`)
  }

  const handleSelectHour = (nextHour12) => {
    commit(nextHour12, minute, period)
    setMode('minute')
  }

  const handleSelectMinute = (nextMinute) => {
    commit(hour12, nextMinute, period)
  }

  const handleSelectPeriod = (nextPeriod) => {
    commit(hour12, minute, nextPeriod)
  }

  return (
    <Popover onOpenChange={(open) => open && setMode('hour')}>
      <PopoverTrigger
        render={<Button id={id} type="button" variant="outline" disabled={disabled} className={triggerClassName} />}
      >
        <span className="truncate text-left">
          {value ? formatDisplay(value) : <span className="text-muted-foreground">Pick a time</span>}
        </span>
        <ClockIcon size={14} className="shrink-0 text-muted-foreground" />
      </PopoverTrigger>
      <PopoverContent className="w-auto rounded-xl p-4" align="start" sideOffset={8}>
        <div className="mb-3 flex items-center justify-center gap-2">
          <button
            type="button"
            onClick={() => setMode('hour')}
            className={`rounded-lg px-2 py-1 text-[22px] font-semibold tabular-nums ${
              mode === 'hour' ? 'bg-accent text-primary' : 'text-muted-foreground'
            }`}
          >
            {hour12}
          </button>
          <span className="text-[22px] font-semibold text-muted-foreground">:</span>
          <button
            type="button"
            onClick={() => setMode('minute')}
            className={`rounded-lg px-2 py-1 text-[22px] font-semibold tabular-nums ${
              mode === 'minute' ? 'bg-accent text-primary' : 'text-muted-foreground'
            }`}
          >
            {String(minute).padStart(2, '0')}
          </button>

          <div className="ml-2 flex flex-col gap-0.5">
            <button
              type="button"
              onClick={() => handleSelectPeriod('AM')}
              className={`rounded px-1.5 py-0.5 text-[11px] font-semibold ${
                period === 'AM' ? 'bg-accent text-primary' : 'text-muted-foreground'
              }`}
            >
              AM
            </button>
            <button
              type="button"
              onClick={() => handleSelectPeriod('PM')}
              className={`rounded px-1.5 py-0.5 text-[11px] font-semibold ${
                period === 'PM' ? 'bg-accent text-primary' : 'text-muted-foreground'
              }`}
            >
              PM
            </button>
          </div>
        </div>

        <ClockFace
          mode={mode}
          hour12={hour12}
          minute={minute}
          onSelectHour={handleSelectHour}
          onSelectMinute={handleSelectMinute}
        />
      </PopoverContent>
    </Popover>
  )
}
