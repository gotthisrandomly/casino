#!/bin/bash

# Make sure we're in the project directory
cd "$(dirname "$0")"

# Create deployment directory if it doesn't exist
DEPLOY_DIR="/data/data/com.termux/files/home/casino"
mkdir -p "$DEPLOY_DIR"

# Copy files to deployment directory
cp -r public/ resources/ "$DEPLOY_DIR/"

# Set permissions
chmod -R 755 "$DEPLOY_DIR"

# Start PHP server
php -S localhost:8000 -t "$DEPLOY_DIR/public"