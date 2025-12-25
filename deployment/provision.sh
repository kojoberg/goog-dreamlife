#!/bin/bash
set -e

# Variables
DB_PASS="L5C5L76a!" 
# Note: In a real scenario, use a different DB password than the root login, but for simplicity/speed we use the provided one.
DOMAIN="162.218.115.17" # Using IP as domain for now

echo "--- Updating System ---"
export DEBIAN_FRONTEND=noninteractive
apt-get update && apt-get upgrade -y
apt-get install -y software-properties-common

echo "--- Adding PHP 8.2 Repo ---"
add-apt-repository ppa:ondrej/php -y
apt-get update

echo "--- Installing Stack ---"
apt-get install -y nginx mysql-server zip unzip acl \
    php8.2-fpm php8.2-mysql php8.2-common php8.2-cli php8.2-xml php8.2-curl \
    php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-gd php8.2-intl

# Install Composer
if ! command -v composer &> /dev/null; then
    echo "--- Installing Composer ---"
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

echo "--- Configuring MySQL ---"
# Check if DB exists, if not create
mysql -e "CREATE DATABASE IF NOT EXISTS dreamlife_pms;"
# Create user if not exists (using the provided password)
mysql -e "CREATE USER IF NOT EXISTS 'dreamuser'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON dreamlife_pms.* TO 'dreamuser'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo "--- Configuring Nginx ---"
# We will copy the config from the synchronized folder
cp /var/www/dreamlife/deployment/nginx.conf /etc/nginx/sites-available/dreamlife
ln -sf /etc/nginx/sites-available/dreamlife /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test and Restart Nginx
nginx -t && systemctl restart nginx

echo "--- Setting Permissions ---"
chown -R www-data:www-data /var/www/dreamlife/storage
chown -R www-data:www-data /var/www/dreamlife/bootstrap/cache
chmod -R 775 /var/www/dreamlife/storage
chmod -R 775 /var/www/dreamlife/bootstrap/cache

echo "--- Running Laravel Setup ---"
cd /var/www/dreamlife

# Install Vendor
export COMPOSER_ALLOW_SUPERUSER=1
composer install --optimize-autoloader --no-dev

# Migrations
php artisan migrate --force --seed || {
    echo "Migration failed, likely due to db connection. Check .env"
}

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "--- Provisioning Complete! ---"
