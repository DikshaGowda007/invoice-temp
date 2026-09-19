export const ROUTES = {
  HOME: '/',
  LOGIN: '/login',
  REGISTER: '/register',
  CLIENTS: '/clients',
  CLIENT_NEW: '/clients/new',
  CLIENT_EDIT: '/clients/:id/edit',
  INVOICES: '/invoices',
  INVOICE_NEW: '/invoices/new',
  INVOICE_DETAIL: '/invoices/:id',
  INVOICE_EDIT: '/invoices/:id/edit',
  SETTINGS: '/settings',
}

export const clientEditPath = (id) => `/clients/${id}/edit`
export const invoiceDetailPath = (id) => `/invoices/${id}`
export const invoiceEditPath = (id) => `/invoices/${id}/edit`
