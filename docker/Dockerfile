FROM node:24-alpine AS node
FROM php:8.4-fpm-alpine

# Copy Node 24 binaries from the official Node image
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/bin/npm /usr/local/bin/npm
COPY --from=node /usr/local/bin/npx /usr/local/bin/npx
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -sf /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && chmod +x /usr/local/bin/npm /usr/local/bin/npx

# Install bash and curl (required for Safe Chain installation)
RUN apk add --no-cache bash curl

# Install Aikido Safe Chain in CI mode
# The --ci flag creates executable shims instead of shell aliases (required for Docker)
RUN curl -fsSL https://github.com/AikidoSec/safe-chain/releases/download/1.5.12/install-safe-chain.sh | bash -s -- --ci

# Prepend Safe Chain shims to PATH so npm/npx commands go through Safe Chain first
ENV PATH="/root/.safe-chain/shims:/root/.safe-chain/bin:${PATH}"

RUN apk add --no-cache \
    nginx \
    supervisor \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        mbstring \
        intl \
        opcache \
        pcntl \
        exif

RUN mkdir -p /var/log/supervisor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/nginx.conf /etc/app/nginx.conf
COPY docker/php-uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY docker/start-app.sh /usr/local/bin/start-app.sh
RUN chmod +x /usr/local/bin/start-app.sh

EXPOSE 8080
CMD ["/usr/local/bin/start-app.sh"]