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
CNTR_USER_ID   ?= $(shell id -u):$(shell id -g)

# MySQL connections (local mysql client)
MYSQL_CONNECT_EPISCIENCES ?= mysql -u root -proot -h 127.0.0.1 -P 33060 episciences
MYSQL_CONNECT_AUTH ?= mysql -u root -proot -h 127.0.0.1 -P 33062 cas_users

# Checks
define require_file
	@if [ ! -f "$(1)" ]; then \
		echo -e "$(RED)✗ Missing file: $(1)$(NC)"; exit 1; \
	fi
endef

define require_cmd
	@if ! command -v $(1) >/dev/null 2>&1; then \
		echo -e "$(RED)✗ Missing command: $(1)$(NC)"; exit 1; \
	fi
endef

# ==========================
#           HELP
# ==========================
.PHONY: help
help: ## Display available commands list
	@echo ""
	@echo -e "$(BOLD)Episciences Manager - Development Commands$(NC)"
	@echo "=========================================="
	@echo ""
	@echo -e "$(GREEN)Docker Development Commands:$(NC)"
	@grep -E '^(up|down|restart|logs|ps|build|clean):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo -e "$(BLUE)Database Commands:$(NC)"
	@grep -E '^(load-db.*):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo -e "$(YELLOW)Code Quality / Testing Commands:$(NC)"
	@grep -E '^(lint|lint-fix|lint-php|lint-php-file|phpstan|rector|check-all|fix-all|test|test-php|test-e2e|format|format-check):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo -e "$(PURPLE)Container Management:$(NC)"
	@grep -E '^(restart-httpd|restart-php|enter-container.*):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo -e "$(CYAN)SSL / Preproduction Commands:$(NC)"
	@grep -E '^(ssl-certs|ssl-clean|preprod.*):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo -e "$(RED)Deployment Commands:$(NC)"
	@grep -E '^(deploy.*|composer-install-prod|cache-clear|cache-warmup|yarn-encore-production|dump-env-.*):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo -e "$(BLUE)Composer/Yarn Commands:$(NC)"
	@grep -E '^(composer-install|composer-update|yarn-build|yarn-encore-production|can-i-use-update):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  $(BOLD)%-22s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo -e "$(YELLOW)Quick Start:$(NC)"
	@echo -e "  1. Run $(BOLD)make up$(NC) to start all containers"
	@echo -e "  2. Add to /etc/hosts: $(BOLD)127.0.0.1 localhost epimanager-dev.episciences.org$(NC)"
	@echo -e "  3. Access: $(GREEN)http://epimanager-dev.episciences.org/$(NC)"
	@echo ""

# ==========================
#     DOCKER (Local dev)
# ==========================
.PHONY: up down restart logs ps build clean
up: ## Start all containers (in background)
	$(DOCKER_COMPOSE) --env-file .env.local up -d
	@echo "====================================================================="
	@echo "Make sure you have this line in /etc/hosts:"
	@echo "127.0.0.1 localhost epimanager-dev.episciences.org"
	@echo "Episciences Manager : http://epimanager-dev.episciences.org/"
	@echo "PhpMyAdmin          : http://localhost:8001/"
	@echo "====================================================================="
	@echo "SQL: place your dumps in ~/tmp/"
	@echo "SQL: import '~/tmp/episciences.sql' with 'make load-db-manager'"

down: ## Stop and remove containers, networks, orphan volumes
	$(DOCKER_COMPOSE) --env-file .env.local down --remove-orphans

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
#        COMPOSER/YARN
# ==========================
.PHONY: composer-install composer-update yarn-build yarn-encore-production can-i-use-update
composer-install: ## Install Composer dependencies
	composer install --no-interaction --prefer-dist --optimize-autoloader

composer-update: ## Update Composer dependencies
	composer update --no-interaction --prefer-dist --optimize-autoloader

yarn-build: ## Compile assets for development
	yarn build
	$(DOCKER) exec -e APP_ENV=dev $(CNTR_NAME_PHP) php bin/console cache:clear --env=dev

yarn-encore-production: ## Compile assets for production
	yarn install --frozen-lockfile
	yarn encore production

can-i-use-update: ## Update Browserslist (host, when caniuse-lite is outdated)
	$(NPX) update-browserslist-db@latest

# ==========================
#   CODE QUALITY / TESTS (HOST)
# ==========================
.PHONY: test test-e2e test-php lint lint-fix format format-check lint-php lint-php-file phpstan rector check-all fix-all

test: ## Run JavaScript unit tests
	yarn test

test-php: ## Run PHP tests (Pest)
	./vendor/bin/pest

