#!/bin/bash
set -e

# Création des répertoires nécessaires s'ils n'existent pas
mkdir -p /var/www/data /var/www/cache /var/www/logs
mkdir -p /var/www/htdocs/var/cache /var/www/htdocs/var/log

# Attribution des permissions larges (lecture/écriture/exécution pour tous)
chmod -R 777 /var/www/htdocs/var/cache /var/www/htdocs/var/log

# Attribution des droits à www-data
chown -R www-data:www-data /var/www/data /var/www/cache /var/www/logs
chown -R www-data:www-data /var/www/htdocs/var/cache /var/www/htdocs/var/log

# Installation des dépendances via Composer (en tant que www-data)
su www-data -s /bin/sh -c "cd /var/www/htdocs && composer install --no-interaction --prefer-dist --optimize-autoloader"

# Vider le cache Symfony si le script console existe
if [ -f "/var/www/htdocs/bin/console" ]; then
  su www-data -s /bin/sh -c "cd /var/www/htdocs && php bin/console cache:clear --env=dev"
fi

# Exécution de la commande passée à l'entrée (php-fpm généralement)
exec "$@"
