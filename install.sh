#!/bin/bash

# Casino Project Production Installation Script
# This script installs and configures the Casino project for production environment

# Exit on any error
set -e

# Text colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print status messages
print_message() {
    echo -e "${GREEN}➤ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✖ $1${NC}"
}

# Check if script is run as root
if [ "$EUID" -ne 0 ]; then
    print_error "Please run this script as root or with sudo"
    exit 1
fi

# Capture installation directory
INSTALL_DIR="$PWD"
print_message "Installing in: $INSTALL_DIR"

# Check system requirements
print_message "Checking system requirements..."

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;" 2>/dev/null || echo "0")
if [ "$(printf '%s\n' "8.1.0" "$PHP_VERSION" | sort -V | head -n1)" != "8.1.0" ]; then
    print_error "PHP 8.1 or higher is required. Current version: $PHP_VERSION"
    exit 1
fi

# Check if required PHP extensions are installed
required_extensions=(
    "bcmath"
    "ctype"
    "curl"
    "dom"
    "fileinfo"
    "gd"
    "json"
    "mbstring"
    "openssl"
    "pdo"
    "tokenizer"
    "xml"
    "zip"
)

missing_extensions=()
for ext in "${required_extensions[@]}"; do
    if ! php -m | grep -q "^$ext$"; then
        missing_extensions+=("$ext")
    fi
done

if [ ${#missing_extensions[@]} -ne 0 ]; then
    print_error "Missing PHP extensions: ${missing_extensions[*]}"
    print_message "Installing missing PHP extensions..."
    apt-get update
    for ext in "${missing_extensions[@]}"; do
        apt-get install -y php8.1-"$ext"
    done
fi

# Install system dependencies
print_message "Installing system dependencies..."
apt-get update
apt-get install -y \
    nginx \
    redis-server \
    supervisor \
    git \
    unzip \
    acl

# Install Composer if not already installed
if ! command -v composer &> /dev/null; then
    print_message "Installing Composer..."
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
fi

# Create casino user and set permissions
print_message "Setting up casino user and permissions..."
useradd -m -s /bin/bash casino || true
chown -R casino:casino "$INSTALL_DIR"

# Install project dependencies
print_message "Installing project dependencies..."
sudo -u casino composer install --no-dev --optimize-autoloader

# Set up environment file
print_message "Setting up environment file..."
if [ ! -f ".env" ]; then
    sudo -u casino cp .env.example .env
    sudo -u casino php artisan key:generate
fi

# Update environment variables
print_message "Please provide the following information:"
read -p "Database Host [localhost]: " db_host
db_host=${db_host:-localhost}
read -p "Database Name: " db_name
read -p "Database User: " db_user
read -sp "Database Password: " db_password
echo
read -p "Redis Host [localhost]: " redis_host
redis_host=${redis_host:-localhost}
read -p "Mail Host [smtp.mailtrap.io]: " mail_host
mail_host=${mail_host:-smtp.mailtrap.io}
read -p "Mail Port [2525]: " mail_port
mail_port=${mail_port:-2525}

# Update .env file
sudo -u casino sed -i "s/DB_HOST=.*/DB_HOST=$db_host/" .env
sudo -u casino sed -i "s/DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
sudo -u casino sed -i "s/DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
sudo -u casino sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$db_password/" .env
sudo -u casino sed -i "s/REDIS_HOST=.*/REDIS_HOST=$redis_host/" .env
sudo -u casino sed -i "s/MAIL_HOST=.*/MAIL_HOST=$mail_host/" .env
sudo -u casino sed -i "s/MAIL_PORT=.*/MAIL_PORT=$mail_port/" .env

# Run database migrations
print_message "Running database migrations..."
sudo -u casino php artisan migrate --force

# Clear and optimize application
print_message "Optimizing application..."
sudo -u casino php artisan config:cache
sudo -u casino php artisan route:cache
sudo -u casino php artisan view:cache
sudo -u casino php artisan event:cache

# Set up storage symbolic link
print_message "Setting up storage symbolic link..."
sudo -u casino php artisan storage:link

# Set up Supervisor
print_message "Setting up Supervisor..."
cat > /etc/supervisor/conf.d/casino-worker.conf << EOF
[program:casino-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $INSTALL_DIR/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=casino
numprocs=2
redirect_stderr=true
stdout_logfile=$INSTALL_DIR/storage/logs/worker.log
stopwaitsecs=3600
EOF

# Set up Nginx
print_message "Setting up Nginx..."
cat > /etc/nginx/sites-available/casino << EOF
server {
    listen 80;
    server_name _;
    root $INSTALL_DIR/public;

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
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Enable the site and restart services
ln -sf /etc/nginx/sites-available/casino /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Set correct permissions
print_message "Setting final permissions..."
chown -R casino:casino storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Restart services
print_message "Restarting services..."
systemctl restart php8.1-fpm
systemctl restart nginx
systemctl restart supervisor
supervisorctl reread
supervisorctl update
supervisorctl start casino-worker:*

print_message "Installation completed successfully!"
print_message "Your casino application should now be accessible via your server's IP address or domain name."
print_warning "Don't forget to:"
echo "1. Set up SSL/TLS certificates"
echo "2. Configure your firewall"
echo "3. Set up regular backups"
echo "4. Monitor your logs at $INSTALL_DIR/storage/logs/"