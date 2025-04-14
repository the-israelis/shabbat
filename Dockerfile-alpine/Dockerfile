FROM php:8.2-fpm-alpine

WORKDIR /app

RUN apk add --no-cache \
    libzip-dev \
    oniguruma-dev \
    bash \
    && docker-php-ext-install zip \
    && apk del libzip-dev oniguruma-dev

COPY . .

CMD ["php", "/app/bot.php"]

# HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 CMD curl -f http://localhost || exit 1
