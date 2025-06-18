#!/bin/bash

echo "🚀 Iniciando processo de deploy Laravel..."

APP_DIR="/var/www/html/vijo_laravel"
PHP_VERSION="8.1"
USER="www-data"

cd $APP_DIR || exit

echo "🔄 Atualizando código..."
git pull origin main || { echo "❌ Falha ao executar git pull"; exit 1; }

echo "🔧 Ajustando permissões..."
sudo chown -R $USER:$USER $APP_DIR
sudo chown -R $USER:$USER $APP_DIR/storage $APP_DIR/bootstrap/cache
sudo chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

echo "🗑️ Limpando caches Laravel..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "⚙️ Regenerando caches Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🧬 Rodando migrations..."
php artisan migrate --force

echo "🔁 Reiniciando queue workers..."
php artisan queue:restart

echo "🔄 Reiniciando supervisor (se necessário)..."
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart all

# Ajustar o dono dos arquivos para o usuário correto (ex: www-data para Apache/Nginx)
sudo chown -R $USER:www-data /var/www/html/vijo_laravel

# Dar permissão de escrita para o grupo (www-data) onde necessário
sudo chmod -R ug+rwX /var/www/html/vijo_laravel

# Garantir que o vendor tenha permissão adequada
sudo chmod -R 775 /var/www/html/vijo_laravel/vendor

# echo "♻️ Reiniciando PHP-FPM (para limpar OpCache)..."
# sudo systemctl restart php$PHP_VERSION-fpm

echo "✅ Deploy finalizado com sucesso!"
