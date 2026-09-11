import { createRoot } from 'react-dom/client'
import { RouterProvider } from 'react-router-dom'
import { StrictMode } from 'react'
import router from '@/app/router'
import { Providers } from '@/app/providers'
import ErrorBoundary from '@/components/common/ErrorBoundary'
import './index.css'

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <ErrorBoundary>
      <Providers>
        <RouterProvider router={router} />
      </Providers>
    </ErrorBoundary>
  </StrictMode>,
)
