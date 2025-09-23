# Episciences Manager

An overlay journal management platform built with Symfony

![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue) ![Symfony](https://img.shields.io/badge/Symfony-7.2-green) ![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-yellow) ![License](https://img.shields.io/badge/License-Proprietary-red)

![CI Status](https://github.com/CCSDForge/episciences-manager/workflows/CI%20Pipeline/badge.svg) ![PHP Tests](https://img.shields.io/github/actions/workflow/status/CCSDForge/episciences-manager/ci.yml?label=PHP%20Tests&logo=php) ![JS Tests](https://img.shields.io/github/actions/workflow/status/CCSDForge/episciences-manager/ci.yml?label=JS%20Tests&logo=javascript) ![E2E Tests](https://img.shields.io/github/actions/workflow/status/CCSDForge/episciences-manager/ci.yml?label=E2E%20Tests&logo=playwright)

This repository hosts the management software for Episciences overlay journals platform. It's built with Symfony 7.2 and provides a modern web interface for journal administration and review management.

## Features

- **Review Management**: Complete system for managing peer reviews
- **User Management**: Authentication with CAS integration
- **Journal Administration**: Tools for managing overlay journals
- **Markdown Support**: Built-in markdown processing for content
- **Modern Frontend**: Bootstrap 5 + Stimulus controllers
- **Multi-language**: French and English translations

More information about Episciences: https://www.episciences.org/

## Tech Stack

- **Backend**: PHP 8.2+ with Symfony 7.2
- **Database**: MySQL with Doctrine ORM
- **Frontend**: Bootstrap 5, Stimulus, Webpack Encore
- **Testing**: Pest (PHP) + Jest (JavaScript) + Playwright (E2E)
- **Authentication**: CAS (Central Authentication Service)
- **Containerization**: Docker & Docker Compose

## Prerequisites

- Docker & Docker Compose
- Make
- Node.js & npm

## Development Setup

### Using Docker (Recommended)

```bash
# Clone the repository
git clone <repository-url>
cd episciences-manager

# Build and start containers
make build
make up

# Install dependencies
make composer-install

# Build production assets
make yarn-encore-production

# Access the application at http://epimanager.episciences.org/
# Make sure to add "127.0.0.1 localhost epimanager.episciences.org" to /etc/hosts
```

### Database Setup

```bash
# Load database dumps (place SQL files in ~/tmp/)
make load-db-manager     # Load episciences.sql
make load-db-auth        # Load cas_users.sql
```

### Local Development

```bash
# Clone the repository
git clone <repository-url>
cd episciences-manager

# Install dependencies
composer install
npm install

# Build assets for development
npm run build
# OR using Make
make npm-build

# Start development
symfony server:start
```

## Available Make Commands

### Container Management
- `make build` - Build Docker containers
- `make up` - Start all containers
- `make down` - Stop containers
- `make clean` - Clean up Docker resources

### Dependencies
- `make composer-install` - Install PHP dependencies
- `make composer-update` - Update PHP dependencies

### Asset Building
- `make npm-build` - Build assets for development (fast build)
- `make yarn-encore-production` - Build production assets (optimized, minified)

### Database
- `make load-db-manager` - Load episciences database
- `make load-db-auth` - Load authentication database

### Code Quality
- `make lint` - Check JavaScript with ESLint
- `make lint-fix` - Fix JavaScript issues automatically
- `make format` - Format JavaScript with Prettier
- `make format-check` - Check JavaScript formatting
- `make lint-php` - Check PHP syntax
- `make check-all` - Run all quality checks
- `make fix-all` - Fix all auto-fixable issues

### Testing
- `make test` - Run JavaScript unit tests
- `make test-php` - Run PHP tests with Pest
- `make test-e2e` - Run E2E tests with Playwright

### Production Deployment
- `make deploy-prod` - Complete production deployment (recommended)
- `make composer-install-prod` - Install production dependencies (no-dev, optimized)
- `make yarn-encore-production` - Build production assets and optimize
- `make cache-clear` - Clear Symfony cache
- `make cache-warmup` - Warm up Symfony cache

### Utilities
- `make restart-httpd` - Restart Apache
- `make restart-php` - Restart PHP-FPM
- `make enter-container-php` - Open shell in PHP container
- `make enter-container-httpd` - Open shell in Apache container
- `make can-i-use-update` - Update browserslist database

## Testing

```bash
# Using Make commands
make test-php        # PHP tests with Pest
make test           # JavaScript tests with Jest
make test-e2e       # E2E tests with Playwright
make check-all      # All quality checks + tests

# Direct commands
vendor/bin/pest     # PHP tests
npm test           # JavaScript tests
npm run test:e2e   # E2E tests
```

## Code Quality

```bash
# Check and fix all issues
make fix-all

# Individual checks
make lint           # ESLint
make format-check   # Prettier
make lint-php       # PHP syntax

# Individual fixes
make lint-fix       # Fix ESLint issues
make format         # Apply Prettier formatting
```

## Production Deployment

### Quick Deployment
```bash
# All-in-one command
make deploy-prod
```

### Manual Step-by-Step (if needed)
```bash
# Follow this exact order:
make composer-install-prod  # 1. Install optimized dependencies
make yarn-encore-production # 2. Build optimized assets
make cache-clear           # 3. Clear old cache
make cache-warmup          # 4. Pre-generate cache
make restart-httpd         # 5. Restart Apache
make restart-php           # 6. Restart PHP-FPM
```

**Important:** Always follow this order! Dependencies must be installed before building assets, cache must be cleared before warming up, and services should be restarted last.

## Acknowledgments

Episciences has received funding from:

- CNRS
- European Commission grant 101017452 "OpenAIRE Nexus - OpenAIRE-Nexus Scholarly Communication Services for EOSC users"

The software is developed by the Center for the Direct Scientific Communication (CCSD).

## License

This project is proprietary software. See [LICENSE](LICENSE) for details.