# Casino Platform

A modern and secure online casino platform built with Laravel and Vue.js.

## Features

- User Authentication and Authorization
- Admin Dashboard
- User Management
- Game Management
- Transaction History
- Real-time Updates
- Secure Payment Processing
- Responsive Design

## Prerequisites

Before you begin, ensure you have the following installed:
- PHP >= 8.1
- Composer
- Node.js >= 16.x
- NPM >= 8.x
- PostgreSQL >= 13.x
- Redis (optional, for better performance)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/gotthisrandomly/casino.git
   cd casino
   ```

2. Run the installation script:
   ```bash
   ./install.sh
   ```

   This script will:
   - Copy .env.example to .env
   - Install PHP dependencies
   - Install Node.js dependencies
   - Generate application key
   - Run database migrations
   - Build frontend assets
   - Create storage link
   - Set proper permissions

3. Configure your database in the .env file:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=casino
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. Create an admin user:
   ```bash
   php artisan casino:create-admin
   ```

5. Start the development server:
   ```bash
   php artisan serve
   ```

6. Start the frontend development server (in a new terminal):
   ```bash
   npm run dev
   ```

The application will be available at `http://localhost:8000`

## Development

- Run tests:
  ```bash
  php artisan test
  ```

- Run code style checks:
  ```bash
  ./vendor/bin/pint
  ```

- Watch for changes and rebuild assets:
  ```bash
  npm run watch
  ```

## Security

This platform implements several security measures:
- CSRF Protection
- XSS Prevention
- SQL Injection Prevention
- Rate Limiting
- Input Validation
- Secure Session Handling

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

The Casino Platform is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, please email support@casino.com or open an issue in the GitHub repository.
##casino gaming platform built with Laravel and Vue.js.

## Features

- 🎰 slot games
- 💳 payment processing
- 🔐 security features
- 📱 Mobile-responsive design
- 🎮 Real-time gaming experience
- 📊 Comprehensive admin dashboard
- 🔄 Automated backup system
- 📈 Detailed analytics
- 💰 Multiple currency support

## Requirements

- PHP 8.1 or higher
- PostgreSQL 13+
- Redis Server
- Node.js 16+
- Composer 2+

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/sdslotman.git
cd sdslotman
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Set up environment file:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=casino_db
DB_USERNAME=casino_user
DB_PASSWORD=your_secure_password
```

6. Run migrations and seeders:
```bash
php artisan migrate --seed
```

7. Build assets:
```bash
npm run build
```

8. Start the application:
```bash
php artisan serve
```

## Configuration

### Casino Settings

Configure casino-specific settings in your `.env`:

```env
CASINO_CURRENCY=USD
CASINO_MIN_DEPOSIT=10
CASINO_MAX_DEPOSIT=10000
CASINO_MIN_WITHDRAWAL=20
CASINO_MAX_WITHDRAWAL=5000
CASINO_DAILY_WITHDRAWAL_LIMIT=20000
```

### Security
- Configure rate limiting
- Set up SSL certificates
- Configure backup settings

## Admin Dashboard

Access the admin dashboard at `/admin` with the following default credentials:
- Email: admin@casino.com
- Password: Change this immediately after first login!

## Development

### Commands

```bash
# Run tests
php artisan test

# Run code style fixes
./vendor/bin/pint

# Generate API documentation
php artisan scribe:generate

# Run development server
php artisan serve

# Watch for changes
npm run dev
```

### Adding New Games

1. Create game configuration:
```bash
php artisan make:game NewGame
```

2. Implement game logic in `app/Games/NewGame/`
3. Add game assets in `resources/games/new-game/`
4. Register game in `config/casino.php`

## Production Deployment

1. Configure production environment
2. Generate production assets
3. Set up SSL certificates
4. Configure caching
5. Set up queue workers
6. Configure backup system

## Security

### Features

- CSRF Protection
- XSS Prevention
- SQL Injection Protection
- Rate Limiting
- Session Security
- Data Encryption

### Reporting Issues

Report security vulnerabilities to gotthisrandomly@github.com

## Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## License

Open Source

## Support

- Documentation: [docs.casino.com](https://github.com/gotthisrandomly/casino/README.md)
- Issues: GitHub Issues
- Email: support: gotthisrandomly@github.com

## Authors
- gotthisrandomly
-based on original design by the creators of GoldenX Casino
