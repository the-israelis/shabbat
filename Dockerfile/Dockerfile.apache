FROM php:8.2-apache-alpine

WORKDIR /app

RUN apk add --no-cache \
    libzip-dev \
    oniguruma-dev \
    bash \
    && docker-php-ext-install zip \

COPY . .

EXPOSE 80

HEALTHCHECK CMD curl --fail http://localhost/ || exit 1

RUN addgroup -S appgroup && adduser -S appuser -G appgroup
USER appuser

CMD ["apache2-foreground"]
