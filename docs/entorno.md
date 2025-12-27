# Entorno

- Requisitos: Docker, docker compose, Node.js (para desarrollo local), PHP 8.3+ si se ejecuta backend sin contenedor.
- Servicios (docker-compose):
  - db (MySQL 8, puerto host 3307).
  - backend (php-fpm) + web (nginx expone 8000).
  - frontend (Vite en 5173).
  - mailpit (SMTP 1025, UI 8025).
- Variables clave backend (`.env`):
  - `APP_URL=http://localhost:8000`
  - `FRONTEND_URL=http://localhost:5173`
  - `SESSION_DOMAIN=localhost`
  - `SANCTUM_STATEFUL_DOMAINS=localhost:5173`
  - Mailpit: `MAIL_MAILER=smtp`, `MAIL_HOST=mailpit`, `MAIL_PORT=1025`
- Alias Vite: `@` (src), `@core`, `@stores`.

TODO: Añadir comandos de instalación dependencias locales y versiones exactas.
