import axiosClient from './axios'
import { API } from '@/constants/api'

export const invoiceApi = {
  list: () => axiosClient.post(API.INVOICES.LIST),
  get: (id) => axiosClient.post(API.INVOICES.GET, { id }),
  download: (id) => axiosClient.post(API.INVOICES.DOWNLOAD, { id }),
  downloadStatus: (requestId) => axiosClient.post(API.INVOICES.DOWNLOAD_STATUS, { request_id: requestId }),
  downloadResult: async (requestId) => {
    try {
      return await axiosClient.post(
        API.INVOICES.DOWNLOAD_RESULT,
        { request_id: requestId },
        { responseType: 'blob' },
      )
    } catch (error) {
      if (!(error instanceof Blob)) throw error

      let parsed
      try {
        parsed = JSON.parse(await error.text())
      } catch {
        throw error
      }
      throw parsed
    }
  },
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
