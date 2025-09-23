DOCKER:= docker
DOCKER_COMPOSE:= docker compose
NPX:= npx
CNTR_NAME_HTTPD := httpd-manager
CNTR_NAME_PHP := php-fpm-manager
CNTR_APP_DIR := /var/www/htdocs
CNTR_APP_USER := www-data

MYSQL_CONNECT_EPISCIENCES:= mysql -u root -proot -h 127.0.0.1 -P 33060 episciences
MYSQL_CONNECT_AUTH:= mysql -u root -proot -h 127.0.0.1 -P 33062 cas_users

.PHONY: build up down clean help test test-php test-e2e lint lint-fix format format-check lint-php lint-php-file check-all fix-all deploy-prod cache-clear cache-warmup composer-install-prod

help: ## Display this help
	@echo "Available targets:"
	@grep -E '^[a-zA-Z_0-9-]+:.*##' Makefile | awk 'BEGIN {FS = ":.*?## "}; {printf "%-30s %s\n", $$1, $$2}'

build: ## Build the docker containers
	$(DOCKER_COMPOSE) build

up: ## Start all the docker containers
	$(DOCKER_COMPOSE) up -d
	@echo "====================================================================="
	@echo "Make sure you have [127.0.0.1 localhost epimanager.episciences.org] in /etc/hosts"
	@echo "Episciences Manager  : http://epimanager.episciences.org/"
	@echo "PhpMyAdmin  : http://localhost:8001/"
	@echo "====================================================================="
	@echo "SQL Place Custom SQL dump files in ~/tmp/"
	@echo "SQL: Import '~/tmp/episciences.sql' with 'make load-db-episciences'"

down: ## Stop the docker containers and remove orphans
	$(DOCKER_COMPOSE) down --remove-orphans

clean: down ## Clean up unused docker resources
	#docker stop $(docker ps -a -q)
	docker system prune -f

load-db-manager: ## Load an SQL dump from ./tmp/episciences.sql
	$(MYSQL_CONNECT_EPISCIENCES) < ~/tmp/episciences.sql

load-db-auth: ## Load an SQL dump from ./tmp/cas_users.sql
	$(MYSQL_CONNECT_AUTH) < ~/tmp/cas_users.sql

composer-install: ## Install composer dependencies
	$(DOCKER_COMPOSE) exec -w $(CNTR_APP_DIR) $(CNTR_NAME_PHP) composer install --no-interaction --prefer-dist --optimize-autoloader

composer-update: ## Update composer dependencies
	$(DOCKER_COMPOSE) exec -w $(CNTR_APP_DIR) $(CNTR_NAME_PHP) composer update --no-interaction --prefer-dist --optimize-autoloader

yarn-encore-production: ## Build production assets (optimized, minified)
	$(DOCKER_COMPOSE) exec -w $(CNTR_APP_DIR) $(CNTR_NAME_PHP) yarn install; yarn encore production

npm-build: ## Build assets for development (fast build)
	npm run build

restart-httpd: ## Restart Apache httpd
	$(DOCKER_COMPOSE) restart $(CNTR_NAME_HTTPD)

restart-php: ## Restart PHP-FPM Container
	$(DOCKER_COMPOSE) restart $(CNTR_NAME_PHP)

can-i-use-update: ## To be launched when Browserslist: caniuse-lite is outdated.
	$(NPX) update-browserslist-db@latest

test: ## Run JavaScript unit tests
	npm test

test-php: ## Run PHP tests with Pest
	./vendor/bin/pest

test-e2e: ## Run E2E tests with Playwright
	npm run test:e2e

lint: ## Check JavaScript code with ESLint
	npm run lint

lint-fix: ## Fix JavaScript code with ESLint (usage: make lint-fix [FILE=path/to/file.js])
	@if [ -n "$(FILE)" ]; then \
		echo "🔧 Fixing specific file: $(FILE)"; \
		npx eslint $(FILE) --fix; \
	else \
		echo "🔧 Fixing all JavaScript files"; \
		npm run lint:fix; \
	fi

