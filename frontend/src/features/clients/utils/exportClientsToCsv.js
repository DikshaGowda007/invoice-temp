import { downloadCsv, todayStamp } from '@/utils/csvExport'

const COLUMNS = [
  { header: 'Name', value: (client) => client.name },
  { header: 'Email', value: (client) => client.email ?? '' },
  { header: 'Phone', value: (client) => client.phone ?? '' },
  { header: 'Address', value: (client) => client.address ?? '' },
  { header: 'Notes', value: (client) => client.notes ?? '' },
]

export function exportClientsToCsv(clients, filename = `clients-${todayStamp()}.csv`) {
  downloadCsv(filename, COLUMNS, clients)
}
