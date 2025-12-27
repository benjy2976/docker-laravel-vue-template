# Convenciones

- Idioma: español en comentarios y documentación; **nombres de módulos, clases, funciones, variables, rutas, permisos y archivos en inglés** (no nombrar módulos/archivos en español aunque las etiquetas visibles estén en español; si se cambia la convención, hacerlo en un solo punto).
- Nombres:
  - Componentes Vue en PascalCase; composables en camelCase con prefijo `use`.
  - Stores @benjy2976/pmsg en `src/stores/...`, modelos en `src/core/...`.
  - Rutas API REST con `apiResource`.
- Layout base de módulos frontend (admin/catálogos): usar `<section class="p-3"><div class="card card-body">...</div></section>` con header simple (título `h5` + descripción) y fila de acciones `d-flex flex-column flex-md-row justify-content-end gap-2` con filtro y botón principal.
- Comentarios como micro-pasos antes de bloques lógicos/complejos, ej.:
  ```js
  // Paso 1: validar payload
  // Paso 2: llamar servicio externo
  // Paso 3: devolver respuesta
  ```
- Estilos: usar clases Bootstrap y variables; evitar inline salvo prototipos rápidos que no se puedan solucionar usando clases de Bootstrap.
- Listados: para tablas/listas en frontend usar el componente compartido `@/components/Table.vue` como estándar. Evitar tablas ad-hoc salvo justificación explícita.
- Estructura de módulos frontend: crear módulos en `src/modules/<namespace>/<module>/` más su core en `@core/<namespace>/` y store en `@stores/<namespace>/`, usando los alias de Vite. No crear carpetas sueltas (`store`, `pages/<...>`) fuera de esta estructura.
- Rutas frontend: registrar nuevas rutas en `router/admin/*.js`, con paths en inglés (kebab-case) y meta `permissions: ['<module>.view']` coherente con los permisos del backend.
- Permisos/menús: usar el patrón `<module>.(view|create|edit|delete)` y entradas de menú hijas de Catálogos/Administración en la seeder de permisos con `menu_path` en inglés.
- Imports: preferir alias configurados en Vite (`@`, `@core`, `@stores`); evita rutas relativas (`../`) al agregar módulos o componentes en `src`.
- Estan permitidas las importaciones que tengan (`./`) por que representan a archivos en el mismo directorio
- Validación backend: usar FormRequest/validate; respuestas JSON claras.
- Control de permisos (backend): en cada controlador API aplicar middleware `permission:<module>.view/create/edit/delete` en el constructor; no usar `role:admin` en las rutas.
- Control de permisos (frontend): renderizar botones de crear/editar/eliminar solo si `auth.can('<module>.create|edit|delete')` (usar `v-if`, no solo deshabilitar). Asegurar que las rutas tengan `meta.permissions: ['<module>.view']`.
- Auth frontend: usar `useAuth` desde `@stores/auth` y su helper `can(permission)` para validar permisos en componentes (no replicar lógica en cada módulo).
- Relaciones en respuestas backend: por defecto NO incluir `with()`/`load()` en controladores. Si consideras necesario retornar datos relacionados, primero consulta y documenta la justificación; solo se añaden relaciones cuando se solicita explícitamente.
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
- Pruebas: documentar comandos en `docs/procedimientos.md`; usar datos seeders para roles/permisos.
- Al proponer código o cambios:
  - Respetar estas convenciones y pedir aprobación antes de aplicarlos.
  - Incluir micro-prompts previos a cada bloque lógico.
  - Nombres descriptivos (en inglés) y consistentes.
  - Si se crea una función/fragmento relevante, proponer su documentación y recordar actualizar la memoria (`docs/`, `.github/`) si aplica.
  - Si se detecta una práctica/patrón no documentado, notificar y sugerir actualización en este archivo o en `docs/procedimientos.md`.
- Estilos de filtros/búsqueda: usar la clase global `.search-box` definida en `src/style.css` (ancho base 320px, 100% en móvil) en lugar de definir estilos locales en cada filtro para evitar duplicación y mantener consistencia.
- Carga única y filtrado local (frontend, catálogos pequeños): si el dataset es acotado (sin paginación y <~500-1000 registros), se permite cargar todo con una sola llamada `store.get()` y filtrar en el frontend. Regla: (a) solo llamar `get()` en el componente dueño del módulo; (b) si necesitas datos de otro módulo/store, consulta antes de agregar `get()` para planificar y evitar duplicar cargas; (c) no disparar `get()` en cada cambio de filtro/búsqueda, solo una vez al montar.
- Nomenclatura de computeds (frontend):
  - Usar `filteredItems` para resultados de filtros locales.
  - Usar `mappedItems` (o nombres equivalentes claros) cuando se enriquece/mapea la lista.
  - Evitar nombres improvisados; mantén consistencia para legibilidad.

TODO: Definir formato de commits y lints/formato de código.