format: ## Format JavaScript code with Prettier
	npm run format

format-check: ## Check JavaScript formatting with Prettier
	npm run format:check

lint-php: ## Check PHP syntax in src/ directory
	@echo "🔍 Checking PHP syntax..."
	@find src/ -name "*.php" -exec php -l {} \; | grep -v "No syntax errors detected" || true
	@echo "✅ PHP syntax check completed"

lint-php-file: ## Check PHP syntax for specific file (usage: make lint-php-file FILE=path/to/file.php)
	@if [ -n "$(FILE)" ]; then \
		echo "🔍 Checking PHP syntax for: $(FILE)"; \
		php -l $(FILE); \
	else \
		echo "❌ Usage: make lint-php-file FILE=path/to/file.php"; \
	fi

check-all: ## Run all code quality checks (PHP syntax, JS lint, formatting)
	@echo "🚀 Running all code quality checks..."
	@echo ""
	@echo "📋 1/4 - Checking PHP syntax..."
	@$(MAKE) lint-php
	@echo ""
	@echo "📋 2/4 - Checking JavaScript with ESLint..."
	@$(MAKE) lint
	@echo ""
	@echo "📋 3/4 - Checking JavaScript formatting..."
	@$(MAKE) format-check
	@echo ""
	@echo "📋 4/4 - Running JavaScript tests..."
	@$(MAKE) test
	@echo ""
	@echo "✅ All checks completed successfully!"

fix-all: ## Fix all auto-fixable issues (JS lint, formatting)
	@echo "🔧 Fixing all auto-fixable issues..."
	@echo ""
	@echo "📋 1/2 - Fixing JavaScript with ESLint..."
	@$(MAKE) lint-fix
	@echo ""
	@echo "📋 2/2 - Fixing JavaScript formatting..."
	@$(MAKE) format
	@echo ""
	@echo "✅ All fixes applied!"

enter-container-php: ## Open shell on PHP container
	$(DOCKER) exec -it $(CNTR_NAME_PHP) sh -c "cd /var/www/htdocs && /bin/bash"

enter-container-httpd: ## Open shell on HTTPD container
	$(DOCKER) exec -it $(CNTR_NAME_HTTPD) sh -c "cd /var/www/htdocs && /bin/bash"

# Production commands
composer-install-prod: ## Install production dependencies (no-dev, optimized)
	$(DOCKER_COMPOSE) exec -w $(CNTR_APP_DIR) $(CNTR_NAME_PHP) composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --classmap-authoritative


cache-clear: ## Clear Symfony cache
	$(DOCKER_COMPOSE) exec -w $(CNTR_APP_DIR) $(CNTR_NAME_PHP) php bin/console cache:clear --env=prod --no-debug

cache-warmup: ## Warm up Symfony cache
	$(DOCKER_COMPOSE) exec -w $(CNTR_APP_DIR) $(CNTR_NAME_PHP) php bin/console cache:warmup --env=prod --no-debug

deploy-prod: ## Complete production deployment
	@echo "🚀 Starting production deployment..."
	@echo ""
	@echo "📋 1/6 - Installing production dependencies..."
	@$(MAKE) composer-install-prod
	@echo ""
	@echo "📋 2/6 - Building production assets..."
	@$(MAKE) yarn-encore-production
	@echo ""
	@echo "📋 3/6 - Running database migrations..."
	$(DOCKER_COMPOSE) exec -w $(CNTR_APP_DIR) $(CNTR_NAME_PHP) php bin/console doctrine:migrations:migrate --no-interaction --env=prod
	@echo ""
	@echo "📋 4/6 - Clearing cache..."
	@$(MAKE) cache-clear
	@echo ""
	@echo "📋 5/6 - Warming up cache..."
	@$(MAKE) cache-warmup
	@echo ""
	@echo "📋 6/6 - Restarting services..."
	@$(MAKE) restart-httpd
	@$(MAKE) restart-php
	@echo ""
	@echo "✅ Production deployment completed successfully!"
	@echo "🌐 Application ready at: http://epimanager.episciences.org/"