applyTo:
  - src/**

# Instrucciones específicas frontend
- Seguir estructura y estilos descritos en `docs/convenciones.md`.
- Usar alias Vite configurados (`@`, `@core`, `@store`, `@pmsg`); preferir rutas absolutas configuradas.
- Modelos (core): en `frontend/src/core/...` configurar alias/route/default y mínimos; exportar el modelo y su `createModelStore` con nombres coherentes.
- Stores Pinia: en `frontend/src/store/...` usar el store generado/exportado por el core model (pmsg/Pinia). Estándar: `defineStore(model.alias, createModelStore())`; sin `reactive` extra ni alias duplicados. Overrides solo si se piden explícitamente.
- Adaptadores de respuesta/paginación los gestiona la librería pmsg; solo implementarlos manualmente si se pide de forma explícita y en los lugares indicados.
- Estilos: respetar `src/style.css` y guías de diseño, usar variables CSS/Bootstrap cuando aplique.
- Al agregar rutas, actualiza `src/router/index.js` y protege con metadatos (`requiresAuth`, `hideChrome`) según convención.
- Micro-pasos en comentarios antes de bloques complejos.
