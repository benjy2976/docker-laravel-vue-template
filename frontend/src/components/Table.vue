<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue'

// Tabla: renderiza headers internamente, maneja orden y paginacion, y expone
// helpers de seleccion (teclado + doble click).
const props = defineProps({
  // items: filas crudas a renderizar (ordenadas y paginadas internamente).
  items       : { type: Array, default: () => [] },
  // tag: id base para construir ids de filas para navegacion con teclado.
  tag         : { type: String, default: 'row' },
  // pageLength: numero de filas por pagina.
  pageLength  : { type: Number, default: 10 },
  // noAction: oculta el contenedor del slot actions cuando es true.
  noAction    : { type: Boolean, default: false },
  // headers: [{ title, key, sortKey?, sortable?, width?, class? }]
  headers     : { type: Array, default: () => [] },
  // defaultSort: { key, direction } o 'id'.
  defaultSort : { type: [String, Object], default: null },
  // rowKey: string o funcion para construir keys estables.
  rowKey      : { type: [String, Function], default: null },
})

const emit = defineEmits(['enter'])

const page = ref(1)
const selected = ref({
  index         : null,
  headHeight    : 0,
  clickPosition : 0,
})
let clickTimer

const tableRef = ref(null)
const headRef = ref(null)
const bodyRef = ref(null)

// Normaliza defaultSort y retorna { key, direction } o null.
const resolvedDefaultSort = computed(() => {
  if (!props.defaultSort) return null
  if (typeof props.defaultSort === 'string') {
    return { key: props.defaultSort, direction: 'desc' }
  }
  const key = props.defaultSort.key
  if (!key) return null
  const direction = String(props.defaultSort.direction || 'asc').toLowerCase()
  return { key, direction: direction === 'desc' ? 'desc' : 'asc' }
})

const sortKey = ref(null)
const sortDirection = ref('asc')

// Cuando defaultSort cambia (o en el primer render), reinicia el orden.
watch(
  () => resolvedDefaultSort.value,
  (value) => {
    if (!value?.key) {
      sortKey.value = null
      sortDirection.value = 'asc'
      return
    }
    sortKey.value = value.key
    sortDirection.value = value.direction || 'asc'
  },
  { immediate: true }
)

// isSortable: valida si el header permite orden y retorna boolean.
const isSortable = (header) => header?.sortable !== false && Boolean(header?.key)

// resolveSortAccessor: retorna sortKey si existe, si no usa key.
const resolveSortAccessor = (header) => {
  if (!header) return null
  return header.sortKey || header.key
}

// resolveSortValue: retorna el valor a ordenar segun el accessor.
const resolveSortValue = (item, accessor) => {
  if (typeof accessor === 'function') return accessor(item)
  if (typeof accessor === 'string') return item?.[accessor]
  return null
}

// compareValues: retorna el orden relativo entre dos valores.
// - null/undefined van al final
// - numeros ordenan numericamente
// - strings usan localeCompare en es con soporte numerico
const compareValues = (left, right) => {
  if (left === null || left === undefined) return right === null || right === undefined ? 0 : 1
  if (right === null || right === undefined) return -1
  if (typeof left === 'number' && typeof right === 'number') return left - right
  const leftText = String(left).toLowerCase()
  const rightText = String(right).toLowerCase()
  return leftText.localeCompare(rightText, 'es', { numeric: true, sensitivity: 'base' })
}

// toggleSort: ajusta columna/direccion activa; no retorna.
const toggleSort = (header) => {
  if (!isSortable(header)) return
  if (sortKey.value === header.key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = header.key
    sortDirection.value = 'asc'
  }
}

// sortIcon: retorna la clase del icono segun el estado actual.
const sortIcon = (header) => {
  if (!isSortable(header)) return ''
  if (sortKey.value !== header.key) return 'bi-arrow-down-up'
  return sortDirection.value === 'asc' ? 'bi-chevron-up' : 'bi-chevron-down'
}

// headerClass: retorna clases para <th> segun header.class.
const headerClass = (header) => {
  const value = header?.class
  if (Array.isArray(value)) return value.filter(Boolean).join(' ')
  if (typeof value === 'string') return value
  return ''
}

