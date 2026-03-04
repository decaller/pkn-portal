# PKN Portal

PKN Portal is a multi-panel event platform built with Laravel 12 + Filament 5.

It supports three main audiences:

- Public visitors: browse published events and news
- Members/users: register accounts, manage event registrations, and access member features
- Admins: manage events, registrations, organizations, users, content, and analytics

## Tech Stack

- PHP 8.2
- Laravel 12
- Filament 5
- PostgreSQL
- Redis
- Tailwind CSS / Vite
- Laravel Sail (local Docker development)

## Panels and URLs

Default panel paths:

- Public panel: `/public`
- User panel: `/user`
- Admin panel: `/admin`

Home page is served at `/` and links into the panels.

## Core Features

- Event management with date, location, capacity, packages, and rundown
- Public event/news listing
- User registration/login using phone number
- Organization-based membership (create new or join existing)
- Event registration flow with participants and package breakdown
- Invoice and payment proof handling
- Surveys and testimonials
- Analytics and document indexing integrations
- Multi-language support (English / Indonesian)

## Local Development (Sail)

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Create environment file

```bash
cp .env.example .env
```

### 3. Start Sail services

```bash
./vendor/bin/sail up -d
```

### 4. Generate app key and migrate

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

### 5. Build frontend assets

```bash
./vendor/bin/sail npm run dev
```

## Useful Commands

- Run tests:
```bash
./vendor/bin/sail artisan test
```

- Format code:
```bash
./vendor/bin/sail php ./vendor/bin/pint
```

- Queue worker (manual):
```bash
./vendor/bin/sail artisan queue:work
```

## Deployment (Docker, Production)

This repository includes a production Docker setup aligned with Filament deployment guidance.

### Files

- `docker-compose.prod.yml`
- `docker/production/Dockerfile`
- `docker/production/nginx/default.conf`
- `docker/production/entrypoint.sh`
- `.env.production.example`

### 1. Prepare environment

```bash
cp .env.production.example .env.production
```

Set real values in `.env.production` (`APP_URL`, database credentials, SMTP, etc.).

Generate an app key:

```bash
docker compose -f docker-compose.prod.yml run --rm app php artisan key:generate --show
```

Copy the printed key into `APP_KEY=` in `.env.production`.

### 2. Build and start

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

### 3. Run migrations

```bash
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

### 4. Verify container health/logs

```bash
docker compose -f docker-compose.prod.yml ps
docker compose -f docker-compose.prod.yml logs -f app nginx worker scheduler
```

### Notes

- `entrypoint.sh` runs:
  - `php artisan optimize`
  - `php artisan filament:optimize`
- Queue worker and scheduler run in dedicated containers.
- Nginx serves `public/` and forwards PHP to the `app` container.

## License

This project is open-sourced software licensed under the MIT license.
