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
    linux-headers

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

# ── Nginx config ──────────────────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/nginx.conf

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

# Expose port 80 (Render routes port 10000 → 80 internally via $PORT)
EXPOSE 10000

# Deploy script is the entrypoint
COPY docker/start.sh /start.sh
# Strip Windows CRLF line endings (if developed on Windows) — must run on Linux
RUN sed -i 's/\r$//' /start.sh \
    && chmod +x /start.sh

CMD ["/start.sh"]
