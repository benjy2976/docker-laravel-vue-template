# Guía para la IA

- Responder en español, conciso y con pasos claros.
- Verificar siempre impacto en memoria (`docs/`, `.github/`). Si hay nuevas convenciones/patrones, proponer actualización y esperar aprobación.
- Usar alias y estructura definida (ver `docs/arquitectura.md`).
- Para tareas de código, añadir micro-comentarios de pasos previos en bloques complejos.
- No inventar endpoints ni datos; usar `TODO` o pedir aclaración.
- Respetar flujos críticos: autenticación Sanctum (csrf-cookie → login/register → api/user), roles/permisos (Spatie), menús dinámicos basados en permisos.
- Para reglas específicas de módulos (migraciones/seeders, rutas `apiResource`, core model/store, adaptadores pmsg), seguir los detalles en `docs/procedimientos.md`, `docs/convenciones.md` y `.github/instructions/*`.
- Si falta documentación de una regla usada, notificar y sugerir dónde agregarla.
# sobre el analisis de reglas en la memoria
- cuando se te solicite revisar reglas en la memoria siempre las aras una a una, y compararas las ideas nuevas tambien una a una para posicionarlas donde deben de estar y evitar redundancias
- cuando el usuario apruebe una propuesta de cambio, implementa exactamente esa propuesta; no reimagines soluciones distintas sin volver a pedir aprobación
- cuando se vaya a incluir una regla siempre se tiene que verificar coliciones con las reglas existentes y de encontrarse consultar al usuario
