FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libsqlite3-dev \
    sqlite3 \
    && docker-php-ext-install pdo_mysql pdo_pgsql pdo_sqlite bcmath intl gd xml zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

ARG PUID=1000
ARG PGID=1000
RUN groupmod -o -g ${PGID} www-data && usermod -o -u ${PUID} -g ${PGID} www-data

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-progress --prefer-dist

COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]
