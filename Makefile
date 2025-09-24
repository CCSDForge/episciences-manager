# ==========================
#       CONFIG & COLORS
# ==========================
SHELL := /bin/bash
.SHELLFLAGS := -o pipefail -c
.DEFAULT_GOAL := help

# Colors for output (forced enabled)
RED=\033[0;31m
GREEN=\033[0;32m
YELLOW=\033[0;33m
BLUE=\033[0;34m
PURPLE=\033[0;35m
CYAN=\033[0;36m
BOLD=\033[1m
NC=\033[0m

DOCKER         ?= docker
DOCKER_COMPOSE ?= docker compose
NPX            ?= npx
CNTR_NAME_HTTPD?= httpd-manager
CNTR_NAME_PHP  ?= php-fpm-manager
CNTR_APP_DIR   ?= /var/www/htdocs

# MySQL connections (local mysql client)
MYSQL_CONNECT_EPISCIENCES ?= mysql -u root -proot -h 127.0.0.1 -P 33060 episciences
MYSQL_CONNECT_AUTH ?= mysql -u root -proot -h 127.0.0.1 -P 33062 cas_users

# Checks
define require_file
	@if [ ! -f "$(1)" ]; then \
		echo "$(RED)✗ Missing file: $(1)$(NC)"; exit 1; \
	fi
endef

define require_cmd
	@if ! command -v $(1) >/dev/null 2>&1; then \
		echo "$(RED)✗ Missing command: $(1)$(NC)"; exit 1; \
	fi
endef

# ==========================
#           HELP
# ==========================
.PHONY: help
help: ## Display available commands list
	@printf "\n"
	@printf "$(BOLD)Episciences Manager - Development Commands$(NC)\n"
	@printf "==========================================\n"
	@printf "\n"
	@printf "$(GREEN)Docker Development Commands:$(NC)\n"
	@grep -E '^(up|down|restart|logs|ps|build|clean):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@printf "\n"
	@printf "$(BLUE)Database Commands:$(NC)\n"
	@grep -E '^(load-db.*):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@printf "\n"
	@printf "$(YELLOW)Code Quality / Testing Commands:$(NC)\n"
	@grep -E '^(lint|lint-fix|lint-php|lint-php-file|check-all|fix-all|test|test-php|test-e2e|format|format-check):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@printf "\n"
	@printf "$(PURPLE)Container Management:$(NC)\n"
	@grep -E '^(restart-httpd|restart-php|enter-container.*):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@printf "\n"
	@printf "$(CYAN)SSL / Preproduction Commands:$(NC)\n"
	@grep -E '^(ssl-certs|ssl-clean|preprod.*):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@printf "\n"
	@printf "$(RED)Deployment Commands:$(NC)\n"
	@grep -E '^(deploy.*|composer-install-prod|cache-clear|cache-warmup|yarn-encore-production):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@printf "\n"
	@printf "$(BLUE)Composer/NPM Commands:$(NC)\n"
	@grep -E '^(composer-install|composer-update|npm-build|yarn-encore-production|can-i-use-update):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@printf "\n"
	@printf "$(YELLOW)Quick Start:$(NC)\n"
	@printf "  1. Run $(BOLD)make up$(NC) to start all containers\n"
	@printf "  2. Add to /etc/hosts: $(BOLD)127.0.0.1 localhost epimanager.episciences.org$(NC)\n"
	@printf "  3. Access: $(GREEN)http://epimanager.episciences.org/$(NC)\n"
	@printf "\n"

# ==========================
#     DOCKER (Local dev)
# ==========================
.PHONY: up down restart logs ps build clean
up: ## Start all containers (in background)
	$(DOCKER_COMPOSE) up -d
	@echo "====================================================================="
	@echo "Make sure you have this line in /etc/hosts:"
	@echo "127.0.0.1 localhost epimanager.episciences.org"
	@echo "Episciences Manager : http://epimanager.episciences.org/"
	@echo "PhpMyAdmin          : http://localhost:8001/"
	@echo "====================================================================="
	@echo "SQL: place your dumps in ~/tmp/"
	@echo "SQL: import '~/tmp/episciences.sql' with 'make load-db-manager'"

down: ## Stop and remove containers, networks, orphan volumes
	$(DOCKER_COMPOSE) down --remove-orphans

restart: ## Restart all containers
	$(DOCKER_COMPOSE) restart

logs: ## Display logs in real time (usage: make logs [SERVICE=php])
	@if [ -n "$(SERVICE)" ]; then \
		$(DOCKER_COMPOSE) logs -f $(SERVICE); \
	else \
		$(DOCKER_COMPOSE) logs -f; \
	fi

ps: ## View containers status
	$(DOCKER_COMPOSE) ps

build: ## Rebuild Docker images
	$(DOCKER_COMPOSE) build

clean: ## Clean up (project containers/volumes only)
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

# ==========================
#        DATABASE
# ==========================
.PHONY: load-db-manager load-db-auth
load-db-manager: ## Load SQL dump from ~/tmp/episciences.sql (local mysql client)
	$(MYSQL_CONNECT_EPISCIENCES) < ~/tmp/episciences.sql

load-db-auth: ## Load SQL dump from ~/tmp/cas_users.sql (local mysql client)
	$(MYSQL_CONNECT_AUTH) < ~/tmp/cas_users.sql

# ==========================
#        COMPOSER/NPM (HOST)
# ==========================
.PHONY: composer-install composer-update npm-build yarn-encore-production can-i-use-update
composer-install: ## Install Composer dependencies
	composer install --no-interaction --prefer-dist --optimize-autoloader

composer-update: ## Update Composer dependencies
	composer update --no-interaction --prefer-dist --optimize-autoloader

npm-build: ## Compile assets for development
	npm run build

yarn-encore-production: ## Compile assets for production
	yarn install --frozen-lockfile
	yarn encore production

can-i-use-update: ## Update Browserslist (host, when caniuse-lite is outdated)
	$(NPX) update-browserslist-db@latest

# ==========================
#   CODE QUALITY / TESTS (HOST)
# ==========================
.PHONY: test test-e2e test-php lint lint-fix format format-check lint-php lint-php-file check-all fix-all

test: ## Run JavaScript unit tests
	npm test

test-php: ## Run PHP tests (Pest)
	./vendor/bin/pest

test-e2e: ## Run E2E tests (Playwright)
	npm run test:e2e

lint: ## Check JS code with ESLint
	npm run lint

lint-fix: ## Fix JS code with ESLint (usage: make lint-fix [FILE=src/file.js])
	@if [ -n "$(FILE)" ]; then \
		echo "🔧 Fixing file: $(FILE)"; \
		$(NPX) eslint "$(FILE)" --fix; \
	else \
		echo "🔧 Fixing all JavaScript files"; \
		npm run lint:fix; \
	fi

format: ## Format JS code with Prettier
	npm run format

format-check: ## Check JS formatting with Prettier
	npm run format:check

lint-php: ## Check PHP syntax in src/
	@echo "🔍 Checking PHP syntax..."
	@find src/ -name "*.php" -print0 | xargs -0 -n1 php -l | grep -v "No syntax errors detected" || true
	@echo "✅ PHP syntax check completed"

lint-php-file: ## Check PHP syntax of a file (make lint-php-file FILE=src/file.php)
	@if [ -n "$(FILE)" ]; then \
		echo "🔍 Checking PHP syntax for: $(FILE)"; \
		php -l "$(FILE)"; \
	else \
		echo "❌ Usage: make lint-php-file FILE=path/to/file.php"; \
		exit 2; \
	fi

check-all: ## Run all checks (PHP, JS, tests)
	@echo "🚀 Running all quality checks..."
	@echo ""; echo "📋 1/4 - Checking PHP syntax..."; $(MAKE) lint-php
	@echo ""; echo "📋 2/4 - Checking JavaScript with ESLint..."; $(MAKE) lint
	@echo ""; echo "📋 3/4 - Checking JavaScript formatting..."; $(MAKE) format-check
	@echo ""; echo "📋 4/4 - Running JavaScript tests..."; $(MAKE) test
	@echo ""; echo "✅ All checks completed successfully!"

fix-all: ## Fix all (lint JS + Prettier)
	@echo "🔧 Fixing all auto-fixable issues..."
	@echo ""; echo "📋 1/2 - Fixing JavaScript with ESLint..."; $(MAKE) lint-fix
	@echo ""; echo "📋 2/2 - Fixing JavaScript formatting..."; $(MAKE) format
	@echo ""; echo "✅ All fixes applied!"

# ==========================
#        CONTAINERS
# ==========================
.PHONY: restart-httpd restart-php enter-container-php enter-container-httpd
restart-httpd: ## Restart Apache httpd (Docker command launched on host)
	$(DOCKER_COMPOSE) restart $(CNTR_NAME_HTTPD)

restart-php: ## Restart PHP-FPM (Docker command launched on host)
	$(DOCKER_COMPOSE) restart $(CNTR_NAME_PHP)

enter-container-php: ## Open shell in PHP container (debug)
	$(DOCKER) exec -it $(CNTR_NAME_PHP) sh -lc "cd /var/www/htdocs && exec bash"

enter-container-httpd: ## Open shell in HTTPD container (debug)
	$(DOCKER) exec -it $(CNTR_NAME_HTTPD) sh -lc "cd /var/www/htdocs && exec bash"

# ==========================
#         SSL / PREPROD
# ==========================
.PHONY: ssl-certs ssl-clean preprod preprod-no-ssl preprod-ci preprod-ci-no-ssl
ssl-certs: ## Generate self-signed SSL certificates
	@echo "$(BOLD)Generating SSL certificates for development...$(NC)"
	$(call require_cmd,openssl)
	@mkdir -p docker/apache/ssl
	@echo "[req]" > docker/apache/ssl/openssl.conf
	@echo "default_bits = 2048" >> docker/apache/ssl/openssl.conf
	@echo "prompt = no" >> docker/apache/ssl/openssl.conf
	@echo "distinguished_name = req_distinguished_name" >> docker/apache/ssl/openssl.conf
	@echo "req_extensions = v3_req" >> docker/apache/ssl/openssl.conf
	@echo "" >> docker/apache/ssl/openssl.conf
	@echo "[req_distinguished_name]" >> docker/apache/ssl/openssl.conf
	@echo "C = FR" >> docker/apache/ssl/openssl.conf
	@echo "ST = France" >> docker/apache/ssl/openssl.conf
	@echo "L = Lyon" >> docker/apache/ssl/openssl.conf
	@echo "O = Episciences" >> docker/apache/ssl/openssl.conf
	@echo "OU = Development" >> docker/apache/ssl/openssl.conf
	@echo "CN = epimanager-preprod.episciences.org" >> docker/apache/ssl/openssl.conf
	@echo "emailAddress = dev@episciences.org" >> docker/apache/ssl/openssl.conf
	@echo "" >> docker/apache/ssl/openssl.conf
	@echo "[v3_req]" >> docker/apache/ssl/openssl.conf
	@echo "keyUsage = keyEncipherment, dataEncipherment, digitalSignature" >> docker/apache/ssl/openssl.conf
	@echo "extendedKeyUsage = serverAuth" >> docker/apache/ssl/openssl.conf
	@echo "subjectAltName = @alt_names" >> docker/apache/ssl/openssl.conf
	@echo "" >> docker/apache/ssl/openssl.conf
	@echo "[alt_names]" >> docker/apache/ssl/openssl.conf
	@echo "DNS.1 = epimanager-preprod.episciences.org" >> docker/apache/ssl/openssl.conf
	@echo "DNS.2 = localhost" >> docker/apache/ssl/openssl.conf
	@echo "IP.1 = 127.0.0.1" >> docker/apache/ssl/openssl.conf
	@if [ ! -f "docker/apache/ssl/epimanager-preprod.episciences.org.crt" ]; then \
		openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
			-keyout docker/apache/ssl/epimanager-preprod.episciences.org.key \
			-out docker/apache/ssl/epimanager-preprod.episciences.org.crt \
			-config docker/apache/ssl/openssl.conf \
			-extensions v3_req; \
		echo "$(GREEN)✓ SSL certificates generated$(NC)"; \
	else \
		echo "$(YELLOW)⚠ SSL certificates already exist$(NC)"; \
		echo "  Run 'make ssl-clean ssl-certs' to regenerate"; \
	fi

ssl-clean: ## Remove SSL certificates
	@echo "$(BOLD)Removing SSL certificates...$(NC)"
	@rm -rf docker/apache/ssl/
	@echo "$(GREEN)✓ SSL certificates removed$(NC)"

preprod: ssl-certs ## Start preprod containers with SSL (Docker command on host)
	@echo "$(BOLD)Starting Docker containers for preprod with SSL...$(NC)"
	$(call require_file,docker-compose.yml)
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✓ Containers started$(NC)"
	@echo ""; echo "$(BOLD)🌐 Application URLs:$(NC)"
	@echo "  $(BLUE)HTTP:$(NC)  http://epimanager-preprod.episciences.org"
	@echo "  $(BLUE)HTTPS:$(NC) https://epimanager-preprod.episciences.org"
	@echo ""; echo "$(YELLOW)⚠️  Required configuration:$(NC)"
	@echo "  Add to $(BOLD)/etc/hosts$(NC): $(BOLD)127.0.0.1    epimanager-preprod.episciences.org$(NC)"

preprod-no-ssl: ## Start preprod containers (HTTP only)
	@echo "$(BOLD)Starting Docker containers for preprod (HTTP only)...$(NC)"
	$(call require_file,docker-compose.yml)
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✓ Containers started$(NC)"
	@echo ""; echo "$(BOLD)🌐 Application URLs:$(NC)"
	@echo "  $(BLUE)HTTP:$(NC)  http://epimanager-preprod.episciences.org"
	@echo ""; echo "$(YELLOW)⚠️  Required configuration:$(NC)"
	@echo "  Add to $(BOLD)/etc/hosts$(NC): $(BOLD)127.0.0.1    epimanager-preprod.episciences.org$(NC)"

preprod-ci: ssl-certs ## Start preprod with CI database (compose in host mode)
	@echo "$(BOLD)Starting Docker containers for preprod (CI mode with standalone database)...$(NC)"
	$(call require_file,docker-compose.yml)
	$(call require_file,docker-compose.ci.yml)
	$(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.ci.yml up -d
	@echo "$(GREEN)✓ CI containers started with standalone database$(NC)"
	@echo ""; echo "$(BOLD)🌐 Application URLs:$(NC)"
	@echo "  $(BLUE)HTTP:$(NC)  http://epimanager-preprod.episciences.org"
	@echo "  $(BLUE)HTTPS:$(NC) https://epimanager-preprod.episciences.org"
	@echo ""; echo "$(YELLOW)⚠️  Required configuration:$(NC)"
	@echo "  Add to $(BOLD)/etc/hosts$(NC): $(BOLD)127.0.0.1    epimanager-preprod.episciences.org$(NC)"

preprod-ci-no-ssl: ## Start preprod with CI database (HTTP only)
	@echo "$(BOLD)Starting Docker containers for preprod (CI mode, HTTP only)...$(NC)"
	$(call require_file,docker-compose.yml)
	$(call require_file,docker-compose.ci.yml)
	$(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.ci.yml up -d
	@echo "$(GREEN)✓ CI containers started with standalone database$(NC)"
	@echo ""; echo "$(BOLD)🌐 Application URLs:$(NC)"
	@echo "  $(BLUE)HTTP:$(NC)  http://epimanager-preprod.episciences.org"
	@echo ""; echo "$(YELLOW)⚠️  Required configuration:$(NC)"
	@echo "  Add to $(BOLD)/etc/hosts$(NC): $(BOLD)127.0.0.1    epimanager-preprod.episciences.org$(NC)"

# ==========================
#        PRODUCTION DEPLOYMENT (HOST-ONLY)
# ==========================
.PHONY: composer-install-prod cache-clear cache-warmup deploy-prod deploy deploy-branch deploy-tag

composer-install-prod: ## Install PHP dependencies (prod, host)
	composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --classmap-authoritative

cache-clear: ## Clear Symfony cache (prod, host)
	php bin/console cache:clear --env=prod --no-debug

cache-warmup: ## Warm up Symfony cache (prod, host)
	php bin/console cache:warmup --env=prod --no-debug

# Shared deployment logic (HOST)
define deploy-logic
	@echo "🚀 Starting deployment for: $(1) (host only)"
	@if [ ! -d ".git" ]; then echo "$(RED)✗ Not a git repository$(NC)"; exit 1; fi
	@git fetch --all --tags
	@git checkout --force $(1)
	@git pull --ff-only || true
	@# Version
	@CURRENT_TAG=$$(git describe --tags --abbrev=0 2>/dev/null || echo "no-tag"); \
	[ "$(1)" != "main" ] && CURRENT_TAG="$(1)" || true; \
	DEPLOY_DATE=$$(date "+%Y-%m-%d %X %z"); \
	printf "%s\n" "<?php" "\$$appVersion='$$CURRENT_TAG ($$DEPLOY_DATE)';" > version.php; \
	echo "$(GREEN)Version written to version.php$(NC)"
	@# Frontend dependencies (if present)
	@if command -v yarn >/dev/null 2>&1; then \
		echo "Installing frontend dependencies..."; \
		yarn install --frozen-lockfile || true; \
		echo "Compiling assets (encore)..."; \
		yarn encore production || true; \
	else \
		echo "$(YELLOW)⚠ Yarn not found, skipping assets compilation$(NC)"; \
	fi
	@echo "$(GREEN)Fetch/checkout/pull OK$(NC)"
endef

deploy-prod: ## Complete production deployment (host-only)
	@echo "🚀 Starting production deployment (host only)..."
	@echo ""; echo "📋 1/6 - Installing production dependencies..."; $(MAKE) composer-install-prod
	@echo ""; echo "📋 2/6 - Building production assets..."; $(MAKE) yarn-encore-production || true
	@echo ""; echo "📋 3/6 - Running database migrations..."
	php bin/console doctrine:migrations:migrate --no-interaction --env=prod
	@echo ""; echo "📋 4/6 - Clearing cache..."; $(MAKE) cache-clear
	@echo ""; echo "📋 5/6 - Warming up cache..."; $(MAKE) cache-warmup
	@echo ""; echo "📋 6/6 - Restarting services (via Docker)..."; $(MAKE) restart-httpd; $(MAKE) restart-php
	@echo ""; echo "✅ Production deployment completed successfully!"
	@echo "🌐 Application ready at: http://epimanager-preprod.episciences.org/"

deploy: ## Deploy main branch (host-only)
	$(call deploy-logic,main)
	$(MAKE) deploy-prod

deploy-branch: ## Deploy a branch (make deploy-branch BRANCH=xxx) (host-only)
	@if [ -z "$(BRANCH)" ]; then \
		echo "Usage: make deploy-branch BRANCH=branch-name"; \
		echo "Examples:\n  make deploy-branch BRANCH=develop\n  make deploy-branch BRANCH=feature/new-api"; \
		exit 1; \
	fi
	$(call deploy-logic,$(BRANCH))
	$(MAKE) deploy-prod

deploy-tag: ## Deploy a tag (make deploy-tag TAG=v1.0.0) (host-only)
	@if [ -z "$(TAG)" ]; then \
		echo "Usage: make deploy-tag TAG=tag-name"; \
		echo "Examples:\n  make deploy-tag TAG=v1.0.0\n  make deploy-tag TAG=v2.1.3"; \
		exit 1; \
	fi
	$(call deploy-logic,$(TAG))
	$(MAKE) deploy-prod

# ==========================
#           PHONY
# ==========================
.PHONY: help up down restart logs ps build clean \
	load-db-manager load-db-auth \
	composer-install composer-update npm-build yarn-encore-production can-i-use-update \
	test test-e2e test-php lint lint-fix format format-check lint-php lint-php-file check-all fix-all \
	restart-httpd restart-php enter-container-php enter-container-httpd \
	ssl-certs ssl-clean preprod preprod-no-ssl preprod-ci preprod-ci-no-ssl \
	composer-install-prod cache-clear cache-warmup deploy-prod deploy deploy-branch deploy-tag