import { Navigate, Outlet } from 'react-router-dom'
import { useAuth } from '@/context/AuthContext'
import { ROUTES } from '@/utils/routePaths'

export default function GuestRoute() {
  const { isAuthenticated } = useAuth()
  return isAuthenticated ? <Navigate to={ROUTES.HOME} replace /> : <Outlet />
}
