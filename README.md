# Memoria del Proyecto

Esta memoria centraliza decisiones, convenciones y procedimientos para colaborar con la IA en español.

## Estructura
- `.github/`: instrucciones para la IA y prompts reutilizables.
- `docs/`: arquitectura, entorno, convenciones, procedimientos, API, guía IA.
- `src/frontend` y `src/backend`: README de cada módulo.

## Uso
- Lee `docs/guia_IA.md` antes de cualquier tarea (reglas para la IA).
- Lee `docs/arquitectura.md` y `docs/entorno.md` antes de cambiar código.
- Sigue `docs/convenciones.md` y `docs/procedimientos.md` en el día a día.
- Para tareas comunes, consulta los prompts en `.github/prompts/`.

## Encendido con Docker
Crear la red externa (una sola vez):
```bash
docker network create proxy_net
```

Dev:
```bash
cp .env.dev.example .env.dev
cp .env.db.example .env.db
cp .env.dev .env
docker compose up -d --build
```

Prod:
```bash
cp .env.prod.example .env.prod
cp .env.db.example .env.db
cp .env.prod .env
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

## Actualizacion en produccion (sin borrar base de datos)

Reglas de seguridad (BD):
- No usar `docker compose down -v`, `docker volume prune` ni `docker system prune --volumes`.
- Los datos viven en volumenes; mientras no se borren, la BD se mantiene.

Frontend (solo cambios web):
```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml --env-file .env.prod build frontend
docker compose -f docker-compose.yml -f docker-compose.prod.yml --env-file .env.prod up -d --no-deps frontend
docker image prune -f
docker builder prune -f
```

Backend (API):
```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml build backend
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --no-deps backend
docker image prune -f
docker builder prune -f
```

Migraciones (si aplica):
```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec backend php artisan migrate
```

### Comandos utiles
Ejecutar migraciones con seeders:
```bash
docker compose exec backend php artisan migrate --seed
```

Alternativa: recrear desde cero y cargar seeders:
```bash
docker compose exec backend php artisan migrate:fresh --seed
```
Aviso: `migrate:fresh` elimina toda la data de la base de datos antes de recrearla.

En producción (borra datos):
```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec backend php artisan migrate:fresh --seed
```

Rebuild limpio (borra contenedores y volúmenes, pierde la DB):
```bash
docker compose down -v --remove-orphans
docker compose build --no-cache
docker compose up -d
```

Rebuild limpio + borrar imágenes locales del proyecto:
```bash
docker compose down --rmi local -v --remove-orphans
docker compose build --no-cache
docker compose up -d
```

Rebuild limpio en producción (borra contenedores/volúmenes y usa compose prod):
```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml down --rmi local -v --remove-orphans
docker compose -f docker-compose.yml -f docker-compose.prod.yml build --no-cache
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

Nota: antes de usar prod, configurar el `.env` del root con URLs reales, credenciales de DB y mail. Hay plantillas en el root (`.env.prod.example`, `.env.dev.example`, `.env.db.example`). El `APP_KEY` se genera al arrancar el contenedor si no existe.

## Backups y restauracion

### Backup total en un solo archivo (dump)
```bash
STAMP=$(date +%Y%m%d_%H%M%S)
OUT=backups/db_full_${STAMP}.dump
docker compose exec -T -e PGPASSWORD="$DB_PASSWORD" db \
  pg_dump -U "$DB_USERNAME" -d "$DB_DATABASE" -Fc > "$OUT"
```

Restaurar dump completo (borra schema antes de restaurar):
```bash
docker compose exec -T db psql -U template -d template -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"
docker compose exec -T -e PGPASSWORD="template" db \
  pg_restore -U "template" -d "template" < backups/db_full_20260131_141121.dump
```

### Backups por modelo (scripts)
Generar backup por modelo (siempre incluye schema en archivos separados):
```bash
./scripts/db_backup_pg.sh --model full
```
Modelos disponibles:
- `full`: todos los modelos/tablas.
- `auth`: usuarios y tokens.
- `permissions`: roles/permisos (Spatie).
- `projects`: proyectos.

Restaurar por modelo (interactivo):
```bash
./scripts/db_restore_pg.sh
```

Restaurar por modelo (no interactivo):
```bash
./scripts/db_restore_pg.sh backups/db_pg_YYYYMMDD_HHMMSS data --model full
```

## Proxy inverso en produccion
1) Asegura que el proxy este en la red `proxy_net`.
2) Configura los dominios en el `.env` del root para que el backend genere URLs y cookies correctas:
```bash
GLOBAL_PROTOCOL=https
GLOBAL_BACKEND_DNS=api.example.com
GLOBAL_FRONTEND_DNS=app.example.com
GLOBAL_SESSION_DOMAIN=.example.com
```
3) En el proxy, enruta cada dominio al servicio correspondiente dentro de la red Docker.

Tabla para copiar/pegar (Nginx Proxy Manager u otro panel similar):
| Host | Domain Names | Scheme | Forward Hostname/IP | Forward Port | Access List | Cache Assets | Block Common Exploits | Websockets |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| API | api.example.com | http | web | 80 | Publicly Accessible | off | on | off |
| Frontend | app.example.com | http | frontend | 80 | Publicly Accessible | off | on | off |

Ejemplo Nginx (proxy externo):
```nginx
server {
  listen 80;
  server_name api.example.com;
  location / {
    proxy_pass http://web:80;
    proxy_set_header Host $host;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
  }
}

server {
  listen 80;
  server_name app.example.com;
  location / {
    proxy_pass http://frontend:80;
    proxy_set_header Host $host;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
  }
}
```

## Usuarios de prueba
Se crean al ejecutar los seeders (`AdminUserSeeder`). Todos usan la misma clave: `password`.

| Rol | Email | Password |
| --- | --- | --- |
| admin | admin@example.com | password |

TODO: añadir enlaces a documentación generada automáticamente si aplica.
