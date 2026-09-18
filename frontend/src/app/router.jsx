import { createBrowserRouter } from 'react-router-dom'
import { ROUTES } from '@/utils/routePaths'

import GuestRoute from '@/routes/GuestRoute'
import ProtectedRoute from '@/routes/ProtectedRoute'

import HomePage from '@/features/home/pages/HomePage'
import LoginPage from '@/features/auth/pages/LoginPage'
import RegisterPage from '@/features/auth/pages/RegisterPage'
import ClientFormPage from '@/features/clients/pages/ClientFormPage'
import ClientListPage from '@/features/clients/pages/ClientListPage'
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
          { path: ROUTES.INVOICES, element: <ComingSoonPage /> },
          { path: ROUTES.SETTINGS, element: <ComingSoonPage /> },
        ],
      },
    ],
  },

  { path: '*', element: <ComingSoonPage /> },
])

export default router
