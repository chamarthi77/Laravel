# Laravel Backend Starter (SQLite for Dev, MySQL for Prod) + Firebase Auth Linkage

A minimal Laravel 12.x backend that:
- Runs locally on **SQLite** for fast setup; uses **MySQL** in production.
- Verifies users via **Firebase Authentication** and links them to app authZ.
- Provides **Projects**, **Permissions**, **Users** tables and **super‑admin CRUD** APIs.
- Exposes a demo health endpoint: `GET /api/alive`.

---

## Stack & Features
- **PHP** 8.2+
- **Laravel** 12.x
- **SQLite** (local), **MySQL** (production)
- **Firebase ID token verification** via `kreait/firebase-tokens` (v5.x)
- **Admin-only CRUD** for Projects, Permissions, Users
- **CORS** ready for separate SPA/frontend

---

## Prerequisites
- PHP 8.2+ with extensions: `pdo`, `pdo_sqlite`, `pdo_mysql`, `openssl`, `mbstring`, `xml`, `ctype`, `json`, `tokenizer`
- Composer
- Node (optional, only if you later add a SPA build in the same repo)
- SQLite (preinstalled on macOS) or MySQL server (for prod)
- A Firebase project (for production auth; optional for local dev)

> Local development can bypass Firebase using a dev token: `Authorization: Bearer local-dev-super`

---

## Quick Start (Local Dev, SQLite)

```bash
# 1) Clone and enter
git clone git@github.com:SpotOnResponse-Developers/spoton-backend.git && cd spoton-backend

# 2) Install PHP deps
composer install

# 3) Create .env (copy sample if present or create fresh)
cp .env.example .env

# 4) Configure .env for SQLite
php -r "file_exists('database') || mkdir('database');"
touch database/database.sqlite

php artisan key:generate

# Edit .env and set:
# APP_ENV=local
# APP_DEBUG=true
# DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/this/repo/database/database.sqlite
# FIREBASE_PROJECT_ID=<your-firebase-project-id or leave empty for local>

# 5) Migrate & seed (permissions + demo project + demo super user)
php artisan migrate --seed

# 6) Serve
php artisan serve
# App: http://127.0.0.1:8000

# 7) Health check
curl -i http://127.0.0.1:8000/api/alive
```

**Authentication (local dev):** use the special dev token `local-dev-super` to exercise admin endpoints:

```
Authorization: Bearer local-dev-super
```

---

## Production (MySQL + Firebase)

1. Set environment variables (for Docker/VM/host) analogous to `.env`:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com

   DB_CONNECTION=mysql
   DB_HOST=<mysql-host>
   DB_PORT=3306
   DB_DATABASE=<db-name>
   DB_USERNAME=<db-user>
   DB_PASSWORD=<db-pass>

   FIREBASE_PROJECT_ID=<your-firebase-project-id>
   ```

2. Run:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate --force
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

3. Point your web server to `public/`. Example Nginx fastcgi block is standard (not repeated here for brevity).

> In production, clients must send **real Firebase ID tokens** in `Authorization: Bearer <ID_TOKEN>`; the backend will create/link users on first verified request.

---

## Routes Overview

- **Health:** `GET /api/alive` (no auth)
- **Admin (requires `Authorization: Bearer <token>` and user with `is_super = true`):**
  - `GET    /api/admin/projects`
  - `POST   /api/admin/projects`
  - `GET    /api/admin/projects/{project}`
  - `PUT    /api/admin/projects/{project}`
  - `DELETE /api/admin/projects/{project}`
  - `GET    /api/admin/permissions`
  - `POST   /api/admin/permissions`
  - `GET    /api/admin/permissions/{permission}`
  - `PUT    /api/admin/permissions/{permission}`
  - `DELETE /api/admin/permissions/{permission}`
  - `GET    /api/admin/users`
  - `POST   /api/admin/users`
  - `GET    /api/admin/users/{user}`
  - `PUT    /api/admin/users/{user}`
  - `DELETE /api/admin/users/{user}`

---

## Curl Examples

### Health
```bash
curl -i http://127.0.0.1:8000/api/alive
```

### Permissions (list/create)
```bash
curl -i -H "Authorization: Bearer local-dev-super" \
  http://127.0.0.1:8000/api/admin/permissions

