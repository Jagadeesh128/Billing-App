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

## Quick Start (Local development)

This repository no longer includes Docker automation. Follow the steps below to run the project locally.

Prereqs: PHP 8.1 or 8.2, Composer, a running MySQL or PostgreSQL instance, and optionally Apache.

1. Clone the repository and enter it:

```bash
git clone <your-repo-url> billing-app
cd billing-app
```

2. Copy environment file and update database connection in `.env.local`:

```bash
cp .env .env.local
# Edit DATABASE_URL in .env.local, for example:
# DATABASE_URL="mysql://db_user:db_pass@127.0.0.1:3306/billing_db"
```

3. Install PHP dependencies:

```bash
composer install
```

4. Create the database and run migrations:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. (Optional) Load fixtures/sample data:

```bash
php bin/console doctrine:fixtures:load
```

6. Run the built-in Symfony server or use your Apache virtual host:

```bash
symfony server:start
# or
php -S 127.0.0.1:8000 -t public
```

7. Open the app in your browser:

- Admin / EasyAdmin: http://localhost:8000/admin
- API docs: http://localhost:8000/docs (if API Platform is enabled)

---
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

- Jagadeesh

