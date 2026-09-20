import { createBrowserRouter } from 'react-router-dom'
import { ROUTES } from '@/utils/routePaths'

import GuestRoute from '@/routes/GuestRoute'
import ProtectedRoute from '@/routes/ProtectedRoute'

import HomePage from '@/features/home/pages/HomePage'
import LoginPage from '@/features/auth/pages/LoginPage'
import RegisterPage from '@/features/auth/pages/RegisterPage'
import ClientFormPage from '@/features/clients/pages/ClientFormPage'
import ClientListPage from '@/features/clients/pages/ClientListPage'
import InvoiceDetailPage from '@/features/invoices/pages/InvoiceDetailPage'
import InvoiceFormPage from '@/features/invoices/pages/InvoiceFormPage'
import InvoiceListPage from '@/features/invoices/pages/InvoiceListPage'
import RecurringInvoiceDetailPage from '@/features/recurringInvoices/pages/RecurringInvoiceDetailPage'
import RecurringInvoiceFormPage from '@/features/recurringInvoices/pages/RecurringInvoiceFormPage'
import RecurringInvoiceListPage from '@/features/recurringInvoices/pages/RecurringInvoiceListPage'
import { AppLayout } from '@/layouts/AppLayout'
import ComingSoonPage from './ComingSoonPage'

const router = createBrowserRouter([
  {
    element: <GuestRoute />,
    children: [
      { path: ROUTES.LOGIN, element: <LoginPage /> },
      { path: ROUTES.REGISTER, element: <RegisterPage /> },
    ],
  },

  {
    element: <ProtectedRoute />,
    children: [
      {
        element: <AppLayout />,
        children: [
          { path: ROUTES.HOME, element: <HomePage /> },
          { path: ROUTES.CLIENTS, element: <ClientListPage /> },
          { path: ROUTES.CLIENT_NEW, element: <ClientFormPage /> },
          { path: ROUTES.CLIENT_EDIT, element: <ClientFormPage /> },
          { path: ROUTES.INVOICES, element: <InvoiceListPage /> },
          { path: ROUTES.INVOICE_NEW, element: <InvoiceFormPage /> },
          { path: ROUTES.INVOICE_EDIT, element: <InvoiceFormPage /> },
          { path: ROUTES.INVOICE_DETAIL, element: <InvoiceDetailPage /> },
          { path: ROUTES.RECURRING_INVOICES, element: <RecurringInvoiceListPage /> },
          { path: ROUTES.RECURRING_INVOICE_NEW, element: <RecurringInvoiceFormPage /> },
          { path: ROUTES.RECURRING_INVOICE_EDIT, element: <RecurringInvoiceFormPage /> },
          { path: ROUTES.RECURRING_INVOICE_DETAIL, element: <RecurringInvoiceDetailPage /> },
          { path: ROUTES.SETTINGS, element: <ComingSoonPage /> },
        ],
      },
    ],
  },

  { path: '*', element: <ComingSoonPage /> },
])

export default router
