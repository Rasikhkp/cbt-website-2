#!/bin/bash
set -e

# Wait for DB to be ready (if needed)
if [ ! -z "$DB_HOST" ]; then
  echo "⏳ Waiting for database at $DB_HOST:$DB_PORT..."
  wait4ports -t 30 tcp://$DB_HOST:$DB_PORT
fi

# Ensure APP_KEY exists
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "null" ]; then
  echo "🔑 Generating new APP_KEY..."
  php artisan key:generate --force
fi

# Run migrations + seed
echo "📦 Running migrations..."
php artisan migrate --seed --force

# Ensure storage link
echo "🔗 Ensuring storage link..."
php artisan storage:link || true

# Finally, run original CMD
exec "$@"
