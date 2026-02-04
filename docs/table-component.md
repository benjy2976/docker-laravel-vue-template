# Manual del componente Table

Este documento explica como usar el componente `Table` ubicado en:
- `frontend/src/components/Table.vue`

El componente renderiza headers internamente, maneja orden y paginacion, y
expone helpers de seleccion por teclado.

## API principal

### Props

- `items` (Array, requerido): lista de filas a renderizar.
- `headers` (Array, requerido): definiciones de columnas para header y orden.
- `defaultSort` (Object | String, opcional):
  - Objeto: `{ key: 'id', direction: 'desc' }`
  - String: `'id'` (por defecto `desc`)
- `pageLength` (Number, opcional): filas por pagina (default: `10`).
- `noAction` (Boolean, opcional): oculta el contenedor del slot `actions`.
- `rowKey` (String | Function, opcional): key estable para cada fila.
- `tag` (String, opcional): base para ids de fila (default: `row`).

### Estructura de header

Cada entrada en `headers` soporta:

- `title` (String, requerido): texto visible en el header.
- `key` (String, requerido): id de columna y estado del orden.
- `sortKey` (String | Function, opcional): accessor custom para ordenar.
  - Si se omite, el componente usa `key`.
- `sortable` (Boolean, opcional): si es `false` no muestra iconos ni ordena.
- `width` (String | Number, opcional): ancho para `th` (px o string CSS).
- `class` (String | Array, opcional): clases custom para `th`.

### Reglas de orden

- El orden se aplica antes de la paginacion.
- Si `sortKey` es funcion, se usa el valor que retorna.
- Si `sortKey` es string, se usa `row[sortKey]`.
- Si `sortKey` no existe, se usa `key`.
- `null`/`undefined` van al final.
- Numeros ordenan numericamente; texto usa `localeCompare` en `es` con soporte numerico.

## Slots

- `actions`: recibe `{ selected }`, usado para botones superiores.
- `body`: recibe `{ item, index, foco, move }`.
- `footer`: usado para filas de estado vacio.

## Ejemplo 1: tabla simple, orden por id desc

```vue
<script setup>
import Table from '@/components/Table.vue'

const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Nombre', key: 'name' },
  { title: 'Estado', key: 'status' },
]
</script>

<template>
  <Table
    :items="items"
    :headers="headers"
    :default-sort="{ key: 'id', direction: 'desc' }"
  >
    <template #body="{ item }">
      <td>{{ item.id }}</td>
      <td>{{ item.name }}</td>
      <td>{{ item.status }}</td>
    </template>
    <template #footer>
      <tr v-if="!items.length">
        <td colspan="3" class="text-center text-muted">Sin resultados</td>
      </tr>
    </template>
  </Table>
</template>
```

## Ejemplo 2: orden por valor derivado

```vue
const headers = [
  { title: 'Usuario', key: 'user', sortKey: (row) => row.username || row.email || '' },
  { title: 'Email', key: 'email' },
]
```

## Ejemplo 3: deshabilitar orden en una columna

```vue
const headers = [
  { title: 'Nombre', key: 'name' },
  { title: 'Imagen', key: 'image', sortable: false },
]
```

## Ejemplo 4: ancho y alineacion en header

```vue
const headers = [
  { title: 'Monto', key: 'amount', class: 'text-end', width: 120 },
  { title: 'Descripcion', key: 'description', width: '40%' },
]
```

## Ejemplo 5: rowKey

```vue
<Table
  :items="items"
  :headers="headers"
  row-key="id"
/>
```

O con funcion:

```vue
<Table
  :items="items"
  :headers="headers"
  :row-key="(row) => `${row.type}-${row.id}`"
/>
```

## Notas

- El header se controla solo por `headers`; no se usa el slot `#header`.
- Los iconos aparecen solo si `sortable !== false`.
- Usa `class` para alineacion con Bootstrap (ej: `text-end`).
- Si quieres mantener el orden actual, usa `sortable: false` y omite `defaultSort`.
