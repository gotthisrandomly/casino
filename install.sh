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

# Database Configuration
echo -e "${YELLOW}Setting up database configuration...${NC}"
read -p "Enter database host (default: localhost): " db_host
db_host=${db_host:-localhost}

read -p "Enter database port (default: 5432): " db_port
db_port=${db_port:-5432}

read -p "Enter database name (default: casino): " db_name
db_name=${db_name:-casino}

read -p "Enter database username: " db_user
while [ -z "$db_user" ]; do
    echo -e "${RED}Database username cannot be empty${NC}"
    read -p "Enter database username: " db_user
done

read -s -p "Enter database password: " db_password
echo
while [ -z "$db_password" ]; do
    echo -e "${RED}Database password cannot be empty${NC}"
    read -s -p "Enter database password: " db_password
    echo
done

# Update .env file with database credentials
sed -i "s/DB_HOST=.*/DB_HOST=$db_host/" .env
sed -i "s/DB_PORT=.*/DB_PORT=$db_port/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$db_password/" .env

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

# Create Admin User
echo -e "${YELLOW}Creating admin user...${NC}"
read -p "Enter admin name: " admin_name
while [ -z "$admin_name" ]; do
    echo -e "${RED}Admin name cannot be empty${NC}"
    read -p "Enter admin name: " admin_name
done

read -p "Enter admin email: " admin_email
while [ -z "$admin_email" ]; do
    echo -e "${RED}Admin email cannot be empty${NC}"
    read -p "Enter admin email: " admin_email
done

read -s -p "Enter admin password (min 8 characters): " admin_password
echo
while [ ${#admin_password} -lt 8 ]; do
    echo -e "${RED}Password must be at least 8 characters long${NC}"
    read -s -p "Enter admin password (min 8 characters): " admin_password
    echo
done

read -s -p "Confirm admin password: " admin_password_confirm
echo
while [ "$admin_password" != "$admin_password_confirm" ]; do
    echo -e "${RED}Passwords do not match${NC}"
    read -s -p "Enter admin password (min 8 characters): " admin_password
    echo
    read -s -p "Confirm admin password: " admin_password_confirm
    echo
done

# Create admin user using artisan command
php artisan tinker --execute="App\Models\User::create([
    'name' => '$admin_name',
    'email' => '$admin_email',
    'password' => Hash::make('$admin_password'),
    'is_admin' => true,
    'status' => 'active',
    'balance' => 0
]);"

if [ $? -ne 0 ]; then
    echo -e "${RED}Failed to create admin user${NC}"
    exit 1
fi

echo -e "${GREEN}Installation completed successfully!${NC}"
echo -e "${GREEN}Admin user created with email: $admin_email${NC}"
echo -e "${GREEN}You can now start the development server with: php artisan serve${NC}"