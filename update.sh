#!/bin/bash

# UVITECH RxPMS - Quick Update Script
# Run this after pulling changes: sudo ./update.sh

set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}   UVITECH RxPMS - Update Script       ${NC}"
echo -e "${BLUE}========================================${NC}"

if [ "$EUID" -ne 0 ]; then
  echo "Please run as root (sudo ./update.sh)"
  exit 1
fi

WEB_DIR="/var/www/dreamlife"
cd $WEB_DIR

echo -e "${GREEN}[1/5] Pulling latest changes...${NC}"
git pull

echo -e "${GREEN}[2/5] Fixing permissions...${NC}"
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo -e "${GREEN}[3/5] Installing dependencies...${NC}"
export COMPOSER_ALLOW_SUPERUSER=1
composer install --optimize-autoloader --no-dev

echo -e "${GREEN}[4/5] Running migrations...${NC}"
php artisan migrate --force

echo -e "${GREEN}[5/5] Clearing caches...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions again after artisan commands
chown -R www-data:www-data storage bootstrap/cache

echo -e "${BLUE}========================================${NC}"
echo -e "${GREEN}   Update Complete! ✅                 ${NC}"
echo -e "${BLUE}========================================${NC}"
