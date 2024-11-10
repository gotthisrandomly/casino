# SDSlotman Casino Platform

A modern, secure, and feature-rich casino gaming platform built with Laravel and Vue.js.

## Features

- 🎰 Multiple slot games
- 💳 Secure payment processing
- 🔐 Advanced security features
- 📱 Mobile-responsive design
- 🎮 Real-time gaming experience
- 📊 Comprehensive admin dashboard
- 🔄 Automated backup system
- 📈 Detailed analytics
- 🌐 Multi-language support
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

- Enable Two-Factor Authentication
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
- Two-Factor Authentication
- Session Security
- Data Encryption

### Reporting Issues

Report security vulnerabilities to security@casino.com

## Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## License

This project is licensed under the MIT License. See LICENSE file.

## Support

- Documentation: [docs.casino.com](https://docs.casino.com)
- Issues: GitHub Issues
- Email: support@casino.com

## Authors

- John Doe - Lead Developer
- Jane Smith - Backend Engineer
- Bob Johnson - Frontend Developer