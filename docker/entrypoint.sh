#!/bin/bash
set -e

# Run migrations + seed
echo "📦 Running migrations..."
php artisan migrate --seed --force

# Ensure storage link
echo "🔗 Ensuring storage link..."
php artisan storage:link || true

# Finally, run original CMD
exec "$@"
