FROM dunglas/frankenphp:1-php8.3-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    zip \
    unzip \
    nodejs \
    npm \
    bash \
    wait4ports \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    opcache \
    zip \
    intl \
    soap

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy package.json and package-lock.json (if available)
COPY package*.json ./

# Install Node.js dependencies
RUN npm ci

# Copy application code
COPY . .

# Set permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app \
    && chmod -R 775 /app/storage \
    && chmod -R 775 /app/bootstrap/cache

# Build assets
RUN npm run build

# Clean up dev dependencies after build
RUN npm prune --production

# Generate optimized autoload files
RUN composer dump-autoload --optimize

# Copy Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

# Copy custom entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Configure PHP
RUN echo -e "opcache.enable=1\n\
opcache.enable_cli=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=4000\n\
opcache.revalidate_freq=60\n\
opcache.fast_shutdown=1" > /usr/local/etc/php/conf.d/opcache.ini

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
