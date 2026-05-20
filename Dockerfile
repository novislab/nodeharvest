# Setup base image
FROM dunglas/frankenphp:1-php8.5-bookworm

# Setup application root
WORKDIR /var/www/html

# Copy application source
COPY . .

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libuv1-dev \
    curl \
    supervisor \
    ffmpeg \
    && install-php-extensions pdo_pgsql mbstring bcmath pcntl gd exif intl redis zip \
    && pecl install uv-beta \
    && docker-php-ext-enable uv \
    && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --prefer-dist --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader --ignore-platform-reqs \
    && composer clear-cache \
    && composer dump-autoload --optimize \
    && php artisan storage:link \
    && curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm \
    && npm ci \
    && mkdir -p /var/www/html/storage/xdg/{data,config} \
    && chown -R www-data:www-data /var/www/html \
    && cp .docker/Caddyfile /etc/caddy/Caddyfile \
    && cp .docker/php.ini /usr/local/etc/php/conf.d/user-php.ini \
    && cp .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf \
    && cp .docker/run.sh /usr/local/bin/run.sh \
    && chmod +x /usr/local/bin/run.sh

# Switch user
USER www-data

# Set XDG directories
ENV XDG_DATA_HOME=/var/www/html/storage/xdg/data \
    XDG_CONFIG_HOME=/var/www/html/storage/xdg/config

# Expose FrankenPHP
EXPOSE 8000

# Start container
ENTRYPOINT ["/usr/local/bin/run.sh"]