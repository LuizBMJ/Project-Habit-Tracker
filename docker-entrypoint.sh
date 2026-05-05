#!/bin/bash
set -e

# Aguardar o PostgreSQL ficar disponível
echo "Aguardando o banco de dados ficar disponível..."
wait-for-it.sh postgres:5432 --timeout=30 --strict -- echo "Banco de dados está disponível!"

# Gerar APP_KEY se não existir
if ! grep -q "^APP_KEY=" .env 2>/dev/null || [ -z "$(grep '^APP_KEY=' .env 2>/dev/null | cut -d'=' -f2)" ]; then
    echo "Gerando APP_KEY..."
    php artisan key:generate --no-interaction
fi

# Executar migrações
echo "Executando migrações..."
php artisan migrate --force --no-interaction

# Limpar cache
php artisan config:clear
php artisan cache:clear

# Iniciar Laravel e Vite em paralelo
echo "Iniciando aplicação..."
php artisan serve --host=0.0.0.0 --port=10000 &
npm run dev -- --host=0.0.0.0 --port=5173 &

# Aguardar processos
wait
