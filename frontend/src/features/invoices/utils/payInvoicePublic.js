import { invoiceApi } from '@/api/invoice.api'

const RAZORPAY_SCRIPT_SRC = 'https://checkout.razorpay.com/v1/checkout.js'
const POLL_INTERVAL_MS = 1200
const MAX_POLL_ATTEMPTS = 20

function loadRazorpayScript() {
  if (window.Razorpay) return Promise.resolve()

  const existing = document.querySelector(`script[src="${RAZORPAY_SCRIPT_SRC}"]`)
  if (existing) {
    return new Promise((resolve) => {
      existing.addEventListener('load', resolve, { once: true })
    })
  }

  return new Promise((resolve, reject) => {
    const script = document.createElement('script')
    script.src = RAZORPAY_SCRIPT_SRC
    script.onload = () => resolve()
    script.onerror = () => reject(new Error('Could not load the payment form. Check your connection and try again.'))
    document.body.appendChild(script)
  })
}

function openCheckout(options) {
  return new Promise((resolve, reject) => {
    const razorpay = new window.Razorpay({
      ...options,
      handler: () => resolve(),
      modal: {
        ondismiss: () => reject(new Error('Payment was not completed.')),
      },
    })
    razorpay.open()
  })
}

async function pollUntilPaid(token) {
  for (let attempt = 0; attempt < MAX_POLL_ATTEMPTS; attempt += 1) {
    await new Promise((resolve) => setTimeout(resolve, POLL_INTERVAL_MS))

    const res = await invoiceApi.publicGet(token)
    if (res.data.data.invoice.status === 'PAID') return
  }

  throw new Error('Payment is still processing — refresh in a moment to see the updated status.')
}

export async function payInvoicePublic(token) {
  const orderRes = await invoiceApi.publicCreatePaymentOrder(token)
  const {
    order_id: orderId,
    amount,
    currency,
    key_id: keyId,
    invoice_number: invoiceNumber,
  } = orderRes.data.data

  await loadRazorpayScript()

  await openCheckout({
    key: keyId,
    order_id: orderId,
    amount,
    currency,
    name: 'InvoiceHub',
    description: `Invoice ${invoiceNumber}`,
    theme: { color: '#d97757' },
  })

  await pollUntilPaid(token)
}
