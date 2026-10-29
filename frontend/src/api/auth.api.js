import axiosClient from './axios'
import { API } from '@/constants/api'

export const authApi = {
  register: (payload) => axiosClient.post(API.AUTH.REGISTER, payload),
  login: (payload) => axiosClient.post(API.AUTH.LOGIN, payload),
  logout: () => axiosClient.post(API.AUTH.LOGOUT),
  forgotPassword: (payload) => axiosClient.post(API.AUTH.FORGOT_PASSWORD, payload),
  resetPassword: (payload) => axiosClient.post(API.AUTH.RESET_PASSWORD, payload),
}
