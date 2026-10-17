export function todayStamp() {
  const now = new Date()
  const year = now.getFullYear()
  const month = String(now.getMonth() + 1).padStart(2, '0')
  const day = String(now.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

function escapeCsvField(value) {
  const stringValue = String(value ?? '')
  if (/[",\n]/.test(stringValue)) {
    return `"${stringValue.replace(/"/g, '""')}"`
  }
  return stringValue
}

export function downloadCsv(filename, columns, rows) {
  const headerRow = columns.map((column) => escapeCsvField(column.header)).join(',')
  const bodyRows = rows.map((row) => columns.map((column) => escapeCsvField(column.value(row))).join(','))
  const csvContent = [headerRow, ...bodyRows].join('\r\n')

  const url = window.URL.createObjectURL(new Blob(['﻿', csvContent], { type: 'text/csv;charset=utf-8;' }))
  const link = document.createElement('a')
  link.href = url
  link.download = filename
  document.body.appendChild(link)
  link.click()
  link.remove()
  window.URL.revokeObjectURL(url)
}
