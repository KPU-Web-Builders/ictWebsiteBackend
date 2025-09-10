# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application with JWT authentication capabilities, designed as a backend auth service. The project uses:
- PHP 8.2+
- Laravel 12.0 Framework  
- JWT Auth (php-open-source-saver/jwt-auth)
- Pest for testing
- Vite with TailwindCSS for frontend assets

## Development Commands

### PHP/Laravel Commands
- `composer dev` - Start development server with queue listener and Vite (recommended for development)
- `php artisan serve` - Start Laravel development server only
- `php artisan test` - Run all tests
- `php artisan config:clear` - Clear configuration cache
- `composer test` - Run tests with config clear

### Frontend Commands
- `npm run dev` - Start Vite development server
- `npm run build` - Build assets for production

### JWT Authentication
- `php artisan jwt:secret` - Generate JWT secret key (required for setup)

## Architecture

### Authentication System
The application implements JWT-based authentication using the `php-open-source-saver/jwt-auth` package. Key configuration is in `config/jwt.php` with the following important settings:
- JWT TTL: 60 minutes (configurable via JWT_TTL)
- Refresh TTL: 2 weeks (configurable via JWT_REFRESH_TTL)
- Blacklist enabled by default for token invalidation

### Directory Structure
- `app/Http/Controllers/` - API controllers (currently minimal)
- `app/Models/` - Eloquent models (includes User model)
- `config/jwt.php` - JWT authentication configuration
- `database/migrations/` - Database schema migrations
- `tests/` - Pest-based test suite (Feature and Unit tests)

### Database
Uses SQLite as default database (`database/database.sqlite`). Database configuration is in `config/database.php`.

### Frontend Assets
- Vite bundler with TailwindCSS
- Assets located in `resources/css/` and `resources/js/`
- Configuration in `vite.config.js`

## Testing
This project uses Pest for testing instead of PHPUnit. Tests are organized in:
- `tests/Feature/` - Feature tests
- `tests/Unit/` - Unit tests

Run tests with either `composer test` or `php artisan test`.