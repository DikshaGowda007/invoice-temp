import axios from 'axios'
import { storage } from '@/services/storage.service'
import { ROUTES } from '@/utils/routePaths'

console.log('[Axios] Client initialized')

const axiosClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: { 'Content-Type': 'application/json' },
  withCredentials: true,
})

axiosClient.interceptors.request.use(
  (config) => {
    const token = storage.getToken()
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    console.group('[Axios Request]')
    console.log('URL:', `${config.baseURL ?? ''}${config.url ?? ''}`)
    console.log('Method:', config.method?.toUpperCase())
    console.log('Headers:', config.headers)
    console.log('Parameters:', config.params)
    console.log('Data:', config.data)
    console.groupEnd()

    return config
  },
  (error) => {
    console.group('[Axios Request Error]')
    console.error(error)
    console.groupEnd()
    return Promise.reject(error)
  },
)

axiosClient.interceptors.response.use(
  (response) => {
    console.group('[Axios Response]')
    console.log('Status:', response.status)
    console.log('URL:', response.config.url)
    console.log('Headers:', response.headers)
    console.log('Response Data:', response.data)
    console.groupEnd()

    if (response.data?.status === 'error') {
      return Promise.reject(response.data)
    }
    return response
  },
  (error) => {
    console.group('[Axios Response Error]')

    console.error('Message:', error.message)
    console.error('Code:', error.code)

    if (error.config) {
      console.log('URL:', error.config.url)
      console.log('Method:', error.config.method?.toUpperCase())
      console.log('Request Data:', error.config.data)
      console.log('Request Headers:', error.config.headers)
    }

    if (error.response) {
      console.error('Status:', error.response.status)
      console.error('Status Text:', error.response.statusText)
      console.error('Response Headers:', error.response.headers)
      console.error('Response Data:', error.response.data)
    } else if (error.request) {
      console.error('No response received.')
      console.error(error.request)
    } else {
      console.error('Request setup failed.')
      console.error(error)
    }

    console.groupEnd()

    if (error.response?.status === 401) {
      storage.clear()
      window.location.href = ROUTES.LOGIN
    }
    return Promise.reject(error.response?.data ?? error)
  },
)

export default axiosClient
