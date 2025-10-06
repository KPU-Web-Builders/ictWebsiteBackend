# Repository Guidelines

## Project Structure & Module Organization
- `app/` — Domain code: `Http/Controllers`, `Models`, `Middleware` (e.g., `DevCors.php`).
- `routes/api.php` — API endpoints; keep RESTful and versionable if needed.
- `config/` — App, auth, CORS, database config.
- `database/` — `migrations/`, `seeders/`, `factories/` for testable data.
- `tests/` — Pest tests: `Feature/` (HTTP, flows), `Unit/` (pure logic).
- `resources/` & `public/` — Frontend assets (Vite/Tailwind), static files.
- `bootstrap/`, `vendor/` — Framework bootstrap and Composer deps.

## Setup, Build, and Development Commands
- Install & configure:
  - `composer install`
  - Windows: `copy .env.example .env` (macOS/Linux: `cp .env.example .env`)
  - `php artisan key:generate`
  - (JWT) `php artisan jwt:secret`
  - Set DB in `.env`, then `php artisan migrate --seed` (if seeds exist)
- Run locally:
  - All-in-one: `composer run dev` (serves app, queue, and Vite)
  - Or separate: `php artisan serve` and `npm run dev`
- Assets: `npm install`, `npm run dev`, `npm run build`
- Tests: `composer test` or `php artisan test`

## Coding Style & Naming Conventions
- PHP: PSR-12, 4-space indent; run formatter: `vendor/bin/pint`.
- Controllers: `PascalCase` with `Controller` suffix (e.g., `AuthController`).
- Models: singular `PascalCase`; tables plural `snake_case`.
- Routes: kebab-case URIs; use resource routes where possible.
- Prefer dependency injection; validate requests consistently.

## Testing Guidelines
- Framework: Pest. Place tests under `tests/Feature` and `tests/Unit`.
- Name tests descriptively (e.g., `UserLoginTest.php`).
- Useful flags: `php artisan test --filter=Name`, `--parallel`, `--coverage` (with Xdebug).
- Use factories/seeders for reproducible data.

## Commit & Pull Request Guidelines
- Commits: concise, present tense. Conventional Commits preferred (e.g., `feat: add JWT login`, `fix: handle CORS preflight`).
- Before pushing: run `vendor/bin/pint` and `composer test`.
- PRs: include purpose, linked issues, API changes (routes, payloads), and reproduction steps. Add example `curl` for new endpoints when applicable.

## Security & Configuration Tips
- Never commit `.env`, keys, or secrets. Rotate on leaks.
- Configure CORS in `config/cors.php` for frontend origins.
- Ensure `APP_KEY` and JWT secret are set in production.
