#!/bin/bash

# UVITECH RxPMS - One-Click Installer for Ubuntu 22.04
# Usage: sudo ./install.sh

set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}=================================================${NC}"
echo -e "${BLUE}   UVITECH RxPMS - Automated Installer           ${NC}"
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
apt install -y nginx mysql-server composer zip unzip \
    php8.4-fpm php8.4-mysql php8.4-common php8.4-cli php8.4-xml php8.4-curl \
    php8.4-mbstring php8.4-zip php8.4-bcmath php8.4-gd php8.4-intl

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
    #git clone https://github.com/kojoberg/goog-dreamlife.git dreamlife
    git clone -b v3.0-alpha https://github.com/kojoberg/goog-dreamlife.git dreamlife
    cd dreamlife
fi

# Permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
# Backups folder
mkdir -p storage/app/backups
chown -R www-data:www-data storage/app/backups
chmod -R 775 storage/app/backups

echo -e "${GREEN}[5/7] Installing Dependencies & Building Assets...${NC}"

# Setup Env First (Needed for composer scripts to not fail)
if [ ! -f .env ]; then
    cp .env.example .env
    
    # Configure Database - SWITCH TO MYSQL
    sed -i "s/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/" .env
    sed -i "s/# DB_HOST=127.0.0.1/DB_HOST=127.0.0.1/" .env
    sed -i "s/# DB_PORT=3306/DB_PORT=3306/" .env
    sed -i "s/# DB_DATABASE=laravel/DB_DATABASE=${DB_NAME}/" .env
    sed -i "s/# DB_USERNAME=root/DB_USERNAME=${DB_USER}/" .env
    sed -i "s/# DB_PASSWORD=/DB_PASSWORD=${DB_PASS}/" .env
    
    # Configure App
    sed -i "s/APP_URL=http:\/\/localhost/APP_URL=http:\/\/$(curl -s ifconfig.me)/" .env
    sed -i "s/APP_ENV=local/APP_ENV=production/" .env
    sed -i "s/APP_DEBUG=true/APP_DEBUG=false/" .env
fi

# Install PHP Dependencies (Downgrade if needed for PHP 8.2)
export COMPOSER_ALLOW_SUPERUSER=1
composer update --optimize-autoloader --no-dev

# Generate Key now that vendor exists
if [ ! -f .env ]; then
    # Double check if key needs generating
    if grep -q "APP_KEY=" .env; then
         php artisan key:generate
    fi
else
     php artisan key:generate
fi

npm install
npm run build

echo -e "${GREEN}[6/7] Migrating Database...${NC}"
php artisan migrate:fresh --force --seed
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
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
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

echo -e "${GREEN}[8/8] Optional: SSL Configuration (Let's Encrypt)${NC}"
read -p "Do you want to install a free SSL certificate now? (y/n): " INSTALL_SSL
if [[ "$INSTALL_SSL" =~ ^[Yy]$ ]]; then
    read -p "Enter your domain name (e.g., pms.dreamlife.com): " DOMAIN_NAME
    read -p "Enter your email for renewal notices: " EMAIL_ADDR

    if [ -n "$DOMAIN_NAME" ] && [ -n "$EMAIL_ADDR" ]; then
        echo "Installing Certbot..."
        apt install -y certbot python3-certbot-nginx

        # Update Nginx config specifically for this domain before requesting cert
        sed -i "s/server_name _;/server_name $DOMAIN_NAME;/" /etc/nginx/sites-available/dreamlife
        systemctl reload nginx

        echo "Requesting Certificate..."
        certbot --nginx -d "$DOMAIN_NAME" --non-interactive --agree-tos --email "$EMAIL_ADDR" --redirect

        # Ensure auto-renew timer is active
        if systemctl list-to-timer | grep -q 'certbot.timer'; then
            echo "Certbot auto-renewal timer is already active."
        else
            echo "Setting up auto-renewal..."
            # Check if crontab already has renew command to avoid duplicates
            (crontab -l 2>/dev/null; echo "0 12 * * * /usr/bin/certbot renew --quiet") | crontab -
        fi
        
        echo -e "${GREEN}SSL Installed Successfully!${NC}"
        APP_URL="https://$DOMAIN_NAME"
        sed -i "s|APP_URL=http://.*|APP_URL=$APP_URL|" .env
        php artisan config:clear
    else
        echo "Skipping SSL: Domain or Email missing."
    fi
fi

# Start Scheduler
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/dreamlife && php artisan schedule:run >> /dev/null 2>&1") | crontab -

echo -e "${BLUE}=================================================${NC}"
echo -e "${GREEN}   INSTALLATION COMPLETE! 🚀                     ${NC}"
echo -e "${BLUE}=================================================${NC}"
echo -e "Access your app at: http://$(curl -s ifconfig.me)"

