# ==========================
#       CONFIG & COULEURS
# ==========================
SHELL := /bin/bash
.SHELLFLAGS := -o pipefail -c
.DEFAULT_GOAL := help

BOLD  ?= \033[1m
NC    ?= \033[0m
RED   ?= \033[0;31m
GREEN ?= \033[0;32m
YELLOW?= \033[0;33m
BLUE  ?= \033[0;34m

DOCKER         ?= docker
DOCKER_COMPOSE ?= docker compose
NPX            ?= npx
CNTR_NAME_HTTPD?= httpd-manager
CNTR_NAME_PHP  ?= php-fpm-manager
CNTR_APP_DIR   ?= /var/www/htdocs

# Connexions MySQL (client mysql local)
MYSQL_CONNECT_EPISCIENCES ?= mysql -u root -proot -h 127.0.0.1 -P 33060 episciences
MYSQL_CONNECT_AUTH ?= mysql -u root -proot -h 127.0.0.1 -P 33062 cas_users

# Vérifications
define require_file
	@if [ ! -f "$(1)" ]; then \
		echo "$(RED)✗ Fichier manquant: $(1)$(NC)"; exit 1; \
	fi
endef

define require_cmd
	@if ! command -v $(1) >/dev/null 2>&1; then \
		echo "$(RED)✗ Commande manquante: $(1)$(NC)"; exit 1; \
	fi
endef

# ==========================
#           HELP
# ==========================
.PHONY: help
help: ## Display available commands list
	@echo ""
	@echo "$(BOLD)📖 Commandes disponibles :$(NC)"
	@echo ""
	@echo "$(BOLD)▶ Docker (développement local)$(NC)"
	@grep -E '^(up|down|restart|logs|ps|build|clean):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  %-22s %s\n", $$1, $$2}'
	@echo ""
	@echo "$(BOLD)▶ Base de données$(NC)"
	@grep -E '^(load-db.*):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  %-22s %s\n", $$1, $$2}'
	@echo ""
	@echo "$(BOLD)▶ Qualité de code / Tests$(NC)"
	@grep -E '^(lint|lint-fix|lint-php|lint-php-file|check-all|fix-all|test|test-php|test-e2e|format|format-check):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  %-22s %s\n", $$1, $$2}'
	@echo ""
	@echo "$(BOLD)▶ Conteneurs$(NC)"
	@grep -E '^(restart-httpd|restart-php|enter-container.*):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  %-22s %s\n", $$1, $$2}'
	@echo ""
	@echo "$(BOLD)▶ SSL / Préprod$(NC)"
	@grep -E '^(ssl-certs|ssl-clean|preprod.*):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  %-22s %s\n", $$1, $$2}'
	@echo ""
	@echo "$(BOLD)▶ Déploiement (prod)$(NC)"
	@grep -E '^(deploy.*|composer-install-prod|cache-clear|cache-warmup|yarn-encore-production):.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS=":.*?## "}; {printf "  %-22s %s\n", $$1, $$2}'
	@echo ""

# ==========================
#     DOCKER (Dév local)
# ==========================
.PHONY: up down restart logs ps build clean
up: ## Start all containers (in background)
	$(DOCKER_COMPOSE) up -d
	@echo "====================================================================="
	@echo "Assurez-vous d'avoir la ligne dans /etc/hosts :"
	@echo "127.0.0.1 localhost epimanager.episciences.org"
	@echo "Episciences Manager : http://epimanager.episciences.org/"
	@echo "PhpMyAdmin          : http://localhost:8001/"
	@echo "====================================================================="
	@echo "SQL : placez vos dumps dans ~/tmp/"
	@echo "SQL : importez '~/tmp/episciences.sql' avec 'make load-db-manager'"

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
#      BASE DE DONNÉES
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
#   QUALITÉ DE CODE / TESTS (HOST)
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
		echo "🔧 Correction du fichier : $(FILE)"; \
		$(NPX) eslint "$(FILE)" --fix; \
	else \
		echo "🔧 Correction de tous les fichiers JavaScript"; \
		npm run lint:fix; \
	fi

format: ## Format JS code with Prettier
	npm run format

format-check: ## Check JS formatting with Prettier
	npm run format:check

lint-php: ## Check PHP syntax in src/
	@echo "🔍 Vérification de la syntaxe PHP..."
	@find src/ -name "*.php" -print0 | xargs -0 -n1 php -l | grep -v "No syntax errors detected" || true
	@echo "✅ Vérification de la syntaxe PHP terminée"

lint-php-file: ## Check PHP syntax of a file (make lint-php-file FILE=src/file.php)
	@if [ -n "$(FILE)" ]; then \
		echo "🔍 Vérification de la syntaxe PHP pour : $(FILE)"; \
		php -l "$(FILE)"; \
	else \
		echo "❌ Usage : make lint-php-file FILE=chemin/vers/fichier.php"; \
		exit 2; \
	fi

check-all: ## Run all checks (PHP, JS, tests)
	@echo "🚀 Lancement de toutes les vérifications qualité..."
	@echo ""; echo "📋 1/4 - Vérification de la syntaxe PHP..."; $(MAKE) lint-php
	@echo ""; echo "📋 2/4 - Vérification JavaScript avec ESLint..."; $(MAKE) lint
	@echo ""; echo "📋 3/4 - Vérification du formatage JavaScript..."; $(MAKE) format-check
	@echo ""; echo "📋 4/4 - Exécution des tests JavaScript..."; $(MAKE) test
	@echo ""; echo "✅ Toutes les vérifications terminées avec succès !"

fix-all: ## Fix all (lint JS + Prettier)
	@echo "🔧 Correction de tous les problèmes auto-réparables..."
	@echo ""; echo "📋 1/2 - Correction JavaScript avec ESLint..."; $(MAKE) lint-fix
	@echo ""; echo "📋 2/2 - Correction du formatage JavaScript..."; $(MAKE) format
	@echo ""; echo "✅ Toutes les corrections appliquées !"

# ==========================
#        CONTENEURS
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
	@echo "$(BOLD)Génération des certificats SSL pour le développement...$(NC)"
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
		echo "$(GREEN)✓ Certificats SSL générés$(NC)"; \
	else \
		echo "$(YELLOW)⚠ Certificats SSL déjà existants$(NC)"; \
		echo "  Lancez 'make ssl-clean ssl-certs' pour regénérer"; \
	fi

ssl-clean: ## Remove SSL certificates
	@echo "$(BOLD)Suppression des certificats SSL...$(NC)"
	@rm -rf docker/apache/ssl/
	@echo "$(GREEN)✓ Certificats SSL supprimés$(NC)"

preprod: ssl-certs ## Start preprod containers with SSL (Docker command on host)
	@echo "$(BOLD)Démarrage des conteneurs Docker pour la préprod avec SSL...$(NC)"
	$(call require_file,docker-compose.yml)
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✓ Conteneurs démarrés$(NC)"
	@echo ""; echo "$(BOLD)🌐 URLs de l'application :$(NC)"
	@echo "  $(BLUE)HTTP:$(NC)  http://epimanager-preprod.episciences.org"
	@echo "  $(BLUE)HTTPS:$(NC) https://epimanager-preprod.episciences.org"
	@echo ""; echo "$(YELLOW)⚠️  Configuration requise :$(NC)"
	@echo "  Ajoutez dans $(BOLD)/etc/hosts$(NC):  $(BOLD)127.0.0.1    epimanager-preprod.episciences.org$(NC)"

preprod-no-ssl: ## Start preprod containers (HTTP only)
	@echo "$(BOLD)Démarrage des conteneurs Docker pour la préprod (HTTP uniquement)...$(NC)"
	$(call require_file,docker-compose.yml)
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✓ Conteneurs démarrés$(NC)"
	@echo ""; echo "$(BOLD)🌐 URLs de l'application :$(NC)"
	@echo "  $(BLUE)HTTP:$(NC)  http://epimanager-preprod.episciences.org"
	@echo ""; echo "$(YELLOW)⚠️  Configuration requise :$(NC)"
	@echo "  Ajoutez dans $(BOLD)/etc/hosts$(NC):  $(BOLD)127.0.0.1    epimanager-preprod.episciences.org$(NC)"

preprod-ci: ssl-certs ## Start preprod with CI database (compose in host mode)
	@echo "$(BOLD)Démarrage des conteneurs Docker pour la préprod (mode CI avec base autonome)...$(NC)"
	$(call require_file,docker-compose.yml)
	$(call require_file,docker-compose.ci.yml)
	$(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.ci.yml up -d
	@echo "$(GREEN)✓ Conteneurs CI démarrés avec base autonome$(NC)"
	@echo ""; echo "$(BOLD)🌐 URLs de l'application :$(NC)"
	@echo "  $(BLUE)HTTP:$(NC)  http://epimanager-preprod.episciences.org"
	@echo "  $(BLUE)HTTPS:$(NC) https://epimanager-preprod.episciences.org"
	@echo ""; echo "$(YELLOW)⚠️  Configuration requise :$(NC)"
	@echo "  Ajoutez dans $(BOLD)/etc/hosts$(NC):  $(BOLD)127.0.0.1    epimanager-preprod.episciences.org$(NC)"

preprod-ci-no-ssl: ## Start preprod with CI database (HTTP only)
	@echo "$(BOLD)Démarrage des conteneurs Docker pour la préprod (mode CI, HTTP uniquement)...$(NC)"
	$(call require_file,docker-compose.yml)
	$(call require_file,docker-compose.ci.yml)
	$(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.ci.yml up -d
	@echo "$(GREEN)✓ Conteneurs CI démarrés avec base autonome$(NC)"
	@echo ""; echo "$(BOLD)🌐 URLs de l'application :$(NC)"
	@echo "  $(BLUE)HTTP:$(NC)  http://epimanager-preprod.episciences.org"
	@echo ""; echo "$(YELLOW)⚠️  Configuration requise :$(NC)"
	@echo "  Ajoutez dans $(BOLD)/etc/hosts$(NC):  $(BOLD)127.0.0.1    epimanager-preprod.episciences.org$(NC)"

# ==========================
#        DEPLOIEMENT PROD (HOST-ONLY)
# ==========================
.PHONY: composer-install-prod cache-clear cache-warmup deploy-prod deploy deploy-branch deploy-tag

composer-install-prod: ## Install PHP dependencies (prod, host)
	composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --classmap-authoritative

cache-clear: ## Clear Symfony cache (prod, host)
	php bin/console cache:clear --env=prod --no-debug

cache-warmup: ## Warm up Symfony cache (prod, host)
	php bin/console cache:warmup --env=prod --no-debug

# Macro de déploiement partagé (HOST)
define deploy-logic
	@echo "🚀 Démarrage du déploiement pour : $(1) (host uniquement)"
	@if [ ! -d ".git" ]; then echo "$(RED)✗ Pas un dépôt git$(NC)"; exit 1; fi
	@git fetch --all --tags
	@git checkout --force $(1)
	@git pull --ff-only || true
	@# Version
	@CURRENT_TAG=$$(git describe --tags --abbrev=0 2>/dev/null || echo "no-tag"); \
	[ "$(1)" != "main" ] && CURRENT_TAG="$(1)" || true; \
	DEPLOY_DATE=$$(date "+%Y-%m-%d %X %z"); \
	printf "%s\n" "<?php" "\$$appVersion='$$CURRENT_TAG ($$DEPLOY_DATE)';" > version.php; \
	echo "$(GREEN)Version écrite dans version.php$(NC)"
	@# Dépendances front (si présent)
	@if command -v yarn >/dev/null 2>&1; then \
		echo "Installation des dépendances front..."; \
		yarn install --frozen-lockfile || true; \
		echo "Compilation des assets (encore)..."; \
		yarn encore production || true; \
	else \
		echo "$(YELLOW)⚠ Yarn introuvable, compilation des assets ignorée$(NC)"; \
	fi
	@echo "$(GREEN)Fetch/checkout/pull OK$(NC)"
endef

deploy-prod: ## Complete production deployment (host-only)
	@echo "🚀 Démarrage du déploiement production (host uniquement)..."
	@echo ""; echo "📋 1/6 - Installation des dépendances de production..."; $(MAKE) composer-install-prod
	@echo ""; echo "📋 2/6 - Compilation des assets de production..."; $(MAKE) yarn-encore-production || true
	@echo ""; echo "📋 3/6 - Exécution des migrations de base de données..."
	php bin/console doctrine:migrations:migrate --no-interaction --env=prod
	@echo ""; echo "📋 4/6 - Nettoyage du cache..."; $(MAKE) cache-clear
	@echo ""; echo "📋 5/6 - Préchauffage du cache..."; $(MAKE) cache-warmup
	@echo ""; echo "📋 6/6 - Redémarrage des services (via Docker)..."; $(MAKE) restart-httpd; $(MAKE) restart-php
	@echo ""; echo "✅ Déploiement de production terminé avec succès !"
	@echo "🌐 Application prête sur : http://epimanager-preprod.episciences.org/"

deploy: ## Deploy main branch (host-only)
	$(call deploy-logic,main)
	$(MAKE) deploy-prod

deploy-branch: ## Deploy a branch (make deploy-branch BRANCH=xxx) (host-only)
	@if [ -z "$(BRANCH)" ]; then \
		echo "$(RED)Usage : make deploy-branch BRANCH=nom-branche$(NC)"; \
		echo "Exemples :\n  make deploy-branch BRANCH=develop\n  make deploy-branch BRANCH=feature/new-api"; \
		exit 1; \
	fi
	$(call deploy-logic,$(BRANCH))
	$(MAKE) deploy-prod

deploy-tag: ## Deploy a tag (make deploy-tag TAG=v1.0.0) (host-only)
	@if [ -z "$(TAG)" ]; then \
		echo "$(RED)Usage : make deploy-tag TAG=nom-tag$(NC)"; \
		echo "Exemples :\n  make deploy-tag TAG=v1.0.0\n  make deploy-tag TAG=v2.1.3"; \
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
