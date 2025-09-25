#!/bin/bash
set -e

echo "📦 Checking if migrations already ran..."
if php artisan migrate:status | grep -q "Yes"; then
    echo "✅ Migrations already applied, skipping migrate --seed"
else
    echo "🚀 Running migrations + seed..."
    php artisan migrate --seed --force
fi

echo "🔗 Ensuring storage link..."
php artisan storage:link || true

exec "$@"

