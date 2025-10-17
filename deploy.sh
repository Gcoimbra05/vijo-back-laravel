#!/bin/bash


echo "🚀 Starting Laravel deploy process..."

APP_DIR="/var/www/html/vijo_laravel"
PHP_VERSION="8.1"
USER="www-data"
SERVER_USER="ubuntu"

cd $APP_DIR || exit

REFRESH=false
for arg in "$@"; do
    if [ "$arg" == "--refresh" ] || [ "$arg" == "-r" ]; then
        REFRESH=true
    fi
done

if [ "$REFRESH" = false ]; then
	echo "🔄 Updating code..."
	git pull origin develop || { echo "❌ Failed to execute git pull"; exit 1; }
else
	echo "⚡ Refresh mode: skipping git pull."
fi

echo "🔧 Adjusting permissions..."
sudo chown -R $USER:$USER $APP_DIR
sudo chown -R $USER:$USER $APP_DIR/storage $APP_DIR/bootstrap/cache
sudo chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

echo "🗑️ Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "🧬 Running migrations..."
php artisan migrate --force

echo "🔁 Restarting queue workers..."
php artisan queue:restart

echo "🔄 Restarting supervisor (if needed)..."
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart all

# Set correct file owner (e.g., www-data for Apache/Nginx)
sudo chown -R $SERVER_USER:www-data /var/www/html/vijo_laravel

# Grant write permission to group (www-data) where needed
sudo chmod -R ug+rwX /var/www/html/vijo_laravel

# Ensure vendor folder has proper permissions
sudo chmod -R 775 /var/www/html/vijo_laravel/vendor

# echo "♻️ Restarting PHP-FPM (to clear OpCache)..."
# sudo systemctl restart php$PHP_VERSION-fpm

echo "⚙️ Regenerating Laravel caches..."
sudo -u $SERVER_USER php artisan config:cache
sudo -u $SERVER_USER php artisan route:cache
sudo -u $SERVER_USER php artisan view:cache

#sudo -u $SERVER_USER composer install --no-dev --optimize-autoloader
sudo -u $SERVER_USER composer dump-autoload -o

echo "✅ Deploy finished successfully!"
