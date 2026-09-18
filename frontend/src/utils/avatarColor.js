const PALETTE = [
  'bg-info-bg text-info',
  'bg-success-bg text-success',
  'bg-warning-bg text-warning',
  'bg-accent text-primary',
  'bg-danger-bg text-danger',
]

export function avatarColorFor(id) {
  const index = Math.abs(Number(id) || 0) % PALETTE.length
  return PALETTE[index]
}

export function initialsFor(name) {
  return (name ?? '')
    .trim()
    .split(/\s+/)
    .filter(Boolean)
    .map((part) => part[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
}
