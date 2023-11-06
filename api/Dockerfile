FROM php:7.4-fpm-alpine as main

# system libs

RUN apk add --no-cache \
		git

ENV BUILD_DEPS="curl-dev mysql-dev libzip-dev"
RUN set -xe \
	&& apk add --no-cache --virtual .build-deps \
		icu-dev \
		postgresql-dev \
        libxml2-dev \
        $PHPIZE_DEPS \
        $BUILD_DEPS \
        linux-headers \
    && docker-php-ext-install -j$(nproc) \
		bcmath \
		intl \
		mysqli \
		soap \
		sockets \
		pdo_pgsql \
		zip \
    && pecl install \
    		apcu-5.1.22 \
    && /usr/local/bin/docker-php-source extract \
	&& cp -r /usr/src/php/ext/mysqli /usr/local/include/php/ext/ \
	&& cp -r /usr/src/php/ext/curl /usr/local/include/php/ext/ \
    && docker-php-ext-enable --ini-name 20-apcu.ini apcu \
    && docker-php-ext-enable --ini-name 05-opcache.ini opcache \
    && runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )" \
    && apk add --no-cache --virtual .api-phpexts-rundeps $runDeps \
    && apk del --no-network .build-deps $BUILD_DEPS $PHPIZE_DEPS \
    && pecl clear-cache

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /srv/api

# install deps
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress \
	&& composer clear-cache

COPY --chown=www-data:www-data . ./

CMD ["php-fpm"]

FROM main as dev
ARG XDEBUG_VERSION=2.9.1

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps

RUN echo 'xdebug.remote_autostart=true' >> /usr/local/etc/php/php.ini && \
    echo 'xdebug.remote_mode=req' >> /usr/local/etc/php/php.ini && \
    echo 'xdebug.remote_connect_back=1 ' >> /usr/local/etc/php/php.ini && \
    echo 'xdebug.remote_port=9003' >> /usr/local/etc/php/php.ini && \
    echo 'xdebug.remote_enable=1' >> /usr/local/etc/php/php.ini && \
    echo 'xdebug.idekey=xdebug' >> /usr/local/etc/php/php.ini && \
    echo 'xdebug.remote_host=172.18.0.1' >> /usr/local/etc/php/php.ini
