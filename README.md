# BonBons PH - E-commerce Platform

A modern e-commerce platform built with Laravel for selling custom bonbons and candies.

## Features

- 🛍️ Product catalog with customization options
- 🔐 User authentication with Google OAuth
- 🛒 Shopping cart functionality
- 👤 User profiles and order history
- 📱 Responsive design with Tailwind CSS
- 🎨 Component-based architecture

## Setup Instructions

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and npm
- MySQL database
- XAMPP (for local development)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd bonbon-ecom
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup**
   ```bash
   # Create database in MySQL
   # Update .env with your database credentials
   php artisan migrate
   ```

6. **Google OAuth Setup**

   a. Go to [Google Cloud Console](https://console.cloud.google.com/)
   
   b. Create a new project or select existing one
   
   c. Enable Google+ API
   
   d. Create OAuth 2.0 credentials:
      - Go to "Credentials" → "Create Credentials" → "OAuth 2.0 Client IDs"
      - Set application type to "Web application"
      - Add authorized redirect URIs: `http://localhost/auth/google/callback`
   
   e. Update your `.env` file:
   ```env
   GOOGLE_CLIENT_ID=your_google_client_id_here
   GOOGLE_CLIENT_SECRET=your_google_client_secret_here
   GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
   ```

7. **Build Assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

8. **Start the Development Server**
   ```bash
   php artisan serve
   ```

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Auth/LoginController.php    # Authentication logic
│   └── ...
├── Models/
│   └── User.php                    # User model with OAuth fields
database/
├── migrations/
│   └── ..._add_google_id_and_avatar_to_users_table.php
resources/
├── views/
│   ├── auth/login.blade.php        # Login form
│   ├── components/                 # Reusable components
│   │   ├── navbar.blade.php
│   │   ├── footer.blade.php
│   │   └── button.blade.php
│   └── pages/                      # Page views
routes/
└── web.php                         # Application routes
```

## Authentication Features

- **Email/Password Login**: Traditional authentication
- **Google OAuth**: Social login with Google accounts
- **Session Management**: Secure session handling
- **Logout**: Proper session cleanup

## Development

### Available Commands

```bash
# Run migrations
php artisan migrate

# Create new migration
php artisan make:migration create_table_name

# Run tests
php artisan test

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Code Style

This project uses Laravel Pint for code formatting:

```bash
./vendor/bin/pint
```

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
