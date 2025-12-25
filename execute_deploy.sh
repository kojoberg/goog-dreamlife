#!/bin/bash

# Configuration
SERVER_IP="162.218.115.17"
SERVER_USER="root"
PROJECT_DIR="/var/www/dreamlife"

echo "=== Dream Life PMS Deployment ==="
echo "Target: $SERVER_USER@$SERVER_IP"

# 1. Build Assets Locally
echo ">>> Building Assets Locally..."
npm run build

echo ">>> Creating Remote Directory..."
# We use standard ssh. It will ask for the password if not keyed.
ssh $SERVER_USER@$SERVER_IP "mkdir -p $PROJECT_DIR"

echo ">>> Syncing Files (Rsync)..."
# Sync everything except large/unnecessary folder
rsync -avz --exclude 'node_modules' \
           --exclude '.git' \
           --exclude 'tests' \
           --exclude 'storage/*.key' \
           ./ $SERVER_USER@$SERVER_IP:$PROJECT_DIR

echo ">>> Copying Production Env..."
scp deployment/env.production $SERVER_USER@$SERVER_IP:$PROJECT_DIR/.env

echo ">>> Running Provisioning on Server..."
# We run the provision script remotely
ssh $SERVER_USER@$SERVER_IP "chmod +x $PROJECT_DIR/deployment/provision.sh && bash $PROJECT_DIR/deployment/provision.sh"

echo "=== Deployment Complete ==="
echo "Visit http://$SERVER_IP to see your application."
