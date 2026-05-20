# syntax=docker/dockerfile:1
FROM php:8.4-cli-bookworm

# Системные библиотеки для сборки расширений (GD без webp — меньше сбоев на разных платформах).
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        unzip \
        g++ \
        make \
        pkg-config \
        libzip-dev \
        zlib1g-dev \
        libpng-dev \
        libonig-dev \
        libicu-dev \
        icu-devtools \
        libsqlite3-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        jpegoptim \
        optipng \
        webp \
    && rm -rf /var/lib/apt/lists/*

# GD отдельным слоем: при сбое в логе видно именно этот шаг.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# libsqlite3-dev обязателен для сборки pdo_sqlite; sockets — для php-amqplib и др.
RUN docker-php-ext-install bcmath mbstring pdo_mysql pdo_sqlite sockets zip

# intl отдельно: чаще всего падает первым при нехватке ICU/компилятора.
RUN docker-php-ext-install intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Без post-install скриптов Composer (они дергают artisan до появления .env и БД).
# package:discover выполняется в docker-entrypoint.sh при старте контейнера.
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && chmod +x docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["./docker-entrypoint.sh"]
