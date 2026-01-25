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
- Node.js & Yarn

## Development Setup

### Using Docker

```bash
# Clone the repository
git clone <repository-url>
cd episciences-manager

# Build and start containers
make build
make up

# Install dependencies
make composer-install
yarn install

# Build assets
yarn build

# Access the application at http://manager-ng-dev.episciences.org:8082/
# Make sure to add "127.0.0.1 localhost manager-ng-dev.episciences.org" to /etc/hosts
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
yarn install

# Build assets for development
yarn build
# OR using Make
make yarn-build

# Start development
symfony server:start
```

## Available Make Commands

### Docker Development Commands
- `make up` - Start all containers (in background)
- `make down` - Stop and remove containers, networks, orphan volumes
- `make restart` - Restart all containers
- `make logs` - Display logs in real time (usage: make logs [SERVICE=php])
- `make ps` - View containers status
- `make build` - Rebuild Docker images
- `make clean` - Clean up (project containers/volumes only)

### Composer/Yarn Commands
- `make composer-install` - Install Composer dependencies
- `make composer-update` - Update Composer dependencies
- `make yarn-build` - Compile assets for development
- `make yarn-encore-production` - Compile assets for production
- `make can-i-use-update` - Update Browserslist (host, when caniuse-lite is outdated)

### Database
- `make load-db-manager` - Load episciences database (episciences.sql from ~/tmp/)
- `make load-db-auth` - Load authentication database (cas_users.sql from ~/tmp/)

### Code Quality / Testing
- `make lint` - Check JS code with ESLint
- `make lint-fix` - Fix JS code with ESLint (usage: make lint-fix [FILE=src/file.js])
- `make format` - Format JS code with Prettier
- `make format-check` - Check JS formatting with Prettier
- `make lint-php` - Check PHP syntax in src/
- `make lint-php-file` - Check PHP syntax of a file (make lint-php-file FILE=src/file.php)
- `make check-all` - Run all checks (PHP, JS, tests)
- `make fix-all` - Fix all (lint JS + Prettier)

### Testing
- `make test` - Run JavaScript unit tests
- `make test-php` - Run PHP tests with Pest
- `make test-e2e` - Run E2E tests with Playwright

### Production Deployment
- `make deploy-prod` - Complete production deployment (migrations + cache + restart)
- `make deploy` - Deploy main branch to production
- `make deploy-branch BRANCH=feature` - Deploy specific branch to production
- `make deploy-tag TAG=v1.0.0` - Deploy specific tag to production
- `make composer-install-prod` - Install production dependencies (no-dev, optimized)
- `make yarn-encore-production` - Build production assets and optimize
- `make cache-clear` - Clear Symfony cache (prod, host)
- `make cache-warmup` - Warm up Symfony cache (prod, host)

### SSL & Preproduction
- `make ssl-certs` - Generate self-signed SSL certificates
- `make ssl-clean` - Remove SSL certificates
- `make preprod-setup` - Complete preprod setup (build assets + compile env + start containers + clear cache)
- `make preprod` - Start preprod containers with SSL
- `make preprod-no-ssl` - Start preprod containers (HTTP only)
- `make preprod-ci` - Start preprod with CI database and SSL
- `make preprod-ci-no-ssl` - Start preprod with CI database (HTTP only)
- `make cache-clear-preprod` - Clear Symfony cache (preprod, Docker container)
- `make dump-env-preprod` - Compile .env files for preprod environment (optimizes performance)
- `make dump-env-prod` - Compile .env files for production environment (optimizes performance)

### Container Management
- `make restart-httpd` - Restart Apache httpd (Docker command launched on host)
- `make restart-php` - Restart PHP-FPM (Docker command launched on host)
- `make enter-container-php` - Open shell in PHP container (debug)
- `make enter-container-httpd` - Open shell in HTTPD container (debug)


## Testing

```bash
# Using Make commands
make test-php        # PHP tests with Pest
make test           # JavaScript tests with Jest
make test-e2e       # E2E tests with Playwright
make check-all      # All quality checks + tests

# Direct commands
vendor/bin/pest     # PHP tests
yarn test           # JavaScript tests
yarn test:e2e       # E2E tests
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

## Preproduction Environment

### SSL Certificate Setup
```bash
# Generate SSL certificates for HTTPS development
make ssl-certs

# Clean up SSL certificates
make ssl-clean
```

### Starting Preproduction Environment
```bash
# Complete setup (recommended) - builds assets, compiles env, starts containers
make preprod-setup

# With SSL (HTTPS + HTTP)
make preprod

# HTTP only (no SSL)
make preprod-no-ssl

# CI environment with SSL
make preprod-ci

# CI environment HTTP only
make preprod-ci-no-ssl
```

### Environment Configuration
The project uses Docker environment files for configuration:
- Main environment variables are stored in `.env.local` (not committed to git)
- This includes database credentials and Docker Compose settings
- The Makefile automatically loads `.env.local` using `--env-file` for all Docker commands
- Environment files can be compiled for better performance using `make dump-env-preprod` or `make dump-env-prod`

**Required `.env.local` variables:**
```bash
DB_EPISCIENCES_USER=<database_user>
DB_EPISCIENCES_PASSWORD=<database_password>
DB_EPISCIENCES_HOST=<database_host>
DB_EPISCIENCES_PORT=<database_port>
DB_EPISCIENCES_DATABASE=<database_name>
COMPOSE_PROJECT_NAME=episciences-manager
```


### Access Configuration
Add to your `/etc/hosts` file:
```
127.0.0.1    manager-ng-dev.episciences.org
127.0.0.1    epimanager-preprod.episciences.org
```

Then access:
- Development: http://manager-ng-dev.episciences.org:8082
- Preproduction(HTTP): http://epimanager-preprod.episciences.org
- Preproduction(HTTPS): https://epimanager-preprod.episciences.org (when SSL is enabled)

## Production Deployment

### Git-based Deployment
```bash
# Deploy current branch
make deploy

# Deploy specific branch
make deploy-branch BRANCH=feature-name

# Deploy specific tag
make deploy-tag TAG=v1.0.0
```

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