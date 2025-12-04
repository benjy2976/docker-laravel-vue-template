applyTo:
  - backend/**

# Instrucciones específicas backend
- Seguir convenciones de `docs/convenciones.md` y estructura de rutas/controladores descrita en `docs/arquitectura.md`.
- Mantener endpoints RESTful (`apiResource`) y middleware coherente (auth:sanctum, roles/permisos).
- Migraciones y seeders deben ser idempotentes y claros; documenta nuevos campos en memoria con aprobación.
- Al añadir servicios (ej. mailpit), refleja variables en `.env` y `docs/entorno.md` (previa aprobación).
- Usa comentarios de micro-pasos en controladores/servicios complejos.
- Pruebas: ejecutar y documentar comandos en `docs/procedimientos.md` cuando corresponda.