curl -i -X POST -H "Content-Type: application/json" \
  -H "Authorization: Bearer local-dev-super" \
  -d '{"name":"operator","description":"Ops role"}' \
  http://127.0.0.1:8000/api/admin/permissions
```

### Projects (create/list/show/update/delete)
```bash
curl -i -X POST -H "Content-Type: application/json" \
  -H "Authorization: Bearer local-dev-super" \
  -d '{"name":"Demo Project","code":"DEMO","description":"Local test"}' \
  http://127.0.0.1:8000/api/admin/projects

curl -i -H "Authorization: Bearer local-dev-super" \
  http://127.0.0.1:8000/api/admin/projects

curl -i -H "Authorization: Bearer local-dev-super" \
  http://127.0.0.1:8000/api/admin/projects/1

curl -i -X PUT -H "Content-Type: application/json" \
  -H "Authorization: Bearer local-dev-super" \
  -d '{"description":"Updated desc"}' \
  http://127.0.0.1:8000/api/admin/projects/1

curl -i -X DELETE -H "Authorization: Bearer local-dev-super" \
  http://127.0.0.1:8000/api/admin/projects/1
```

### Users (create/list/show/update/delete)
```bash
curl -i -X POST -H "Content-Type: application/json" \
  -H "Authorization: Bearer local-dev-super" \
  -d '{"firebase_uid":"test-uid-123","email":"test@example.com","display_name":"Test User","project_id":1,"permission_id":1,"is_super":false}' \
  http://127.0.0.1:8000/api/admin/users

curl -i -H "Authorization: Bearer local-dev-super" \
  http://127.0.0.1:8000/api/admin/users

curl -i -H "Authorization: Bearer local-dev-super" \
  http://127.0.0.1:8000/api/admin/users/1

curl -i -X PUT -H "Content-Type: application/json" \
  -H "Authorization: Bearer local-dev-super" \
  -d '{"permission_id":2}' \
  http://127.0.0.1:8000/api/admin/users/1

curl -i -X DELETE -H "Authorization: Bearer local-dev-super" \
  http://127.0.0.1:8000/api/admin/users/1
```

---

## Environment Variables

Common keys:
```env
APP_NAME=Laravel
APP_ENV=local            # local|production
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Choose one DB configuration:
# SQLite (local)
DB_CONNECTION=sqlite
DB_DATABASE=/abs/path/to/repo/database/database.sqlite

# MySQL (production)
# DB_CONNECTION=mysql
# DB_HOST=your-mysql-host
# DB_PORT=3306
# DB_DATABASE=your-db-name
# DB_USERNAME=your-db-user
# DB_PASSWORD=your-db-pass

# Sessions (DB driver requires sessions table; this project includes a migration)
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Firebase
FIREBASE_PROJECT_ID=your-firebase-project-id
```

---

## Firebase Notes

- **Local dev:** you may use `Authorization: Bearer local-dev-super` to simulate a super admin.
- **Production:** obtain a Firebase ID token from your frontend (Google/GitHub/etc. sign-in). Send it as:
  ```http
  Authorization: Bearer <FIREBASE_ID_TOKEN>
  ```
  The backend verifies the token and creates/links the user on first request.

---

## Troubleshooting

- **“no such table: sessions”**  
  Ensure migrations have run: `php artisan migrate`. This repo includes a sessions migration (`0001_01_01_000003_create_sessions_table.php`).

- **Routes not showing `/api/*`**  
  Confirm `bootstrap/app.php` registers API routes:
  ```php
  ->withRouting(
      web: __DIR__.'/../routes/web.php',
      api: __DIR__.'/../routes/api.php',
      commands: __DIR__.'/../routes/console.php',
      health: '/up',
  )
  ```
  Then `php artisan route:clear && php artisan route:list`.

- **401 Unauthorized on admin endpoints**  
  Provide `Authorization` header. Locally: `Bearer local-dev-super`. In prod: a real Firebase ID token.

- **403 Forbidden**  
  Your authenticated user is not a super admin. Set `is_super=true` for the user in DB (via migration/seed or admin update).

- **Composer conflict with PHP 8.2 (beste/clock)**  
  Use `kreait/firebase-tokens:^5.2` or later.

---

## Development Tips
- Re-run everything cleanly:
  ```bash
  php artisan migrate:fresh --seed
  php artisan serve
  ```
- Inspect routes:
  ```bash
  php artisan route:list
  ```

---

## License
MIT (or your preferred license).
