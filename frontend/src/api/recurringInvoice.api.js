import axiosClient from './axios'
import { API } from '@/constants/api'

export const recurringInvoiceApi = {
  list: () => axiosClient.post(API.RECURRING_INVOICES.LIST),
  get: (id) => axiosClient.post(API.RECURRING_INVOICES.GET, { id }),
  add: (payload) => axiosClient.post(API.RECURRING_INVOICES.ADD, payload),
  clone: (id) => axiosClient.post(API.RECURRING_INVOICES.CLONE, { id }),
  edit: (payload) => axiosClient.post(API.RECURRING_INVOICES.EDIT, payload),
  delete: (id) => axiosClient.post(API.RECURRING_INVOICES.DELETE, { id }),
  updateStatus: (payload) => axiosClient.post(API.RECURRING_INVOICES.UPDATE_STATUS, payload),
  addLineItem: (payload) => axiosClient.post(API.RECURRING_INVOICES.LINE_ITEM_ADD, payload),
  updateLineItem: (payload) => axiosClient.post(API.RECURRING_INVOICES.LINE_ITEM_UPDATE, payload),
  deleteLineItem: (lineItemId) =>
    axiosClient.post(API.RECURRING_INVOICES.LINE_ITEM_DELETE, { line_item_id: lineItemId }),
}
