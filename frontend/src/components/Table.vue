<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue'

const props = defineProps({
  items      : { type: Array, default: () => [] },
  tag        : { type: String, default: 'row' },
  pageLength : { type: Number, default: 10 },
  noAction   : { type: Boolean, default: false },
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

const lastPage = computed(() => Math.ceil(props.items.length / props.pageLength) || 1)
watch(
  () => lastPage.value,
  () => {
    if (page.value > lastPage.value) {
      page.value = 1
      resetSelection()
    }
  }
)

const itemsPage = computed(() => {
  const start = (page.value - 1) * props.pageLength
  return props.items.slice(start, start + props.pageLength)
})

const status = computed(() => {
  const index = selected.value.index
  return {
    index,
    item : index !== null ? itemsPage.value[index] : undefined,
  }
})

const resetSelection = () => {
  selected.value.index = null
  selected.value.clickPosition = 0
}

const moveRight = () => {
  if (page.value < lastPage.value) {
    page.value += 1
    resetSelection()
    return true
  }
  return false
}

const moveLeft = () => {
  if (page.value > 1) {
    page.value -= 1
    resetSelection()
    return true
  }
  return false
}

const moveStart = () => {
  selected.value.index = 0
  slide()
}

const moveEnd = () => {
  selected.value.index = Math.min(props.pageLength, itemsPage.value.length) - 1
  slide()
}

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

const selectRow = (index) => {
  selected.value.index = index
  const row = bodyRef.value?.querySelector(`#${props.tag}${index}`)
  if (!row || !headRef.value) return
  selected.value.headHeight = headRef.value.offsetHeight
  selected.value.clickPosition = row.offsetTop - selected.value.headHeight - bodyRef.value.scrollTop
}

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

const foco = (e, index) => {
  e.target.select?.()
  selectRow(index)
}

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

const enter = () => emit('enter', status.value)

const classRow = (index) => {
  if (selected.value.index === index) {
    return 'table-active'
  }
  return itemsPage.value[index]?.class || ''
}

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
    <div v-if="!noAction" class="mb-3">
      <slot name="actions" :selected="status" ></slot>
    </div>
    <div class="table-responsive">
      <table ref="tableRef" class="table table-hover table-bordered mb-0">
        <thead ref="headRef" class="table-light">
          <slot name="header" ></slot>
        </thead>
        <tbody ref="bodyRef">
          <tr
            v-for="(item, index) in itemsPage"
            :id="`${tag}${index}`"
            :key="index"
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
