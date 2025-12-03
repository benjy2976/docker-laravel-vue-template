import Model from '@/pmsg/src/Model'
import api from '@/axios'

const permissionConfig = {
  alias: 'permissions',
  route: '/api/permissions',
  hash: false,
  sync: false,
  store: false,
  methods: null,
  hasKey: true,
  key: 'id',
  name: 'name',
  maxRelationsResolve: 1,
  relations: [],
  selectable: false,
  default: {
    id: null,
    name: null,
    guard_name: null,
    is_menu: false,
    menu_label: null,
    menu_path: null,
    icon: null,
    parent_id: null,
    sort_order: 0,
    created_at: null,
    updated_at: null,
  },
  params: { modeljs: true },
  paginate: false,
  pagination: { current_page: 1, per_page: 10 },
}

export const Permission = new Model(permissionConfig, api)

export const createPermissionStore = (state = null, getters = null, actions = null) =>
  Permission.getStore(state, getters, actions)
