FROM php:8.2-cli

# Instalar dependências
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar Node
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório
WORKDIR /app

# Copiar projeto
COPY . .

# Instalar dependências
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Porta
EXPOSE 10000

# Rodar Laravel
CMD php artisan serve --host 0.0.0.0 --port 10000