import axiosClient from './axios'
import { API } from '@/constants/api'

export const authApi = {
  register: (payload) => axiosClient.post(API.AUTH.REGISTER, payload),
  login: (payload) => axiosClient.post(API.AUTH.LOGIN, payload),
}
