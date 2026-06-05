#!/bin/sh
set -e

echo "========================================"
echo "  AI Interview System - Deploy Script"
echo "========================================"

# ── Step 0: Create .env from injected environment variables ──────────────────
# In Docker/Render, there is no .env file — Laravel reads env vars directly.
# But some artisan commands (key:generate) need a writable .env file.
# We create a minimal stub so artisan is happy.
if [ ! -f /var/www/html/.env ]; then
    echo "[0/6] Creating .env stub from environment variables..."
    cat > /var/www/html/.env << EOF
APP_NAME="${APP_NAME:-AI Interview System}"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=${LOG_CHANNEL:-stderr}
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-laravel}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}

SESSION_DRIVER=${SESSION_DRIVER:-file}
SESSION_LIFETIME=120
CACHE_STORE=${CACHE_STORE:-file}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}

GEMINI_API_KEY=${GEMINI_API_KEY:-}
GEMINI_MODEL=${GEMINI_MODEL:-gemini-1.5-flash}
OPENAI_API_KEY=${OPENAI_API_KEY:-}
MAIL_MAILER=${MAIL_MAILER:-log}
EOF
fi

echo "[1/6] Ensuring APP_KEY is set..."
# Only generate if APP_KEY is blank in the .env stub
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --no-interaction --force
else
    echo "      APP_KEY already set via environment — skipping generate."
fi

# Ensure storage directories exist and are fully writable before artisan commands
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

echo "[2/6] Clearing all caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

echo "[3/6] Running database migrations..."
php artisan migrate --force --no-interaction

echo "[4/6] Seeding roles & permissions (safe to re-run)..."
php artisan db:seed --class=RolePermissionSeeder --force --no-interaction || true

echo "[5/6] Caching config / routes / views for performance..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[6/6] Creating storage symlink..."
php artisan storage:link --force || true

# Set correct permissions for storage after symlink
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads 2>/dev/null || true
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads 2>/dev/null || true

echo ""
echo "✅ Deploy complete. Starting Nginx + PHP-FPM via Supervisor..."
echo "========================================"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
