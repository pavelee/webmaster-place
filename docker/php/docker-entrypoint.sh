#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-production"
	if [ "$BUILD_APP_ENV" = "dev" ]; then
		PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-development"
		composer install --no-scripts --no-interaction --prefer-dist --optimize-autoloader
	fi
	ln -sf "$PHP_INI_RECOMMENDED" "$PHP_INI_DIR/php.ini"
fi

bin/console app:link-templates

exec docker-php-entrypoint "$@"