test-e2e: ## Run E2E tests (Playwright)
	yarn test:e2e

lint: ## Check JS code with ESLint
	yarn lint

lint-fix: ## Fix JS code with ESLint (usage: make lint-fix [FILE=src/file.js])
	@if [ -n "$(FILE)" ]; then \
		echo -e "$(BLUE)🔧 Fixing file: $(BOLD)$(FILE)$(NC)"; \
		$(NPX) eslint "$(FILE)" --fix; \
	else \
		echo -e "$(BLUE)🔧 Fixing all JavaScript files$(NC)"; \
		yarn lint:fix; \
	fi

format: ## Format JS code with Prettier
	yarn format

format-check: ## Check JS formatting with Prettier
	yarn format:check

lint-php: ## Check PHP syntax in src/
	@echo -e "$(BLUE)🔍 Checking PHP syntax...$(NC)"
	@find src/ -name "*.php" -print0 | xargs -0 -n1 php -l | grep -v "No syntax errors detected" || true
	@echo -e "$(GREEN)✅ PHP syntax check completed$(NC)"

lint-php-file: ## Check PHP syntax of a file (make lint-php-file FILE=src/file.php)
	@if [ -n "$(FILE)" ]; then \
		echo -e "$(BLUE)🔍 Checking PHP syntax for: $(BOLD)$(FILE)$(NC)"; \
		php -l "$(FILE)"; \
	else \
		echo -e "$(RED)❌ Usage: make lint-php-file FILE=path/to/file.php$(NC)"; \
		exit 2; \
	fi

phpstan: ## Run PHPStan static analysis (make phpstan [TARGET=path/to/file] [LEVEL=X])
	@echo -e "$(BLUE)Ensuring PHPStan cache directory exists and is writable...$(NC)"
	@$(DOCKER_COMPOSE) --env-file .env.local exec -u 0:0 $(CNTR_NAME_PHP) mkdir -p /tmp/phpstan
	@$(DOCKER_COMPOSE) --env-file .env.local exec -u 0:0 $(CNTR_NAME_PHP) chmod -R 777 /tmp/phpstan
	@echo -e "$(BLUE)🔍 Running PHPStan static analysis...$(NC)"
	@$(DOCKER_COMPOSE) --env-file .env.local exec -u $(CNTR_USER_ID) -w $(CNTR_APP_DIR) $(CNTR_NAME_PHP) \
		./vendor/bin/phpstan analyse --memory-limit=1G $(if $(LEVEL),--level $(LEVEL)) $(TARGET)
	@echo -e "$(GREEN)✅ PHPStan analysis completed$(NC)"

rector: ## Run Rector refactoring tool (make rector [TARGET=path/to/file] [DRY_RUN=1])
	@echo -e "$(BLUE)Ensuring Rector cache directories exist and are writable...$(NC)"
	@$(DOCKER_COMPOSE) --env-file .env.local exec -u 0:0 $(CNTR_NAME_PHP) mkdir -p $(CNTR_APP_DIR)/cache/rector /tmp/cache
	@$(DOCKER_COMPOSE) --env-file .env.local exec -u 0:0 $(CNTR_NAME_PHP) chmod -R 777 $(CNTR_APP_DIR)/cache/rector /tmp/cache
	@echo -e "$(BLUE)🔍 Running Rector...$(NC)"
	@$(DOCKER_COMPOSE) --env-file .env.local exec -u $(CNTR_USER_ID) -w $(CNTR_APP_DIR) $(CNTR_NAME_PHP) \
		./vendor/bin/rector process $(TARGET) $(if $(DRY_RUN),--dry-run)
	@echo -e "$(GREEN)✅ Rector completed$(NC)"

check-all: ## Run all checks (PHP, JS, tests)
	@echo -e "🚀 Running all quality checks..."
	@echo -e ""; echo "📋 1/4 - Checking PHP syntax..."; $(MAKE) lint-php
	@echo -e ""; echo "📋 2/4 - Checking JavaScript with ESLint..."; $(MAKE) lint
	@echo -e ""; echo "📋 3/4 - Checking JavaScript formatting..."; $(MAKE) format-check
	@echo -e ""; echo "📋 4/4 - Running JavaScript tests..."; $(MAKE) test
	@echo -e ""; echo "✅ All checks completed successfully!"

