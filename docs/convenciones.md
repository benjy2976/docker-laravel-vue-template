# Convenciones

- Idioma: español en comentarios y documentación; **nombres de módulos, clases, funciones, variables, rutas, permisos y archivos en inglés** (no nombrar módulos/archivos en español aunque las etiquetas visibles estén en español; si se cambia la convención, hacerlo en un solo punto).
- ASCII: en comentarios y codigo se usa ASCII por defecto; en textos visibles de UI se permiten tildes y signos de apertura cuando mejoran la claridad.
- Nombres:
  - Componentes Vue en PascalCase; composables en camelCase con prefijo `use`.
  - Stores @benjy2976/pmsg en `src/stores/...`, modelos en `src/core/...`.
  - Rutas API REST con `apiResource`.
- Layout base de módulos frontend (admin/catálogos): usar `<section class="p-3"><div class="card card-body">...</div></section>` con header simple (título `h5` + descripción) y fila de acciones `d-flex flex-column flex-md-row justify-content-end gap-2` con filtro y botón principal.
- Formularios en modales (frontend): usar `modal-header` con título + `btn-close`, `modal-body` con `<form @submit.prevent="submit">` y grilla `row g-3`, y acciones en `modal-footer` fijo con `d-flex justify-content-end align-items-center`; botones Cancelar/Guardar con `@click="submit"` y sin `id` en `<form>` ni atributo `form`.
  Ejemplo base:
  ```vue
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Título</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <!-- campos -->
          </div>
        </form>
      </div>
      <div class="modal-footer d-flex justify-content-end align-items-center">
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-outline-secondary" @click="close">Cancelar</button>
          <button type="button" class="btn btn-primary" @click="submit">Guardar</button>
        </div>
      </div>
    </div>
  </div>
  ```
- Comentarios en funciones (frontend y backend): toda funcion/metodo/computed debe incluir un micro-comentario breve que indique su objetivo y el resultado esperado (return).
- Comentarios como micro-pasos antes de bloques logicos/complejos, ej.:
  ```js
  // Paso 1: validar payload
  // Paso 2: llamar servicio externo
  // Paso 3: devolver respuesta
  ```
