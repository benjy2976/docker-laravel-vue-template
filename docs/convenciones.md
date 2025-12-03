# Convenciones

- Idioma: español en comentarios y documentación; **nombres de módulos, clases, funciones, variables, rutas y archivos en inglés** (punto único para cambiar el idioma de nombres si se decide otra convención).
- Nombres:
  - Componentes Vue en PascalCase; composables en camelCase con prefijo `use`.
  - Stores pmsg en `src/store/...`, modelos en `src/core/...`.
  - Rutas API REST con `apiResource`.
- Comentarios como micro-pasos antes de bloques lógicos/complejos, ej.:
  ```js
  // Paso 1: validar payload
  // Paso 2: llamar servicio externo
  // Paso 3: devolver respuesta
  ```
- Estilos: usar clases Bootstrap y variables; evitar inline salvo prototipos rápidos.
- Imports: preferir alias configurados en Vite (`@`, `@core`, `@store`, `@pmsg`).
- Validación backend: usar FormRequest/validate; respuestas JSON claras.
- Pruebas: documentar comandos en `docs/procedimientos.md`; usar datos seeders para roles/permisos.
- Al proponer código o cambios:
  - Respetar estas convenciones y pedir aprobación antes de aplicarlos.
  - Incluir micro-prompts previos a cada bloque lógico.
  - Nombres descriptivos (en inglés) y consistentes.
  - Si se crea una función/fragmento relevante, proponer su documentación y recordar actualizar la memoria (`docs/`, `.github/`) si aplica.
  - Si se detecta una práctica/patrón no documentado, notificar y sugerir actualización en este archivo o en `docs/procedimientos.md`.

TODO: Definir formato de commits y lints/formato de código.
