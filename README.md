# BonBon Ecom

Laravel 12 e-commerce project with:
- storefront pages
- admin management pages
- API v1 routes
- Google OAuth login

## Requirements

- PHP `8.2+`
- Composer
- Node.js `18+` and npm
- MySQL or MariaDB

## Quick Start (Manual)

1. Clone and enter the project
```bash
git clone <repo-url>
cd bonbon-ecom
```

2. Install dependencies
```bash
composer install
npm install
```

3. Create `.env` and app key
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=bonbon_ecom_dev
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations + seed demo data
```bash
php artisan migrate:fresh --seed
```

6. Run app and Vite
```bash
php artisan serve
npm run dev
```

## One-Command Setup

Use either command:

```bash
composer run setup:local
```

or

```bash
npm run setup
```

Fresh reset + seed (if needed):
```bash
php artisan migrate:fresh --seed
```

## Local Accounts (Seeded)

- Admin: `admin@example.com` / `12345678`
- User: `test@example.com` / `12345678`
- User: `customer@example.com` / `12345678`

## Google OAuth Setup

Set in `.env`:
```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

In Google Cloud Console, add this exact redirect URI:
- `http://127.0.0.1:8000/auth/google/callback`

Important: URI must match exactly (host, port, scheme, and path).

## Current Routes

To view full current routes:
```bash
php artisan route:list --except-vendor
```

### Web Storefront
- `GET /`
- `GET /products`
- `GET /product/{slug}`
- `GET /cart`
- `POST /cart/add`
- `PUT /cart/{item}`
- `DELETE /cart/{item}`
- `POST /cart/{item}/increment`
- `POST /cart/{item}/decrement`
- `POST /cart/clear`
- `GET /checkout`
- `GET /customize`
- `GET /assistant`

### Auth + Profile
- `GET /login`
- `POST /login`
- `GET /register`
- `POST /register`
- `POST /logout`
- `GET /auth/google`
- `GET /auth/google/callback`
- `GET /profile`
- `POST /profile`
- `POST /profile/address`
- `PUT /profile/address/{address}`
- `DELETE /profile/address/{address}`
- `POST /profile/payment-method`
- `PUT /profile/payment-method/{paymentMethod}`
- `DELETE /profile/payment-method/{paymentMethod}`
- `POST /profile/order/{order}/reorder`

### Admin
- `GET /admin`
- `POST /admin/settings`
- Resource routes:
  - `/admin/categories`
  - `/admin/products`
- Extra admin routes:
  - `GET /admin/categories-data`
  - `DELETE /admin/products/images/{image}`
  - `POST /admin/products/images/order`

### API v1
- `GET /api/v1/products`
- `GET /api/v1/products/{slug}`
- `GET /api/v1/categories`
- `GET /api/v1/categories/{slug}`
- `GET /api/v1/cart`
- `POST /api/v1/cart/add`
- `PUT /api/v1/cart/update/{item}`
- `DELETE /api/v1/cart/remove/{item}`
- `DELETE /api/v1/cart/clear`
- `GET /api/v1/orders`
- `GET /api/v1/orders/{order}`
- `POST /api/v1/checkout`
- `PUT /api/v1/orders/{order}/cancel`

## Useful Commands

```bash
php artisan test
php artisan migrate
php artisan migrate:fresh --seed
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Notes for Developers

- Logout is `POST /logout` (not GET).
- Demo data is created by:
  - `StoreSettingsSeeder`
  - `CatalogSeeder`
  - `CustomerFlowSeeder`
- If schema changes and data behaves oddly, run:
```bash
php artisan migrate:fresh --seed
```


### Admin Routes
- `GET /admin/invoices/{invoice}/print` - Display invoice in browser
- `POST /admin/invoices/{invoice}/track-print` - Record print event
- `GET /admin/invoices/{invoice}/download` - Download invoice file

### API Routes (Authenticated Users)
- `GET /api/v1/invoices/{invoice}/download` - Download own invoice

### Database
- `invoices` table tracks PDF paths and print counts
- Foreign key relationship with `orders` table
- Automatic cascade delete when order is deleted

