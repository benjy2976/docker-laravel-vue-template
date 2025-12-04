applyTo:
  - backend/**

# Instrucciones específicas backend
- Seguir convenciones de `docs/convenciones.md` y estructura de rutas/controladores descrita en `docs/arquitectura.md`.
- Mantener endpoints RESTful (`apiResource`) y middleware coherente (auth:sanctum, roles/permisos).
- Migraciones/seeders: crear solo si el módulo es nuevo y no existen; si ya están, no regenerar salvo instrucción explícita. Documentar cambios relevantes.
- Rutas: agregar `apiResource` en `backend/routes/api.php` solo si no existe; si hay rutas previas/colisión de nombres, pausar y pedir confirmación antes de tocar nada.
- Al añadir servicios (ej. mailpit), refleja variables en `.env` y `docs/entorno.md` (previa aprobación).
- Usa comentarios de micro-pasos en controladores/servicios complejos.
- Pruebas: ejecutar y documentar comandos en `docs/procedimientos.md` cuando corresponda.
