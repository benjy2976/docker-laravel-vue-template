applyTo:
  - src/**

# Instrucciones específicas frontend
- Seguir estructura y estilos descritos en `docs/convenciones.md`.
- Usar alias Vite configurados (`@`, `@core`, `@store`, `@pmsg`); preferir rutas absolutas configuradas.
- Mantener componentes modulares (Navbar, Sidebar, Content, etc.) y layouts en `src/layout`.
- Estado: preferir stores centralizados (pmsg/Pinia) en `src/store/**`.
- Estilos: respetar `src/style.css` y guías de diseño, usar variables CSS/Bootstrap cuando aplique.
- Al agregar rutas, actualiza `src/router/index.js` y protege con metadatos (`requiresAuth`, `hideChrome`) según convención.
- Micro-pasos en comentarios antes de bloques complejos.
