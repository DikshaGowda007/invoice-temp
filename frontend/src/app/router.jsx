import { createBrowserRouter } from 'react-router-dom'
import { ROUTES } from '@/utils/routePaths'

import GuestRoute from '@/routes/GuestRoute'

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

  { path: '*', element: <ComingSoonPage /> },
])

export default router
