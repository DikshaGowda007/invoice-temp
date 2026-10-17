import { downloadCsv, todayStamp } from '@/utils/csvExport'

const COLUMNS = [
  { header: 'Invoice Number', value: (invoice) => invoice.invoice_number },
  { header: 'Client', value: (invoice) => invoice.client_name ?? '' },
  { header: 'Status', value: (invoice) => invoice.status },
  { header: 'Issue Date', value: (invoice) => invoice.issue_date ?? '' },
  { header: 'Due Date', value: (invoice) => invoice.due_date ?? '' },
  { header: 'Total', value: (invoice) => invoice.total },
]

export function exportInvoicesToCsv(invoices, filename = `invoices-${todayStamp()}.csv`) {
  downloadCsv(filename, COLUMNS, invoices)
}
