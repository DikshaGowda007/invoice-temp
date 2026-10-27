import axiosClient from './axios'
import { API } from '@/constants/api'

export const dashboardApi = {
  summary: (range) => axiosClient.post(API.DASHBOARD.SUMMARY, { range }),
}
