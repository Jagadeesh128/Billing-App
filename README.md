## Billing App (Back-end)

Project: a Symfony-based billing application that manages businesses, invoices, invoice items, and billing settings. It provides an EasyAdmin interface for administration, API endpoints (API Platform), JWT authentication, and PDF invoice generation.

- **Repository:** this workspace
- **License:** MIT

---

## Key Features

- Business and Billing Settings management
- Invoice creation, line items, and automatic totals calculation
- PDF invoice generation and print view
- Admin UI using EasyAdmin
- API Platform for REST endpoints
- JWT-based authentication and refresh tokens
- Doctrine ORM with migrations
- Docker-ready (compose.yaml) for local development

---

## Repository Layout

| Directory/File | Purpose |
|---|---|
| `src/` | Application source (Entities, Controllers, Subscribers, State, Repositories) |
| `public/` | Web root and asset entry points |
| `config/` | Symfony + bundle configuration files |
| `templates/` | Twig templates (admin, PDF, views) |
| `migrations/` | Doctrine migration files |
| `docker/` or root compose files | Docker configuration (`compose.yaml`, `compose.override.yaml`) |
| `apache/` | Apache vhost used by Docker image |

---

## Quick Start (Docker - recommended)

Prerequisites: Docker and Docker Compose.

1. Clone the repository and enter it:

```bash
git clone <your-repo-url> billing-app
cd billing-app
```

2. Build and start services:

```bash
docker compose up --build -d
```

3. Enter the app container (if needed) and run migrations:

```bash
docker compose exec app bash
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction # optional
```

4. Open the app in your browser (port may vary):

- Admin / EasyAdmin: http://localhost:8000/admin (or configured host)
- API docs: check `/api` or `/docs` depending on API Platform setup

5. Stop services:

```bash
docker compose down
```

---

## Local (non-Docker) setup

Prereqs: PHP 8.1/8.2+, Composer, MySQL/Postgres, Apache or built-in server.

1. Copy `.env` to `.env.local` and update `DATABASE_URL`.

2. Install dependencies and create the database:

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load # optional
```

3. Run the server locally:

```bash
symfony server:start
# or
php -S 127.0.0.1:8000 -t public
```

---

## Useful Commands

- Install deps: `composer install`
- Migrations: `php bin/console doctrine:migrations:migrate`
- Fixtures (optional): `php bin/console doctrine:fixtures:load`
- Clear cache: `php bin/console cache:clear`

---

## Troubleshooting

- DB host issues: ensure `DATABASE_URL` matches your DB host or Docker service name.
- 403/404 on admin: confirm `public/` is document root and `mod_rewrite` is enabled when using Apache.
- Invoice totals look wrong in UI: amounts are stored as full units; EasyAdmin `MoneyField` may require `setStoredAsCents(false)` (already set in the admin controller).

---

## Contributing

Contributions are welcome. Open issues or PRs on the repository. Follow the existing coding style and run tests (if available) before submitting changes.

---

## Authors

- Jagadeesh128 (original)

