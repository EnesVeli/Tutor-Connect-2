FROM php:8.2-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends curl unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY app/ /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

EXPOSE 8080

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t /var/www/html/public"]
