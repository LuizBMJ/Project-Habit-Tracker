FROM php:8.4-cli

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev zip libonig-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    zip \
    bcmath \
    gd

# Instalar Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Diretório da aplicação
WORKDIR /app

# Copiar projeto inteiro
COPY . .

# ⚠️ NÃO usar .env fixo (Render usa ENV variables)
RUN rm -f .env

# Instalar dependências PHP
RUN composer install --no-dev --optimize-autoloader

# Build frontend (Vite)
RUN npm install && npm run build

# Permissões Laravel
RUN chmod -R 777 storage bootstrap/cache

# Limpar cache Laravel (importante pro Render)
RUN php artisan config:clear || true
RUN php artisan cache:clear || true

# Porta do Render
EXPOSE 10000

# Rodar aplicação
CMD php artisan serve --host=0.0.0.0 --port=10000