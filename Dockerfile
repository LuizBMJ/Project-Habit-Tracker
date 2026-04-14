FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev zip libonig-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN cp .env.example .env
RUN php artisan key:generate

RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run build

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 10000

CMD php artisan serve --host 0.0.0.0 --port 10000