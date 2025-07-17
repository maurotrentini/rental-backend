# Rental Property Management System - Backend API

A comprehensive Laravel-based REST API for managing short-term rental properties, bookings, guests, and additional services. Built with Laravel 12, MySQL, and Laravel Sanctum for authentication.

## 🏗️ Project Overview

This API provides complete functionality for a rental property management system including:

-   **Property Management** - CRUD operations for rental properties
-   **Booking System** - Reservation management with conflict prevention
-   **Guest Management** - Customer profiles and booking history
-   **Extras System** - Additional services and add-ons
-   **Authentication** - Secure API access with Laravel Sanctum
-   **Email Notifications** - Automated booking confirmations
-   **Conflict Prevention** - Automatic booking overlap detection

## 🛠️ Tech Stack

-   **Framework**: Laravel 12
-   **Database**: MySQL 8.0+
-   **Authentication**: Laravel Sanctum
-   **Email**: Laravel Mail with fallback logging
-   **Validation**: Laravel Form Requests
-   **API Resources**: Laravel API Resources
-   **Testing**: PHPUnit

## 📋 Prerequisites

Before running this application, ensure you have:

-   PHP 8.1 or higher
-   Composer
-   MySQL 8.0 or higher
-   Node.js (for frontend integration)

## 🚀 Installation & Setup

### 1. Clone and Install Dependencies

```bash
# Clone the repository
git clone https://github.com/maurotrentini/rental-backend.git
cd rental-backend

# Install PHP dependencies
composer install
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database Setup

**Create MySQL Database:**

```sql
mysql -u root -p
CREATE DATABASE rental_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel'@'localhost' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON rental_db.* TO 'laravel'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**Configure `.env` file:**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rental_db
DB_USERNAME=laravel
DB_PASSWORD=secret
```

### 4. Run Migrations and Seeders

```bash
# Run database migrations
php artisan migrate

# Seed with sample data (optional)
php artisan db:seed
```

### 5. Configure Sanctum

```bash
# Publish Sanctum configuration
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Run Sanctum migrations (if not already included)
php artisan migrate
```

### 6. Start the Development Server

```bash
# Start Laravel development server
php artisan serve

# API will be available at: http://localhost:8000
```

## 🔐 Authentication

The API uses Laravel Sanctum for authentication. All protected endpoints require a Bearer token.

### Getting Started:

1. **Register a user:**

    ```bash
    POST /api/register
    {
      "name": "Admin User",
      "email": "admin@rental.com",
      "password": "password",
      "password_confirmation": "password"
    }
    ```

2. **Login to get token:**

    ```bash
    POST /api/login
    {
      "email": "admin@rental.com",
      "password": "password"
    }
    ```

3. **Use token in subsequent requests:**
    ```bash
    Authorization: Bearer {your-token-here}
    ```

## 📚 API Endpoints

### Authentication

-   `POST /api/register` - Register new user
-   `POST /api/login` - User login
-   `POST /api/logout` - User logout (requires auth)

### Properties

-   `GET /api/properties` - List all properties
-   `POST /api/properties` - Create new property
-   `GET /api/properties/{id}` - Get property details
-   `PUT /api/properties/{id}` - Update property
-   `DELETE /api/properties/{id}` - Delete property
-   `GET /api/properties/{id}/availability` - Check availability

### Bookings

-   `GET /api/bookings` - List all bookings
-   `POST /api/bookings` - Create new booking
-   `GET /api/bookings/{id}` - Get booking details
-   `PUT /api/bookings/{id}` - Update booking
-   `DELETE /api/bookings/{id}` - Cancel booking
-   `GET /api/bookings-calendar` - Get calendar events

### Guests

-   `GET /api/guests` - List all guests
-   `POST /api/guests` - Create new guest
-   `GET /api/guests/{id}` - Get guest details
-   `PUT /api/guests/{id}` - Update guest
-   `DELETE /api/guests/{id}` - Delete guest
-   `GET /api/guests/{id}/booking-history` - Get booking history

### Extras

-   `GET /api/extras` - List all extras
-   `POST /api/extras` - Create new extra
-   `GET /api/extras/{id}` - Get extra details
-   `PUT /api/extras/{id}` - Update extra
-   `DELETE /api/extras/{id}` - Delete extra

## 🗄️ Database Schema

### Core Tables:

-   **properties** - Rental property information
-   **guests** - Customer profiles
-   **extras** - Additional services/add-ons
-   **bookings** - Reservation records
-   **booking_extras** - Many-to-many relationship for booking extras

### Key Relationships:

-   Property → Bookings (One-to-Many)
-   Guest → Bookings (One-to-Many)
-   Booking ↔ Extras (Many-to-Many)

## 🔧 Configuration

### Email Configuration

```env
MAIL_MAILER=log  # For development (logs to storage/logs)
# For production, configure SMTP:
# MAIL_MAILER=smtp
# MAIL_HOST=your-smtp-host
# MAIL_PORT=587
# MAIL_USERNAME=your-email
# MAIL_PASSWORD=your-password
```

### Queue Configuration (Optional)

```env
QUEUE_CONNECTION=database  # For background jobs
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## 📊 Sample Data

The seeder creates:

-   **Admin user**: `admin@rental.com` / `password`
-   **3 sample properties** with different price points
-   **3 sample guests** with contact information
-   **5 sample extras** (Airport pickup, Late checkout, etc.)
-   **3 sample bookings** with extras attached

## 🚀 Production Deployment

### Environment Setup:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use production database credentials
DB_HOST=your-production-host
DB_DATABASE=your-production-db
DB_USERNAME=your-production-user
DB_PASSWORD=your-secure-password

# Configure production mail
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
```

### Deployment Commands:

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Seed production data (optional)
php artisan db:seed --class=ProductionSeeder
```

## 🔍 Key Features

### Booking Conflict Prevention

The system automatically prevents double-bookings by checking for date overlaps before creating new reservations.

### Automatic Price Calculation

Total booking price is calculated automatically based on:

-   Property price per night × number of nights
-   Selected extras × quantities
-   Real-time price updates

### Email Notifications

Automated email confirmations are sent for:

-   New bookings
-   Booking modifications
-   Cancellations

### Advanced Filtering

All list endpoints support filtering:

-   Properties: by name, status, price range
-   Bookings: by status, dates, property, guest
-   Guests: by name, email, phone

## 🐛 Troubleshooting

### Common Issues:

**Database Connection Error:**

```bash
# Check MySQL service
sudo service mysql status

# Verify credentials in .env file
# Test connection manually
mysql -u laravel -p rental_db
```

**Migration Errors:**

```bash
# Reset migrations (WARNING: destroys data)
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status
```

**Permission Issues:**

```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 📞 Support

For issues or questions:

1. Check the logs: `storage/logs/laravel.log`
2. Review the API documentation
3. Run tests to verify functionality
4. Check database connections and permissions

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

---

**Happy coding! 🚀**
