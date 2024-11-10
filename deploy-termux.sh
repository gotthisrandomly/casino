#!/data/data/com.termux/files/usr/bin/bash

# Update package lists
pkg update -y

# Install required packages
pkg install -y php php-fpm nginx mariadb nodejs-lts composer git

# Create working directory
mkdir -p ~/casino
cd ~/casino

# Clone the repository (replace with your actual repository URL)
git clone https://github.com/yourusername/casino.git .

# Install PHP dependencies
composer install --no-dev

# Install Node.js dependencies
npm install
npm run build

# Configure Nginx
echo "server {
    listen 8080;
    root /data/data/com.termux/files/home/casino/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/data/data/com.termux/files/usr/var/run/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}" > $PREFIX/etc/nginx/nginx.conf

# Set up database
mysql -u root -e "CREATE DATABASE casino;"
mysql -u root -e "CREATE USER 'casino'@'localhost' IDENTIFIED BY 'casinopass';"
mysql -u root -e "GRANT ALL PRIVILEGES ON casino.* TO 'casino'@'localhost';"
mysql -u root -e "FLUSH PRIVILEGES;"

# Configure environment
cp .env.example .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=casino/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=casino/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=casinopass/" .env

# Run migrations and seed admin user
php artisan migrate --force
php artisan db:seed --force

# Set up admin user
php artisan tinker --execute="
    \$user = new \App\Models\User();
    \$user->name = 'Admin';
    \$user->email = 'gotthisrandomly';
    \$user->password = bcrypt('admin');
    \$user->is_admin = true;
    \$user->save();
"

# Generate application key
php artisan key:generate

# Create storage link
php artisan storage:link

# Set permissions
chmod -R 755 storage bootstrap/cache

# Start services
php-fpm
nginx

# Start Laravel scheduler in background
nohup php artisan schedule:work > /dev/null 2>&1 &

# Start queue worker in background
nohup php artisan queue:work > /dev/null 2>&1 &

echo "Deployment complete! The application is running at http://localhost:8080"
echo "Admin login credentials:"
echo "Username: gotthisrandomly"
echo "Password: admin"