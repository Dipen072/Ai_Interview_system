#!/bin/sh
set -e

echo "========================================"
echo "  AI Interview System - Deploy Script"
echo "========================================"

# Default PORT to 10000 if Render doesn't set it
export PORT="${PORT:-10000}"

echo "[1/6] Generating app key if missing..."
php artisan key:generate --no-interaction --force

echo "[2/6] Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "[3/6] Running database migrations..."
php artisan migrate --force --no-interaction

echo "[4/6] Seeding roles & permissions (if not already seeded)..."
php artisan db:seed --class=RolePermissionSeeder --force --no-interaction || true

echo "[5/6] Caching config / routes / views for performance..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[6/6] Creating storage symlink..."
php artisan storage:link --force || true

echo ""
echo "✅ Deploy complete. Starting services on PORT=$PORT ..."
echo "========================================"

# Start supervisor (which manages php-fpm + nginx)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
