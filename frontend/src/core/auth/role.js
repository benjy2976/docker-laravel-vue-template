import Model from '@/pmsg/src/Model'
import api from '@/axios'

const roleConfig = {
  alias: 'roles',
  route: '/api/roles',
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
    permissions: [],
    created_at: null,
    updated_at: null,
  },
  params: { modeljs: true },
  paginate: false,
  pagination: { current_page: 1, per_page: 10 },
}

export const Role = new Model(roleConfig, api)

export const createRoleStore = (state = null, getters = null, actions = null) =>
  Role.getStore(state, getters, actions)