// headerStyle: retorna estilo para <th> segun header.width o null.
const headerStyle = (header) => {
  if (!header?.width) return null
  const width = typeof header.width === 'number' ? `${header.width}px` : header.width
  return { width }
}

// sortedItems: retorna items ordenados antes de paginar.
const sortedItems = computed(() => {
  if (!sortKey.value) return [...props.items]
  const header = props.headers.find(item => item.key === sortKey.value)
  const accessor = header ? resolveSortAccessor(header) : sortKey.value
  const items = [...props.items]
  items.sort((a, b) => {
    const result = compareValues(
      resolveSortValue(a, accessor),
      resolveSortValue(b, accessor)
    )
    return sortDirection.value === 'desc' ? -result : result
  })
  return items
})

// lastPage: retorna el total de paginas disponible.
const lastPage = computed(() => Math.ceil(sortedItems.value.length / props.pageLength) || 1)
// Mantiene page dentro del rango cuando la lista se reduce.
watch(
  () => lastPage.value,
  () => {
    if (page.value > lastPage.value) {
      page.value = 1
      resetSelection()
    }
  }
)

// itemsPage: retorna la pagina actual de items.
const itemsPage = computed(() => {
  const start = (page.value - 1) * props.pageLength
  return sortedItems.value.slice(start, start + props.pageLength)
})

// status: retorna el indice y item seleccionado.
const status = computed(() => {
  const index = selected.value.index
  return {
    index,
    item : index !== null ? itemsPage.value[index] : undefined,
  }
})

// resetSelection: limpia la seleccion y no retorna.
const resetSelection = () => {
  selected.value.index = null
  selected.value.clickPosition = 0
}

// moveRight: avanza una pagina y retorna true si se movio.
const moveRight = () => {
  if (page.value < lastPage.value) {
    page.value += 1
    resetSelection()
    return true
  }
  return false
}

// moveLeft: retrocede una pagina y retorna true si se movio.
const moveLeft = () => {
  if (page.value > 1) {
    page.value -= 1
    resetSelection()
    return true
  }
  return false
}

// moveStart: mueve la seleccion al inicio de la pagina y no retorna.
const moveStart = () => {
  selected.value.index = 0
  slide()
}

// moveEnd: mueve la seleccion al final de la pagina y no retorna.
const moveEnd = () => {
  selected.value.index = Math.min(props.pageLength, itemsPage.value.length) - 1
  slide()
}

// slide: hace scroll para mantener visible la seleccion y no retorna.
const slide = (tag = null) => {
  if (selected.value.index === null) return
  const row = bodyRef.value?.querySelector(`#${props.tag}${selected.value.index}`)
  if (!row) return
  const newPosition = row.offsetTop - (selected.value.headHeight || 0) - selected.value.clickPosition
  bodyRef.value.scrollTop = newPosition
  if (bodyRef.value.scrollTop !== newPosition) {
    selected.value.clickPosition = row.offsetTop - (selected.value.headHeight || 0) - bodyRef.value.scrollTop
  }
  if (tag) {
    const input = bodyRef.value.querySelector(`#${tag}${selected.value.index}`)
    input?.focus()
  }
}

// selectRow: registra la seleccion y el offset de click, no retorna.
const selectRow = (index) => {
  selected.value.index = index
  const row = bodyRef.value?.querySelector(`#${props.tag}${index}`)
  if (!row || !headRef.value) return
  selected.value.headHeight = headRef.value.offsetHeight
  selected.value.clickPosition = row.offsetTop - selected.value.headHeight - bodyRef.value.scrollTop
}

// move: mueve la seleccion por delta y no retorna.
const move = (delta, tag = null) => {
  let newIndex = 0
  if (selected.value.index !== null) {
    newIndex = selected.value.index + delta
    if (newIndex < 0) {
      newIndex = moveLeft() ? props.pageLength - 1 : 0
    }
    if (newIndex >= props.pageLength || newIndex >= itemsPage.value.length) {
      newIndex = moveRight() ? 0 : Math.min(props.pageLength, itemsPage.value.length) - 1
    }
  } else {
    newIndex = delta < 0 ? props.pageLength - 1 : 0
  }
  selected.value.index = newIndex
  slide(tag)
}

