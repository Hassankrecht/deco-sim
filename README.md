# Ali Krecht Group

A comprehensive Laravel-based multi-purpose website with e-commerce capabilities, project portfolio management, and a robust admin panel. The platform supports multi-language functionality (English, Arabic, Portuguese) and provides a complete solution for managing products, projects, reviews, orders, and content.

## 🌟 Features

### Public Website
- **Multi-language Support**: Full internationalization for English, Arabic, and Portuguese
- **Dynamic Homepage**: Customizable hero section with images, videos, and carousels
- **Project Portfolio**: Showcase projects with categories, images, and detailed descriptions
- **Product Catalog**: E-commerce functionality with categories, product galleries, and detailed views
- **Shopping Cart**: Full cart management with add/remove/update quantity
- **Checkout System**: Complete checkout flow with order processing and invoice generation
- **Coupon System**: Discount code functionality for orders
- **Reviews & Testimonials**: Customer review system with admin moderation
- **Contact Forms**: Contact page with message management
- **Gallery**: Media gallery for showcasing images
- **Services**: Service pages with detailed information
- **Sitemap**: Automatic sitemap.xml generation for SEO

### Admin Panel
- **Dashboard**: Comprehensive admin dashboard with statistics and analytics
- **Project Management**: CRUD operations for projects with image galleries
- **Product Management**: Full product management with categories, images, and components
- **Category Management**: Manage product and project categories with translations
- **Order Management**: View, process, and manage customer orders
- **User Management**: Manage admin users and registered customers
- **Review Moderation**: Approve, reject, or delete customer reviews
- **Coupon Management**: Create and manage discount coupons
- **Income Reports**: Track revenue and generate exportable reports
- **Order Reports**: Detailed order analytics and exports
- **Message Management**: Handle contact form submissions
- **Home Settings**: Configure homepage content, banners, and media
- **App Settings**: Mobile app-specific configuration

### Technical Features
- **Authentication**: Separate authentication for users and admins
- **File Upload**: Robust image and file upload system with Intervention Image
- **PDF Generation**: Invoice generation using DomPDF
- **API Ready**: Laravel Sanctum for API authentication
- **Queue System**: Background job processing
- **Email Notifications**: Configurable email system
- **SEO Friendly**: Sitemap generation and meta tag management
- **Responsive Design**: Mobile-first approach with Bootstrap 5

## 🛠 Tech Stack

### Backend
- **Framework**: Laravel 10.10+
- **PHP**: 8.1+
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Queue**: Database queue driver
- **Cache**: File cache driver (configurable for Redis)

### Frontend
- **Framework**: Vue.js 3.5+
- **UI Library**: Bootstrap 5.2+
- **Build Tool**: Vite 5.0+
- **CSS Preprocessor**: Sass 1.56+
- **Icons**: Popper.js 2.11+
- **HTTP Client**: Axios 1.6+

### Key Packages
- **Image Processing**: Intervention Image 3.11+
- **PDF Generation**: Barryvdh Laravel DomPDF 3.1+
- **API Authentication**: Laravel Sanctum 3.3+
- **Authentication UI**: Laravel UI 4.6+
- **HTTP Client**: Guzzle HTTP 7.2+

## 📋 Requirements

- PHP >= 8.1
- Composer
- Node.js >= 16
- NPM or Yarn
- MySQL >= 5.7
- Git

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/Hassankrecht/ali-krecht-group.git
cd ali-krecht-group
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit the `.env` file and configure your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### 5. Database Setup

```bash
php artisan migrate
php artisan db:seed
```

### 6. Create Storage Link

```bash
php artisan storage:link
```

### 7. Build Assets

```bash
npm run build
```

For development with hot-reload:

```bash
npm run dev
```

### 8. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## ⚙️ Configuration

### Environment Variables

Key environment variables in `.env`:

- `APP_NAME`: Application name
- `APP_ENV`: Environment (local/production)
- `APP_DEBUG`: Debug mode (true/false)
- `APP_URL`: Application URL
- `APP_LOCALE`: Default locale (ar/en/pt)
- `DB_*`: Database configuration
- `MAIL_*`: SMTP configuration for emails
- `AWS_*`: AWS S3 configuration (optional for cloud storage)
- `RECAPTCHA_*`: Google reCAPTCHA keys
- `GOOGLE_CLIENT_IDS`: Google OAuth client IDs
- `FACEBOOK_APP_*`: Facebook app credentials

### Storage Configuration

Media files are stored in `storage/app/public/` with the following structure:
- `home/` - Homepage images and videos
- `projects/` - Project images
- `products/` - Product images
- `reviews/` - Review images

Ensure the `public/storage` symlink exists for public access.

## 📁 Project Structure

```
ali-krecht-group/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin panel controllers
│   │   │   ├── Api/            # API controllers
│   │   │   └── Auth/           # Authentication controllers
│   │   ├── Middleware/         # Custom middleware
│   │   └── Requests/           # Form request validation
│   ├── Models/                 # Eloquent models
│   ├── Services/               # Business logic services
│   ├── Jobs/                   # Queue jobs
│   └── Events/                 # Event definitions
├── config/                     # Configuration files
├── database/
│   ├── migrations/             # Database migrations
│   ├── seeders/               # Database seeders
│   └── factories/             # Model factories
├── public/                    # Public assets
├── resources/
│   ├── views/                 # Blade templates
│   ├── lang/                  # Translation files
│   └── assets/                # Frontend assets
├── routes/
│   ├── web.php                # Web routes
│   ├── api.php                # API routes
│   └── console.php            # Console routes
└── storage/                   # Application storage
```

## 🔐 Authentication

### User Authentication
- Login: `/login`
- Registration: `/register`
- Dashboard: `/dashboard`
- Password Reset: Standard Laravel password reset

### Admin Authentication
- Admin Login: `/admin/login`
- Admin Dashboard: `/admin/dashboard`
- Separate admin user model with dedicated authentication

## 🌍 Multi-language Support

The application supports three languages:
- English (en)
- Arabic (ar) - RTL support
- Portuguese (pt)

Language switching is available via `/lang/{locale}` route. Translation files are located in `resources/lang/{locale}/`.

## 📦 Deployment

### Production Checklist

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Configure production database credentials
3. Set up proper caching: `php artisan config:cache` and `php artisan route:cache`
4. Optimize assets: `npm run build`
5. Set up queue worker: `php artisan queue:work`
6. Configure cron job for Laravel scheduler
7. Set up SSL certificate
8. Configure proper file permissions
9. Set up backup strategy

### Server Requirements

- Web server (Apache/Nginx) with mod_rewrite
- PHP 8.1 or higher with required extensions
- MySQL 5.7 or higher
- Composer
- Node.js and NPM

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License.

## 📞 Support

For support and questions:
- Email: support@alikrechtgroup.com
- Website: https://alikrechtgroup.com

## 🙏 Acknowledgments

- Laravel Framework
- Vue.js
- Bootstrap
- All open-source contributors

---

**Note**: This is a professional Laravel application designed for production use. Ensure proper security measures, regular updates, and backups when deploying to production.
