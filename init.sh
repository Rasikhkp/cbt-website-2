#!/bin/bash

# Simple Laravel setup
set -e

echo "Setting up Laravel application..."

# Generate APP_KEY if not set
sed -i "s|^APP_KEY=.*|APP_KEY=base64:$(openssl rand -base64 32)|" .env.prod

# Start containers
docker compose up -d --build

# Wait for DB and run setup
sleep 10
docker compose exec cbt_web php artisan migrate
docker compose exec cbt_web php artisan storage:link

echo "Setup complete!"
