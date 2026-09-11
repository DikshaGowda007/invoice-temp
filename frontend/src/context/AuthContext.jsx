import { createContext, useContext, useState } from 'react'
import { storage } from '@/services/storage.service'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(() => storage.getUser())
  const [token, setToken] = useState(() => storage.getToken())

  const login = (userData, jwtToken) => {
    storage.setToken(jwtToken)
    storage.setUser(userData)
    setToken(jwtToken)
    setUser(userData)
  }

  const isAuthenticated = !!token

  return (
    <AuthContext.Provider value={{ user, token, isAuthenticated, login }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  const context = useContext(AuthContext)
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider')
  }
  return context
}
