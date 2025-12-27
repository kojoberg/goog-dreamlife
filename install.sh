#!/bin/bash

# Dream Life PMS - One-Click Installer for Ubuntu 22.04
# Usage: sudo ./install.sh

set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}=================================================${NC}"
echo -e "${BLUE}   Dream Life PMS - Automated Installer          ${NC}"
echo -e "${BLUE}=================================================${NC}"

if [ "$EUID" -ne 0 ]; then
  echo -e "${RED}Please run as root (sudo ./install.sh)${NC}"
  exit 1
fi

echo -e "${GREEN}[1/7] Updating System...${NC}"
apt update && apt upgrade -y
apt install -y software-properties-common curl git unzip

echo -e "${GREEN}[2/7] Installing Dependencies (PHP, Nginx, MySQL, Node)...${NC}"
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y nginx mysql-server composer \
    php8.2-fpm php8.2-mysql php8.2-common php8.2-cli php8.2-xml php8.2-curl \
    php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-gd php8.2-intl

# Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

echo -e "${GREEN}[3/7] Setting up Database...${NC}"
DB_NAME="dreamlife_pms"
DB_USER="dreamuser"
read -p "Enter a secure password for the database user: " DB_PASS

mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME};"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo -e "${GREEN}[4/7] Deploying Application...${NC}"
WEB_DIR="/var/www/dreamlife"

if [ -d "$WEB_DIR" ]; then
    echo "Directory exists. Pulling latest changes..."
    cd $WEB_DIR
    git pull
else
    echo "Cloning repository..."
    cd /var/www
    git clone https://github.com/kojoberg/goog-dreamlife.git dreamlife
    cd dreamlife
fi

# Permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo -e "${GREEN}[5/7] Installing Dependencies & Building Assets...${NC}"

# Install PHP Dependencies First (Required for artisan)
export COMPOSER_ALLOW_SUPERUSER=1
composer install --optimize-autoloader --no-dev

# Setup Env
if [ ! -f .env ]; then
    cp .env.example .env
    sed -i "s/DB_DATABASE=laravel/DB_DATABASE=${DB_NAME}/" .env
    sed -i "s/DB_USERNAME=root/DB_USERNAME=${DB_USER}/" .env
    sed -i "s/DB_PASSWORD=/DB_PASSWORD=${DB_PASS}/" .env
    sed -i "s/APP_URL=http:\/\/localhost/APP_URL=http:\/\/$(curl -s ifconfig.me)/" .env
    sed -i "s/APP_ENV=local/APP_ENV=production/" .env
    sed -i "s/APP_DEBUG=true/APP_DEBUG=false/" .env
    php artisan key:generate
fi

npm install
npm run build

echo -e "${GREEN}[6/7] Migrating Database...${NC}"
php artisan migrate --force --seed
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo -e "${GREEN}[7/7] Configuring Nginx...${NC}"
cat > /etc/nginx/sites-available/dreamlife <<EOF
server {
    listen 80;
    server_name _;
    root /var/www/dreamlife/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sf /etc/nginx/sites-available/dreamlife /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl restart nginx

# Start Scheduler
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/dreamlife && php artisan schedule:run >> /dev/null 2>&1") | crontab -

echo -e "${BLUE}=================================================${NC}"
echo -e "${GREEN}   INSTALLATION COMPLETE! 🚀                     ${NC}"
echo -e "${BLUE}=================================================${NC}"
echo -e "Access your app at: http://$(curl -s ifconfig.me)"
echo -e "Admin Email: admin@dreamlife.com"
echo -e "Password: password"
