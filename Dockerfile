FROM php:8.2-fpm-alpine

# ── System dependencies ──────────────────────────────────────────────────────
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    git \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    postgresql-dev \
    linux-headers \
    bash

# ── PHP Extensions ────────────────────────────────────────────────────────────
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        opcache

# ── Composer ──────────────────────────────────────────────────────────────────
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ── Runtime directories ───────────────────────────────────────────────────────
RUN mkdir -p \
    /var/log/nginx \
    /var/log/supervisor \
    /run/nginx \
    && touch /run/nginx.pid

# ── Nginx config ──────────────────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# ── Supervisor config ─────────────────────────────────────────────────────────
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ── Application code ─────────────────────────────────────────────────────────
WORKDIR /var/www/html
COPY . .

# Install production dependencies only (no dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Storage & cache directories with proper permissions
RUN mkdir -p \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache \
        storage/logs \
        bootstrap/cache \
        public/uploads/profiles \
    && chmod -R 775 storage bootstrap/cache public/uploads \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads

# ── Entrypoint script ─────────────────────────────────────────────────────────
COPY docker/start.sh /start.sh
# Strip Windows CRLF line endings — critical for Linux execution
RUN sed -i 's/\r$//' /start.sh \
    && chmod +x /start.sh

EXPOSE 10000

CMD ["/start.sh"]
