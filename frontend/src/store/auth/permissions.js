import { reactive } from 'vue'
import { createPermissionStore } from '@/core/auth/permission'

const store = reactive(createPermissionStore())

export const usePermissionStore = () => {
  const ensureMenusLoaded = async () => {
    if (!store.items.length) {
      await store.get({ params: { is_menu: true } })
    }
  }

  return {
    ...store,
    ensureMenusLoaded,
  }
}
