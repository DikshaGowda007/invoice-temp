export const API = {
  AUTH: {
    REGISTER: '/auth/register',
    LOGIN: '/auth/login',
    LOGOUT: '/auth/logout',
  },
  CLIENTS: {
    LIST: '/clients/list',
    GET: '/clients/get',
    ADD: '/clients/add',
    EDIT: '/clients/edit',
    DELETE: '/clients/delete',
  },
  INVOICES: {
    LIST: '/invoices/list',
    GET: '/invoices/get',
    ADD: '/invoices/add',
    CLONE: '/invoices/clone',
    EDIT: '/invoices/edit',
    DELETE: '/invoices/delete',
    UPDATE_STATUS: '/invoices/update-status',
    LINE_ITEM_ADD: '/invoices/line-item/add',
    LINE_ITEM_UPDATE: '/invoices/line-item/update',
    LINE_ITEM_DELETE: '/invoices/line-item/delete',
  },
}
