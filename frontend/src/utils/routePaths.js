export const ROUTES = {
  HOME: '/',
  LOGIN: '/login',
  REGISTER: '/register',
  CLIENTS: '/clients',
  CLIENT_NEW: '/clients/new',
  CLIENT_EDIT: '/clients/:id/edit',
  INVOICES: '/invoices',
  SETTINGS: '/settings',
}

export const clientEditPath = (id) => `/clients/${id}/edit`
