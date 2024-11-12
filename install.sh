#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting Casino Platform Installation...${NC}"

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo -e "${RED}Composer is not installed. Please install composer first.${NC}"
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo -e "${RED}NPM is not installed. Please install npm first.${NC}"
    exit 1
fi

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}Creating .env file from .env.example...${NC}"
    cp .env.example .env
    if [ $? -ne 0 ]; then
        echo -e "${RED}Failed to create .env file${NC}"
        exit 1
    fi
fi

# Install PHP dependencies
echo -e "${YELLOW}Installing PHP dependencies...${NC}"
composer install
if [ $? -ne 0 ]; then
    echo -e "${RED}Failed to install PHP dependencies${NC}"
    exit 1
fi

# Install Node.js dependencies
echo -e "${YELLOW}Installing Node.js dependencies...${NC}"
npm install
if [ $? -ne 0 ]; then
    echo -e "${RED}Failed to install Node.js dependencies${NC}"
    exit 1
fi

# Generate application key
echo -e "${YELLOW}Generating application key...${NC}"
php artisan key:generate
if [ $? -ne 0 ]; then
    echo -e "${RED}Failed to generate application key${NC}"
    exit 1
fi

# Run database migrations
echo -e "${YELLOW}Running database migrations...${NC}"
php artisan migrate
if [ $? -ne 0 ]; then
    echo -e "${RED}Failed to run database migrations${NC}"
    exit 1
fi

# Build frontend assets
echo -e "${YELLOW}Building frontend assets...${NC}"
npm run build
if [ $? -ne 0 ]; then
    echo -e "${RED}Failed to build frontend assets${NC}"
    exit 1
fi

# Create storage link
echo -e "${YELLOW}Creating storage link...${NC}"
php artisan storage:link
if [ $? -ne 0 ]; then
    echo -e "${RED}Failed to create storage link${NC}"
    exit 1
fi

# Optimize application
echo -e "${YELLOW}Optimizing application...${NC}"
php artisan optimize
if [ $? -ne 0 ]; then
    echo -e "${RED}Failed to optimize application${NC}"
    exit 1
fi

# Set permissions
echo -e "${YELLOW}Setting file permissions...${NC}"
chmod -R 775 storage bootstrap/cache
if [ $? -ne 0 ]; then
    echo -e "${RED}Failed to set file permissions${NC}"
    exit 1
fi

echo -e "${GREEN}Installation completed successfully!${NC}"
echo -e "${YELLOW}Please configure your database settings in the .env file if you haven't already.${NC}"
echo -e "${YELLOW}To create an admin user, run: php artisan casino:create-admin${NC}"
echo -e "${GREEN}You can now start the development server with: php artisan serve${NC}"