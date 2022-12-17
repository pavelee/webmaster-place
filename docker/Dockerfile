ARG PHP_VER=8.0
ARG NGINX_VER=1.17
ARG ALPINE_VER=3.13
ARG TZ=Europe/Warsaw

FROM php:${PHP_VER}-fpm-alpine${ALPINE_VER} AS php-fpm-dev

RUN apk add --no-cache tzdata
RUN ln -snf /usr/share/zoneinfo/Europe/Warsaw /etc/localtime && echo Europe/Warsaw > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "Europe/Warsaw"\n' > /$PHP_INI_DIR/conf.d/tzone.ini

RUN apk add --no-cache \
		acl \
		fcgi \
		# file \
		# gettext \
		# git \
		gnu-libiconv \
	;

ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

ARG APCU_VERSION=5.1.20
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		zlib-dev \
		libxml2-dev \
		libldap \
		openldap-dev \
	; \
	\
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		pdo_mysql \
		zip \
		soap \
		ldap \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
	;

COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN  chmod +x /usr/local/bin/docker-entrypoint

COPY --from=composer:2.2.6 /usr/bin/composer /usr/bin/composer

ARG USER_ID=1000
RUN apk --no-cache add shadow && usermod -u ${USER_ID} www-data

RUN chown -R www-data:www-data .
RUN chown -R www-data:www-data /usr/local/etc/php

# copy php configuration
COPY docker/php/conf.d/dev.ini $PHP_INI_DIR/php.ini-development
COPY docker/php/conf.d/prod.ini $PHP_INI_DIR/php.ini-production

USER www-data

WORKDIR /srv/app

# we are not using APP_ENV to not overwrite what is in .env files
# symfony uses .env file to determine enviorment!
ENV BUILD_APP_ENV=dev

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# FROM php-fpm-dev AS php-fpm-pre

# ENV BUILD_APP_ENV=pre

# # temporary switch back to root, just to execute lines below
# USER root

# ARG USER_ID=1000
# RUN apk --no-cache add shadow && usermod -u ${USER_ID} www-data

# RUN apk add dcron libcap; \
# 	touch /etc/crontabs/www-data; \
#     chown www-data:www-data /etc/crontabs/www-data; \
# 	chmod -R g+rwx /etc/crontabs/; \
#     chown www-data:www-data /usr/sbin/crond && setcap cap_setgid=ep /usr/sbin/crond; \
# 	chmod g+rwx /usr/sbin/crond; \
# 	crontab /etc/crontabs/www-data;

# COPY .env ./
# COPY .env.local ./
# COPY bin bin/
# COPY config config/
# COPY migrations migrations/
# COPY public public/
# COPY src src/
# COPY templates templates/
# COPY translations translations/
# COPY composer.json composer.lock symfony.lock ./

# RUN chown -R www-data:www-data .
# RUN chown -R www-data:www-data /usr/local/etc/php

# RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

# USER www-data

# FROM php-fpm-pre AS php-fpm-prod

# ENV BUILD_APP_ENV=prod

FROM nginx:${NGINX_VER}-alpine AS nginx-dev

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /src/app

# FROM nginx-dev AS nginx

# COPY --from=php-fpm-pre /wipeo/public ./