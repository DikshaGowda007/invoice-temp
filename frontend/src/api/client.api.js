import axiosClient from './axios'
import { API } from '@/constants/api'

export const clientApi = {
  list: () => axiosClient.post(API.CLIENTS.LIST),
  get: (id) => axiosClient.post(API.CLIENTS.GET, { id }),
  add: (payload) => axiosClient.post(API.CLIENTS.ADD, payload),
  edit: (payload) => axiosClient.post(API.CLIENTS.EDIT, payload),
  delete: (id) => axiosClient.post(API.CLIENTS.DELETE, { id }),
}