// foco: selecciona el input y activa la fila, no retorna.
const foco = (e, index) => {
  e.target.select?.()
  selectRow(index)
}

// clickRow: maneja click y doble click; no retorna.
const clickRow = (index) => {
  if (clickTimer && selected.value.index === index) {
    selectRow(index)
    emit('enter', status.value)
    clearTimeout(clickTimer)
    clickTimer = undefined
  } else {
    clickTimer = setTimeout(() => {
      clickTimer = undefined
    }, 500)
    if (index !== selected.value.index) {
      selectRow(index)
    } else {
      selected.value.index = null
    }
  }
}

// enter: emite la fila activa al presionar Enter y no retorna.
const enter = () => emit('enter', status.value)

// classRow: retorna la clase de fila segun seleccion/item.
const classRow = (index) => {
  if (selected.value.index === index) {
    return 'table-active'
  }
  return itemsPage.value[index]?.class || ''
}

// resolveRowKey: retorna una key estable para cada fila.
const resolveRowKey = (item, index) => {
  if (!props.rowKey) return index
  if (typeof props.rowKey === 'function') return props.rowKey(item) ?? index
  if (typeof props.rowKey === 'string') return item?.[props.rowKey] ?? index
  return index
}

// onBeforeUnmount: limpia el timer pendiente y no retorna.
onBeforeUnmount(() => {
  if (clickTimer) clearTimeout(clickTimer)
})
</script>

<template>
  <div
    tabindex="0"
    @keydown.up.prevent="move(-1)"
    @keydown.down.prevent="move(1)"
    @keydown.esc.prevent.stop="selected.index = null"
    @keydown.left.prevent.stop="moveLeft()"
    @keydown.right.prevent.stop="moveRight()"
    @keydown.home.prevent.stop="moveStart()"
    @keydown.end.prevent.stop="moveEnd()"
    @keydown.enter.prevent="enter"
  >
    <div v-if="!noAction" class="mb-1">
      <slot name="actions" :selected="status" ></slot>
    </div>
    <div class="table-responsive">
      <table ref="tableRef" class="table table-hover table-bordered mb-0">
        <thead ref="headRef" class="table-light">
          <tr>
            <th
              v-for="header in props.headers"
              :key="header.key || header.title"
              scope="col"
              :class="headerClass(header)"
              class="fw-normal"
              :style="headerStyle(header)"
            >
              <button
                v-if="isSortable(header)"
                type="button"
                class="btn btn-link p-0 d-inline-flex align-items-center gap-1 text-decoration-none text-black"
                :class="{ 'fw-bold': sortKey === header.key }"
                @click="toggleSort(header)"
              >
                <span>{{ header.title }}</span>
                <i class="bi" :class="sortIcon(header)"></i>
              </button>
              <span v-else>{{ header.title }}</span>
            </th>
          </tr>
        </thead>
        <tbody ref="bodyRef">
          <tr
            v-for="(item, index) in itemsPage"
            :id="`${tag}${index}`"
            :key="resolveRowKey(item, index)"
            :class="classRow(index)"
            @click.prevent.stop="clickRow(index)"
          >
            <slot name="body" :item="item" :index="index" :foco="foco" :move="move" ></slot>
          </tr>
        </tbody>
        <tfoot>
          <slot name="footer" ></slot>
        </tfoot>
      </table>
    </div>

    <nav class="mt-3" aria-label="Paginación">
      <ul class="pagination pagination-sm justify-content-end mb-0">
        <li class="page-item" :class="{ disabled: page === 1 }">
          <button class="page-link" type="button" @click="page > 1 && (page -= 1)">Anterior</button>
        </li>
        <li
          v-for="p in lastPage"
          :key="p"
          class="page-item"
          :class="{ active: p === page }"
        >
          <button class="page-link" type="button" @click="page = p">{{ p }}</button>
        </li>
        <li class="page-item" :class="{ disabled: page === lastPage }">
          <button class="page-link" type="button" @click="page < lastPage && (page += 1)">Siguiente</button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<style scoped>
.pedido-table {
  display: contents;
}
</style>
