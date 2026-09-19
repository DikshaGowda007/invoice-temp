import axiosClient from './axios'
import { API } from '@/constants/api'

export const invoiceApi = {
  list: () => axiosClient.post(API.INVOICES.LIST),
  get: (id) => axiosClient.post(API.INVOICES.GET, { id }),
  add: (payload) => axiosClient.post(API.INVOICES.ADD, payload),
  clone: (id) => axiosClient.post(API.INVOICES.CLONE, { id }),
  edit: (payload) => axiosClient.post(API.INVOICES.EDIT, payload),
  delete: (id) => axiosClient.post(API.INVOICES.DELETE, { id }),
  updateStatus: (payload) => axiosClient.post(API.INVOICES.UPDATE_STATUS, payload),
  addLineItem: (payload) => axiosClient.post(API.INVOICES.LINE_ITEM_ADD, payload),
  updateLineItem: (payload) => axiosClient.post(API.INVOICES.LINE_ITEM_UPDATE, payload),
  deleteLineItem: (lineItemId) =>
    axiosClient.post(API.INVOICES.LINE_ITEM_DELETE, { line_item_id: lineItemId }),
}
