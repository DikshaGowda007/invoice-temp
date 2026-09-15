import { createBrowserRouter } from 'react-router-dom'
import { ROUTES } from '@/utils/routePaths'

import GuestRoute from '@/routes/GuestRoute'
import ProtectedRoute from '@/routes/ProtectedRoute'

import HomePage from '@/features/home/pages/HomePage'
import LoginPage from '@/features/auth/pages/LoginPage'
import RegisterPage from '@/features/auth/pages/RegisterPage'
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
    children: [{ path: ROUTES.HOME, element: <HomePage /> }],
  },

  { path: '*', element: <ComingSoonPage /> },
])

export default router
