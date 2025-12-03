# Guía para la IA

- Responder en español, conciso y con pasos claros.
- Verificar siempre impacto en memoria (`docs/`, `.github/`). Si hay nuevas convenciones/patrones, proponer actualización y esperar aprobación.
- Usar alias y estructura definida (ver `docs/arquitectura.md`).
- Para tareas de código, añadir micro-comentarios de pasos previos en bloques complejos.
- No inventar endpoints ni datos; usar `TODO` o pedir aclaración.
- Respetar flujos críticos: autenticación Sanctum (csrf-cookie → login/register → api/user), roles/permisos (Spatie), menús dinámicos basados en permisos.
- Si falta documentación de una regla usada, notificar y sugerir dónde agregarla.
