FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev zip libonig-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip bcmath gd

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copia TUDO primeiro
COPY . .

# Cria .env antes do composer
RUN cp .env.example .env || true
RUN php artisan key:generate || true

# Agora instala dependências
RUN composer install --no-dev --optimize-autoloader

# Build frontend
RUN npm install && npm run build

# Permissões
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 10000

CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "10000"]