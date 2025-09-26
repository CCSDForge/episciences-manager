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

# Build production assets
make yarn-encore-production

# Access the application at http://epimanager-dev.episciences.org/
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
yarn install

# Build assets for development
yarn build
# OR using Make
make yarn-build

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
- `make yarn-build` - Build assets for development (fast build)
- `make yarn-encore-production` - Build production assets (optimized, minified)

### Database
- `make load-db-manager` - Load episciences database (episciences.sql from ~/tmp/)
- `make load-db-auth` - Load authentication database (cas_users.sql from ~/tmp/)

### Code Quality
- `make lint` - Check JavaScript with ESLint
- `make lint-fix` - Fix JavaScript issues automatically
- `make format` - Format JavaScript with Prettier
- `make format-check` - Check JavaScript formatting
- `make lint-php` - Check PHP syntax in src/ directory
- `make lint-php-file FILE=path` - Check specific PHP file syntax
- `make check-all` - Run all quality checks (PHP, JS, tests)
- `make fix-all` - Fix all auto-fixable issues (ESLint + Prettier)

### Testing
- `make test` - Run JavaScript unit tests
- `make test-php` - Run PHP tests with Pest
- `make test-e2e` - Run E2E tests with Playwright

### Production Deployment
- `make deploy-prod` - Complete production deployment
- `make deploy` - Deploy current branch to production
- `make deploy-branch BRANCH=feature` - Deploy specific branch to production
- `make deploy-tag TAG=v1.0.0` - Deploy specific tag to production
- `make composer-install-prod` - Install production dependencies (no-dev, optimized)
- `make yarn-encore-production` - Build production assets and optimize
- `make cache-clear` - Clear Symfony cache
- `make cache-warmup` - Warm up Symfony cache

### SSL & Preproduction
- `make ssl-certs` - Generate self-signed SSL certificates
- `make ssl-clean` - Remove SSL certificates
- `make preprod-setup` - Complete preprod setup (build assets + compile env + start containers)
- `make preprod` - Start preprod containers with SSL
- `make preprod-no-ssl` - Start preprod containers (HTTP only)
- `make preprod-ci` - Start preprod with CI database and SSL
- `make preprod-ci-no-ssl` - Start preprod with CI database (HTTP only)
- `make dump-env-preprod` - Compile .env files for preprod environment (optimizes performance)
- `make dump-env-prod` - Compile .env files for production environment (optimizes performance)

### Container Management
- `make restart-httpd` - Restart Apache
- `make restart-php` - Restart PHP-FPM
- `make enter-container-php` - Open shell in PHP container
- `make enter-container-httpd` - Open shell in Apache container

### Utilities
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
127.0.0.1    epimanager-dev.episciences.org
127.0.0.1    epimanager-preprod.episciences.org
```

Then access:
- Development: http://epimanager-dev.episciences.org
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