FROM php:8.2-cli

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer \
    APP_ENV=production \
    APP_DEBUG=false \
    PORT=10000

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_sqlite sqlite3 mbstring zip exif pcntl bcmath \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader \
    && npm install \
    && npm run build \
    && mkdir -p database storage bootstrap/cache \
    && touch database/database.sqlite \
    && chmod -R 775 storage bootstrap/cache database

EXPOSE 10000

CMD sh -c "php artisan serve --host 0.0.0.0 --port ${PORT}"