# Entorno

- Requisitos: Docker, docker compose, Node.js (para desarrollo local), PHP 8.3+ si se ejecuta backend sin contenedor.
- Servicios (docker-compose):
  - db (PostgreSQL 15, puerto host 5434).
  - backend (php-fpm) + web (nginx expone 8000).
  - frontend (Vite en 5173).
  - mailpit (SMTP 1025, UI 8025).
- Archivos de entorno (root):
  - `.env.dev`: variables de desarrollo (backend + frontend).
  - `.env.prod`: variables de produccion.
  - `.env.db`: credenciales de PostgreSQL.
- Variables clave backend (`backend/.env`):
  - `APP_URL=http://localhost:8000`
  - `FRONTEND_URL=http://localhost:5173`
  - `SESSION_DOMAIN=localhost`
  - `SANCTUM_STATEFUL_DOMAINS=localhost:5173`
  - Mailpit: `MAIL_MAILER=smtp`, `MAIL_HOST=mailpit`, `MAIL_PORT=1025`
- Alias Vite: `@` (src), `@core`, `@stores`.

TODO: Añadir comandos de instalacion dependencias locales y versiones exactas.