fix-all: ## Fix all (lint JS + Prettier)
	@echo -e "🔧 Fixing all auto-fixable issues..."
	@echo -e ""; echo "📋 1/2 - Fixing JavaScript with ESLint..."; $(MAKE) lint-fix
	@echo -e ""; echo "📋 2/2 - Fixing JavaScript formatting..."; $(MAKE) format
	@echo -e ""; echo "✅ All fixes applied!"

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
	@echo -e "$(BOLD)Generating SSL certificates for development...$(NC)"
	$(call require_cmd,openssl)
	@mkdir -p docker/apache/ssl
	@printf "[req]\n" > docker/apache/ssl/openssl.conf
	@printf "default_bits = 2048\n" >> docker/apache/ssl/openssl.conf
	@printf "prompt = no\n" >> docker/apache/ssl/openssl.conf
	@printf "distinguished_name = req_distinguished_name\n" >> docker/apache/ssl/openssl.conf
	@printf "req_extensions = v3_req\n" >> docker/apache/ssl/openssl.conf
	@printf "\n" >> docker/apache/ssl/openssl.conf
	@printf "[req_distinguished_name]\n" >> docker/apache/ssl/openssl.conf
	@printf "C = FR\n" >> docker/apache/ssl/openssl.conf
	@printf "ST = France\n" >> docker/apache/ssl/openssl.conf
	@printf "L = Lyon\n" >> docker/apache/ssl/openssl.conf
	@printf "O = Episciences\n" >> docker/apache/ssl/openssl.conf
	@printf "OU = Development\n" >> docker/apache/ssl/openssl.conf
	@printf "CN = epimanager-preprod.episciences.org\n" >> docker/apache/ssl/openssl.conf
	@printf "emailAddress = dev@episciences.org\n" >> docker/apache/ssl/openssl.conf
	@printf "\n" >> docker/apache/ssl/openssl.conf
	@printf "[v3_req]\n" >> docker/apache/ssl/openssl.conf
	@printf "keyUsage = keyEncipherment, dataEncipherment, digitalSignature\n" >> docker/apache/ssl/openssl.conf
	@printf "extendedKeyUsage = serverAuth\n" >> docker/apache/ssl/openssl.conf
	@printf "subjectAltName = @alt_names\n" >> docker/apache/ssl/openssl.conf
	@printf "\n" >> docker/apache/ssl/openssl.conf
	@printf "[alt_names]\n" >> docker/apache/ssl/openssl.conf
	@printf "DNS.1 = epimanager-preprod.episciences.org\n" >> docker/apache/ssl/openssl.conf
	@printf "DNS.2 = localhost\n" >> docker/apache/ssl/openssl.conf
	@printf "IP.1 = 127.0.0.1\n" >> docker/apache/ssl/openssl.conf
	@if [ ! -f "docker/apache/ssl/epimanager-preprod.episciences.org.crt" ]; then \
		openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
			-keyout docker/apache/ssl/epimanager-preprod.episciences.org.key \
			-out docker/apache/ssl/epimanager-preprod.episciences.org.crt \
			-config docker/apache/ssl/openssl.conf \
			-extensions v3_req; \
		echo -e "$(GREEN)✓ SSL certificates generated$(NC)"; \
	else \
		echo -e "$(YELLOW)⚠ SSL certificates already exist$(NC)"; \
		echo "  Run 'make ssl-clean ssl-certs' to regenerate"; \
	fi

ssl-clean: ## Remove SSL certificates
	@echo -e "$(BOLD)Removing SSL certificates...$(NC)"
	@rm -rf docker/apache/ssl/
	@echo -e "$(GREEN)✓ SSL certificates removed$(NC)"

preprod-setup: ## Complete preprod setup (build assets + compile env + start containers)
	@echo -e "🚀 Setting up complete preprod environment..."
	@echo -e ""; echo "📋 1/3 - Building production assets..."; $(MAKE) yarn-encore-production
	@echo -e ""; echo "📋 2/3 - Compiling environment variables..."; $(MAKE) dump-env-preprod || true
	@echo -e ""; echo "📋 3/3 - Starting preprod containers..."; $(MAKE) preprod
	@echo -e ""; echo "✅ Preprod environment ready!"

preprod: ssl-certs ## Start preprod containers with SSL (Docker command on host)
	@echo -e "$(BOLD)Starting Docker containers for preprod with SSL...$(NC)"
	$(call require_file,docker-compose.yaml)
	$(DOCKER_COMPOSE) --env-file .env.local up -d
	@echo -e "$(GREEN)✓ Containers started$(NC)"
	@echo -e "$(BOLD)🌐 Application URLs:$(NC)"
	@echo -e "  $(BLUE)HTTP:$(NC)  http://epimanager-preprod.episciences.org"
	@echo -e "  $(BLUE)HTTPS:$(NC) https://epimanager-preprod.episciences.org"
	@echo -e "$(YELLOW)⚠️  Required configuration:$(NC)"
	@echo -e "  Add to $(BOLD)/etc/hosts$(NC): $(BOLD)127.0.0.1    epimanager-preprod.episciences.org$(NC)"