- Estilos: usar clases Bootstrap y variables; evitar inline salvo prototipos rápidos que no se puedan solucionar usando clases de Bootstrap.
- Listados: para tablas/listas en frontend usar el componente compartido `@/components/Table.vue` como estándar. Evitar tablas ad-hoc salvo justificación explícita. Definir columnas via `headers` (title, key, sortKey?, sortable?, width?, class?) y ordenar en el componente con `defaultSort` (string u objeto). Usar `sortKey` solo si el valor es derivado; desactivar orden con `sortable: false`; no usar slot `#header`.
- Estructura de módulos frontend: crear módulos en `src/modules/<namespace>/<module>/` más su core en `@core/<namespace>/` y store en `@stores/<namespace>/`, usando los alias de Vite. No crear carpetas sueltas (`store`, `pages/<...>`) fuera de esta estructura.
- Contexto de módulos frontend (opcional): se puede crear un archivo `context.md` en `src/modules/<namespace>/<module>/` para documentar propósito, flujos, dependencias (stores/props/eventos) y reglas de negocio. Este archivo es opcional y no sustituye la documentación global.
- Rutas frontend: registrar nuevas rutas en `router/admin/*.js`, con paths en inglés (kebab-case) y meta `permissions: ['<module>.view']` coherente con los permisos del backend.
- Permisos/menús: usar el patrón `<module>.(view|create|edit|delete)` y entradas de menú hijas de Catálogos/Administración en la seeder de permisos con `menu_path` en inglés.
- Imports: preferir alias configurados en Vite (`@`, `@core`, `@stores`); evita rutas relativas (`../`) al agregar módulos o componentes en `src`.
- Estan permitidas las importaciones que tengan (`./`) por que representan a archivos en el mismo directorio
- Validación backend: usar `$request->validate()` en controladores CRUD; reservar FormRequest para validaciones reutilizables o complejas. Respuestas JSON claras.
- Control de permisos (backend): en cada controlador API aplicar middleware `permission:<module>.view/create/edit/delete` en el constructor; no usar `role:admin` en las rutas.
- Control de permisos (frontend): renderizar botones de crear/editar/eliminar solo si `auth.can('<module>.create|edit|delete')` (usar `v-if`, no solo deshabilitar). Asegurar que las rutas tengan `meta.permissions: ['<module>.view']`.
- Auth frontend: usar `useAuth` desde `@stores/auth` y su helper `can(permission)` para validar permisos en componentes (no replicar lógica en cada módulo).
- Estado (Pinia + pmsg):
  - La plataforma usa Pinia como gestor de estado. La libreria `@benjy2976/pmsg` estandariza la generacion de stores, por lo que todos los stores del sistema heredan la misma estructura de getters y actions. No existe un modulo "pmsg"; estas reglas aplican a todo el sistema.
  - Fuente unica de verdad: priorizar los estados de Pinia generados por pmsg; evitar duplicar datos en `ref` locales salvo necesidad justificada.
  - Acciones pmsg: `create`, `update`, `delete`, `get`, `show` deben ejecutarse via store para mantener el estado sincronizado.
  - Separacion de conceptos: la obtencion/sincronizacion de datos (actions) es independiente de como se muestran/derivan (getters/computed). No asumir secuencialidad estricta (ej. `show` y `find` pueden dispararse en tiempos distintos).
  - Estrategia de carga: usar la accion mas adecuada (`get`, `show`, `checkAsinc`, `sync*`) segun el caso; si se propone otra estrategia, consultar antes.
  - Acceso directo al modelo pmsg (sin store) solo cuando el modulo no tenga store o haya un motivo claro, y se documenta.
  - Getters pmsg (detalle):
    - `list`: retorna el arreglo de items, aplicando `resolve` a cada elemento.
    - `find(id, level = 1)`: busca un item por `id`, aplica `resolve` y devuelve el default si no existe.
    - `filter(fn, level = 1)`: filtra items con `fn` y devuelve la lista resuelta.
    - `selected`: devuelve `itemSelected` resuelto si hay seleccion activa; si no, retorna `false`.
    - `name(id)`: devuelve el atributo `name` del item encontrado o `null` si no existe.
    - `default`: devuelve el objeto default del modelo.
  - Actions pmsg (detalle):
    - `get(params, pagination)`: consulta la lista desde el backend y sincroniza `items`.
    - `show(id)`: consulta un item puntual y lo sincroniza en `items`.
    - `create(item)`: crea en backend y agrega/sincroniza en `items`.
    - `update(item)`: actualiza en backend y sincroniza en `items`.
    - `delete(item)`: elimina en backend y sincroniza `items`.
    - `setItems(items)`: reemplaza completamente la lista local.
    - `sync(item|items)`: decide si sincroniza un item o una lista segun el formato recibido.
    - `syncItem(item)`: sincroniza un solo item con `items`.
    - `syncItems(items)`: sincroniza una lista con `items`.
    - `checkAsinc(key|keys)`: agrega llaves a una cola y dispara una carga batched asincrona.
- Relaciones en respuestas backend: por defecto NO incluir `with()`/`load()` en controladores. Si consideras necesario retornar datos relacionados, primero consulta y documenta la justificación; solo se añaden relaciones cuando se solicita explícitamente.
- Relaciones via `with` (backend): si se habilita `with` en un endpoint, normalizar el parametro (CSV/array, trim, dedupe), aplicar allowlist y seleccionar columnas via closures. No cargar relaciones implicitas; solo las pedidas de forma exacta (ej. `details.product`).
- Documentación obligatoria en backend (modelos/controladores/servicios nuevos):
  - Toda función pública debe llevar docblock en español con propósito, parámetros (`@param Tipo $var`), retorno (`@return Tipo`), y si aplica excepciones (`@throws`). Ejemplo:
    ```php
    /**
     * Ajusta el estado de un recurso con validación previa.
     *
     * @param User $user Usuario objetivo.
     * @param int $amount Cantidad a procesar.
     * @return int Resultado del ajuste.
     *
     * @throws \DomainException si la operación no es válida.
     */
    ```
  - Relaciones en modelos deben incluir docblock indicando tipo de relación y tabla destino. Ejemplo:
    ```php
    /**
     * Relación: el recurso pertenece a otro recurso (belongsTo -> related_table).
     */
    public function related(): BelongsTo
    {
        return $this->belongsTo(Related::class);
    }
    ```
