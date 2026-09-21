import { invoiceApi } from '@/api/invoice.api'

const MAX_ATTEMPTS = 30
const POLL_INTERVAL_MS = 800

export async function downloadInvoicePdf(invoiceId, fallbackFilename = 'invoice.pdf') {
  const dispatchRes = await invoiceApi.download(invoiceId)
  const requestId = dispatchRes.data.data.request_id

  for (let attempt = 0; attempt < MAX_ATTEMPTS; attempt += 1) {
    await new Promise((resolve) => setTimeout(resolve, POLL_INTERVAL_MS))

    const statusRes = await invoiceApi.downloadStatus(requestId)
    const status = statusRes.data.data.status

    if (status === 'ready') {
      const res = await invoiceApi.downloadResult(requestId)
      const filenameMatch = res.headers['content-disposition']?.match(/filename="(.+)"/)
      const filename = filenameMatch?.[1] ?? fallbackFilename

      const url = window.URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }))
      const link = document.createElement('a')
      link.href = url
      link.download = filename
      document.body.appendChild(link)
      link.click()
      link.remove()
      window.URL.revokeObjectURL(url)
      return
    }
    if (status === 'failed') {
      throw new Error('PDF generation failed. Please try again.')
    }
  }

  throw new Error('PDF is taking longer than expected. Please try again shortly.')
}