preprod-no-ssl: ## Start preprod containers (HTTP only)
	@echo -e "$(BOLD)Starting Docker containers for preprod (HTTP only)...$(NC)"
	$(call require_file,docker-compose.yml)
	$(DOCKER_COMPOSE) --env-file .env.local up -d
	@echo -e "$(GREEN)✓ Containers started$(NC)"
	@echo -e ""; echo "$(BOLD)🌐 Application URLs:$(NC)"
	@echo -e "  $(BLUE)HTTP:$(NC)  http://epimanager-preprod.episciences.org"
	@echo -e ""; echo "$(YELLOW)⚠️  Required configuration:$(NC)"
	@echo -e "  Add to $(BOLD)/etc/hosts$(NC): $(BOLD)127.0.0.1    epimanager-preprod.episciences.org$(NC)"

preprod-ci: ssl-certs ## Start preprod with CI database (compose in host mode)
	@echo -e "$(BOLD)Starting Docker containers for preprod (CI mode with standalone database)...$(NC)"
	$(call require_file,docker-compose.yml)
	$(call require_file,docker-compose.ci.yml)
	$(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.ci.yml up -d
	@echo -e "$(GREEN)✓ CI containers started with standalone database$(NC)"
	@echo -e ""; echo "$(BOLD)🌐 Application URLs:$(NC)"
	@echo -e "  $(BLUE)HTTP:$(NC)  http://epimanager-preprod.episciences.org"
	@echo -e "  $(BLUE)HTTPS:$(NC) https://epimanager-preprod.episciences.org"
	@echo -e ""; echo "$(YELLOW)⚠️  Required configuration:$(NC)"
	@echo -e "  Add to $(BOLD)/etc/hosts$(NC): $(BOLD)127.0.0.1    epimanager-preprod.episciences.org$(NC)"

preprod-ci-no-ssl: ## Start preprod with CI database (HTTP only)
	@echo -e "$(BOLD)Starting Docker containers for preprod (CI mode, HTTP only)...$(NC)"
	$(call require_file,docker-compose.yml)
	$(call require_file,docker-compose.ci.yml)
	$(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.ci.yml up -d
	@echo -e "$(GREEN)✓ CI containers started with standalone database$(NC)"
	@echo -e ""; echo "$(BOLD)🌐 Application URLs:$(NC)"
	@echo -e "  $(BLUE)HTTP:$(NC)  http://epimanager-preprod.episciences.org"
	@echo -e ""; echo "$(YELLOW)⚠️  Required configuration:$(NC)"
	@echo -e "  Add to $(BOLD)/etc/hosts$(NC): $(BOLD)127.0.0.1    epimanager-preprod.episciences.org$(NC)"

# ==========================
#        PRODUCTION DEPLOYMENT
# ==========================
.PHONY: composer-install-prod cache-clear cache-warmup deploy-prod deploy deploy-branch deploy-tag

composer-install-prod: ## Install PHP dependencies (prod, host)
	composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --classmap-authoritative

cache-clear: ## Clear Symfony cache (prod environment)
	php bin/console cache:clear --env=prod

cache-warmup: ## Warm up Symfony cache (prod environment)
	php bin/console cache:warmup --env=prod

# Shared deployment logic
define deploy-logic
	@echo -e "$(BOLD)🚀 Starting deployment for: $(YELLOW)$(1)$(NC) $(BLUE)(host only)$(NC)"
	@if [ ! -d ".git" ]; then echo -e "$(RED)✗ Not a git repository$(NC)"; exit 1; fi
	@git fetch --all --tags
	@git checkout --force $(1)
	@git pull --ff-only || true
	@# Version
	@CURRENT_TAG=$$(git describe --tags --abbrev=0 2>/dev/null || echo "no-tag")
; \
	[ "$(1)" != "main" ] && CURRENT_TAG="$(1)" || true; \
	DEPLOY_DATE=$$(date "+%Y-%m-%d %X %z")
; \
	printf "%s\n" "<?php" "\$$appVersion='$$CURRENT_TAG ($$DEPLOY_DATE)';" > version.php; \
	echo -e "$(GREEN)Version written to version.php$(NC)"
	@# Frontend dependencies (if present)
	@if command -v yarn >/dev/null 2>&1; then \
		echo -e "$(BLUE)Installing frontend dependencies...$(NC)"; \
		yarn install --frozen-lockfile || true; \
		echo -e "$(BLUE)Compiling assets (encore)...$(NC)"; \
		yarn encore production || true; \
	else \
		echo -e "$(YELLOW)⚠ Yarn not found, skipping assets compilation$(NC)"; \
	fi
	@echo -e "$(GREEN)Fetch/checkout/pull OK$(NC)"
endef

dump-env-preprod: ## Compile .env files for preprod environment (optimizes performance)
	@echo -e "$(BOLD)Compiling environment variables for preprod...$(NC)\n"
	$(DOCKER) exec $(CNTR_NAME_PHP) composer dump-env preprod
	@echo -e "$(GREEN)✓ Environment compiled to .env.local.php$(NC)\n"

dump-env-prod: ## Compile .env files for production environment (optimizes performance)
	@echo -e "$(BOLD)Compiling environment variables for production...$(NC)\n"
	$(DOCKER) exec $(CNTR_NAME_PHP) composer dump-env prod
	@echo -e "$(GREEN)✓ Environment compiled to .env.local.php$(NC)\n"

deploy-prod: ## Complete production deployment
	@echo -e "$(BOLD)🚀 Starting production deployment $(BLUE)(host only)$(NC)..."
	@echo -e ""; echo -e "$(BLUE)📋 1/7 - Installing production dependencies...$(NC)"; $(MAKE) composer-install-prod
	@echo -e ""; echo -e "$(BLUE)📋 2/7 - Building production assets...$(NC)"; $(MAKE) yarn-encore-production || true
	@echo -e ""; echo -e "$(BLUE)📋 3/7 - Compiling environment...$(NC)"; $(MAKE) dump-env-prod || true
	@echo -e ""; echo -e "$(BLUE)📋 4/7 - Running database migrations...$(NC)"
	php bin/console doctrine:migrations:migrate --no-interaction --env=prod
	@echo -e ""; echo -e "$(BLUE)📋 5/7 - Clearing cache...$(NC)"; $(MAKE) cache-clear
	@echo -e ""; echo -e "$(BLUE)📋 6/7 - Warming up cache...$(NC)"; $(MAKE) cache-warmup
	@echo -e ""; echo -e "$(BLUE)📋 7/7 - Restarting services (via Docker)...$(NC)"; $(MAKE) restart-httpd; $(MAKE) restart-php
	@echo -e ""; echo -e "$(GREEN)✅ Production deployment completed successfully!$(NC)"
	@echo -e "$(GREEN)🌐 Application ready at: $(BOLD)http://epimanager-preprod.episciences.org/$(NC)"

deploy: ## Deploy main branch (host-only)
	$(call deploy-logic,main)
	$(MAKE) deploy-prod

deploy-branch: ## Deploy a branch (make deploy-branch BRANCH=xxx) (host-only)
	@if [ -z "$(BRANCH)" ]; then \
		echo -e "$(RED)Usage: make deploy-branch BRANCH=branch-name$(NC)"; \
		echo -e "$(YELLOW)Examples:$(NC)"; \
		echo -e "  $(BOLD)make deploy-branch BRANCH=develop$(NC)"; \
		echo -e "  $(BOLD)make deploy-branch BRANCH=feature/new-api$(NC)"; \
		exit 1; \
	fi
	$(call deploy-logic,$(BRANCH))
	$(MAKE) deploy-prod

deploy-tag: ## Deploy a tag (make deploy-tag TAG=v1.0.0) (host-only)
	@if [ -z "$(TAG)" ]; then \
		echo -e "$(RED)Usage: make deploy-tag TAG=tag-name$(NC)"; \
		echo -e "$(YELLOW)Examples:$(NC)"; \
		echo -e "  $(BOLD)make deploy-tag TAG=v1.0.0$(NC)"; \
		echo -e "  $(BOLD)make deploy-tag TAG=v2.1.3$(NC)"; \
		exit 1; \
	fi
	$(call deploy-logic,$(TAG))
	$(MAKE) deploy-prod

# ==========================
#           PHONY
# ==========================
.PHONY: help up down restart logs ps build clean \
	load-db-manager load-db-auth \
	composer-install composer-update yarn-build yarn-encore-production can-i-use-update \
	test test-e2e test-php lint lint-fix format format-check lint-php lint-php-file phpstan rector check-all fix-all \
	restart-httpd restart-php enter-container-php enter-container-httpd \
	ssl-certs ssl-clean preprod preprod-no-ssl preprod-ci preprod-ci-no-ssl \
	composer-install-prod cache-clear cache-warmup deploy-prod deploy deploy-branch deploy-tag