- Middlewares backend: los middleware de permisos se declaran en el constructor del controlador (`$this->middleware('permission:...')`); no se registran en rutas ni en otro archivo.
- Migraciones backend: evitar `cascadeOnDelete` en llaves foraneas; usar `restrictOnDelete`. Si se requiere `cascadeOnDelete` o `nullOnDelete`, consultar antes de aplicarlo.
- Migraciones backend (comentarios): los comentarios del docblock y pasos logicos deben estar en español. Cuando un campo numerico representa estados o tipos (ej. `status`, `type`, `account_type`), usar `->comment('1=valor, 2=valor, ...')` para documentar los valores posibles directamente en la columna de la base de datos. Ejemplo:
  ```php
  $table->tinyInteger('status')->default(1)
      ->comment('1=disponible, 2=reservado, 3=asignado, 4=defectuoso, 5=archivado');
  ```
- Pruebas: documentar comandos en `docs/procedimientos.md`; usar datos seeders para roles/permisos.
- Al proponer código o cambios:
  - Respetar estas convenciones y pedir aprobación antes de aplicarlos.
  - Incluir micro-prompts previos a cada bloque lógico.
  - Nombres descriptivos (en inglés) y consistentes.
  - Si se crea una función/fragmento relevante, proponer su documentación y recordar actualizar la memoria (`docs/`, `.github/`) si aplica.
  - Si se detecta una práctica/patrón no documentado, notificar y sugerir actualización en este archivo o en `docs/procedimientos.md`.
- Estilos de filtros/búsqueda: usar la clase global `.search-box` definida en `src/style.css` (ancho base 320px, 100% en móvil) en lugar de definir estilos locales en cada filtro para evitar duplicación y mantener consistencia.
- Carga única y filtrado local (frontend, catálogos pequeños): si el dataset es acotado (sin paginación y <~500-1000 registros), se permite cargar todo con una sola llamada `store.get()` y filtrar en el frontend. Regla: (a) solo llamar `get()` en el componente dueño del módulo; (b) si necesitas datos de otro módulo/store, consulta antes de agregar `get()` para planificar y evitar duplicar cargas; (c) no disparar `get()` en cada cambio de filtro/búsqueda, solo una vez al montar.
- Datos relacionados entre módulos (frontend): usar el store del módulo relacionado como única fuente de verdad; las relaciones se piden de forma explícita con `with` (ej: `with=roles`) y el backend no debe devolver relaciones si no se solicitan. Derivar opciones en computeds. Evitar `axios` directo o mapas locales cuando el store ya expone la lista.
- Listas por IDs (frontend): cuando se envíen listas de IDs en queries (`ids`, `model_ids`, etc.), asegurar IDs únicos en el frontend (dedupe con `Set`) antes de llamar a `store.get()` o `api`.
- Normalización de listas (backend): para parámetros con listas (`ids`, `model_ids`, `roles`, `with`, `include`), aplicar siempre el mismo saneado: remover espacios con `preg_replace('/\s+/u', '', (string) $v)`, filtrar vacíos, castear cuando aplique y deduplicar con `unique()` antes de construir el query.
- Helpers recomendados (frontend/backend):
  - Frontend (JS):
    ```js
    // Devuelve ids unicos como string para query.
    const toUniqueIdsParam = (list = []) =>
      Array.from(new Set(list)).join(',')
    ```
  - Backend (PHP):
    ```php
    // Normaliza listas que vienen como CSV o array.
    $normalizeList = fn ($input) => collect(is_array($input) ? $input : explode(',', (string) $input))
        ->map(fn ($v) => preg_replace('/\s+/u', '', (string) $v))
        ->filter(fn ($v) => $v !== '')
        ->map(fn ($value) => (int) $value)
        ->unique()
        ->values();
    ```
- Nomenclatura de computeds (frontend):
  - Usar `filteredItems` para resultados de filtros locales.
  - Usar `mappedItems` (o nombres equivalentes claros) cuando se enriquece/mapea la lista.
  - Evitar nombres improvisados; mantén consistencia para legibilidad.

TODO: Definir formato de commits y lints/formato de código.
