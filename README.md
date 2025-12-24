# 📰 UNB News Portal - United News of Bangladesh

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red?style=for-the-badge&logo=laravel" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.2+-8892BF?style=for-the-badge&logo=php" alt="PHP Version">
  <img src="https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap" alt="Bootstrap Version">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

<p align="center">
  <strong>A comprehensive, feature-rich News Portal for United News of Bangladesh built with Laravel 12.x</strong>
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#tech-stack">Tech Stack</a> •
  <a href="#installation">Installation</a> •
  <a href="#contributing">Contributing</a>
</p>

---

## 🌟 Overview

**UNB News Portal** is a robust, scalable news publishing platform designed for United News of Bangladesh. This system provides comprehensive content management, subscription-based access control, advanced analytics, and a complete administrative interface for managing news, media, and user subscriptions.

### ✨ Key Highlights

- 🏗️ **Modern Architecture**: Built with Laravel 12.x following MVC patterns
- 💳 **Subscription System**: Multi-tier subscription packages with access control
- 📊 **Advanced Analytics**: Visitor tracking, page views, and detailed reporting
- 📝 **Activity Logging**: Complete audit trail for admin and user activities
- 🎨 **Media Management**: Comprehensive media library with image and video galleries
- 🌍 **Multi-Language**: Full support for Bangla and English content
- 🔐 **Role-Based Access**: Granular permissions system using Spatie Permission
- 🎫 **Support Tickets**: Complete customer support ticket management system

---

## 🚀 Features

### 🔐 User & Access Management

- ✅ Multi-role system (Super Admin, Admin, Editor, etc.)
- ✅ Role-based permissions with granular access control
- ✅ Admin authentication and profile management
- ✅ Password reset functionality
- ✅ User subscription management

### 💳 Subscription System

- ✅ Multiple subscription packages (Lite, Pro, Ultra)
- ✅ Access control for news, images, videos, and exclusive content
- ✅ Language-based access (Bangla, English)
- ✅ Subscription lifecycle management
- ✅ User subscription tracking and approval
- ✅ Ad-free and priority support features

### 📝 Content Management

- ✅ Rich Text Editor (Summernote) for news publishing
- ✅ Category & Tag Management
- ✅ Breaking news & featured articles
- ✅ News sorting and organization
- ✅ Pending news approval workflow
- ✅ SEO-friendly URLs and meta tags
- ✅ News export (PDF, Excel, CSV, XML, JSON, TXT)
- ✅ Archive system for deleted news

### 🎨 Media Management

- ✅ **Media Library**: Centralized media management
  - Image, video, audio, and document support
  - Metadata management (title, alt text, caption, description)
  - File type filtering and search
- ✅ **Image Gallery**: Create and manage image galleries
  - Group images by gallery slug
  - Exclusive content support
- ✅ **Video Gallery**: Manage video content
  - Support for media library videos
  - External video URLs (YouTube, Vimeo, Facebook, etc.)
- ✅ Watermark settings for images

### 📊 Analytics & Tracking

- ✅ Real-time visitor analytics
- ✅ Page view tracking
- ✅ Country-wise analytics
- ✅ Organic traffic analysis
- ✅ Repeater visitor tracking
- ✅ Most visited pages and IPs
- ✅ Bot activity detection
- ✅ IP blocking functionality
- ✅ Analytics export capabilities

### 📋 Activity Logs

- ✅ Complete activity logging system
- ✅ Admin activity tracking (create, update, delete)
- ✅ User activity tracking (view, comment, export)
- ✅ Activity restoration for deleted items
- ✅ Top viewed and exported news analytics
- ✅ Filter by date, user type, action, and model

### 🎫 Support Ticket System

- ✅ Complete ticket management
- ✅ Ticket categories and tags
- ✅ Assignment to admins
- ✅ Reply system with attachments
- ✅ Internal notes
- ✅ SLA tracking and logging
- ✅ Status management (Open, In Progress, Resolved, Closed)
- ✅ Priority levels (Low, Medium, High, Urgent)

### 🌐 Frontend Features

- ✅ Subscription-based content access
- ✅ Advanced search & filter by category
- ✅ Comment system with moderation
- ✅ Social media sharing integration
- ✅ Newsletter subscription
- ✅ Responsive and mobile-friendly design
- ✅ Multi-language support (Bangla/English)

### 🛠 Administration

- ✅ Comprehensive admin dashboard
- ✅ Analytics dashboard with statistics
- ✅ Activity logs management
- ✅ Role and permission management
- ✅ Site settings and configuration
- ✅ Footer management (3 grid sections)
- ✅ Social links and counts
- ✅ Advertisement management
- ✅ Home section settings
- ✅ Localization management

### 📧 Communication

- ✅ Contact form and message management
- ✅ Newsletter subscribers
- ✅ Email notifications
- ✅ Support ticket email notifications

---

## 🛠 Tech Stack

### 🏗️ Core Technologies

| Layer | Technology | Purpose |
|-------|------------|---------|
| **Backend** | Laravel 12.x | Main framework |
| **Frontend** | Blade, Bootstrap 5 | Template engine & styling |
| **Database** | MySQL | Data persistence |
| **Authentication** | Laravel Sanctum | API authentication |
| **Permissions** | Spatie Permission | Role-based access control |

### 📦 Key Packages

| Package | Purpose |
|---------|---------|
| **barryvdh/laravel-dompdf** | PDF generation for news export |
| **maatwebsite/excel** | Excel export functionality |
| **intervention/image** | Image processing and manipulation |
| **spatie/laravel-permission** | Role and permission management |
| **guzzlehttp/guzzle** | HTTP client for API integrations |

### 🎨 Frontend Libraries

| Component | Technology | Description |
|-----------|------------|-------------|
| **UI Framework** | Bootstrap 5 | Responsive design |
| **Rich Editor** | Summernote | Content editing |
| **Icons** | Font Awesome | Icon library |
| **Build Tool** | Vite | Asset bundling |

---

## ⚙️ Installation

### 📋 Prerequisites

- PHP >= 8.2
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- Node.js 18+ & NPM
- Git

### 🚀 Quick Setup

```bash
# Clone the repository
git clone https://github.com/animeshkarmitun/unbwire.git
cd unbwire

# Install PHP dependencies
composer install

# Install Node dependencies
npm install && npm run build

# Copy and configure environment file
cp .env.example .env
php artisan key:generate

# Configure your .env file with:
# - Database credentials
# - Mail settings
# - App URL

# Run migrations and seed the database
php artisan migrate --seed

# Create symbolic link for storage
php artisan storage:link

# Set up permissions (optional, for production)
php artisan permissions:update-all

# Start the development server
php artisan serve
```

### 🌐 Access Points

- **Frontend**: `http://127.0.0.1:8000`
- **Admin Panel**: `http://127.0.0.1:8000/admin`
- **Default Admin**: Check `database/seeders/DatabaseSeeder.php` for credentials

### 🔑 Default Credentials

After seeding, check the `DatabaseSeeder.php` file for default admin credentials.

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
```

---

## 🧑‍💻 Usage & Development

### 👥 User Roles & Permissions

The system uses Spatie Permission package for role-based access control. Key permission groups include:

- **News**: index, create, update, delete, all-access
- **Category**: index, create, update, delete
- **Subscription Package**: index, create, update, delete
- **Analytics**: index, view, export
- **Activity Log**: index, view, restore, export
- **Support Tickets**: index, view, create, update, assign, delete
- **Media Library**: index, create, update, delete
- **Gallery**: image/video gallery permissions
- And many more...

### 📊 Development Commands

```bash
# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update permissions
php artisan permissions:update-all

# Remove old gallery permissions
php artisan permissions:remove-old-gallery
```

---

## 🚀 Deployment

### 🏭 Production Deployment Steps

```bash
# Install production dependencies
composer install --optimize-autoloader --no-dev

# Run database migrations
php artisan migrate --force

# Cache configurations for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize

# Set up storage link
php artisan storage:link

# Set proper permissions
chmod -R 755 storage bootstrap/cache
```

### ☁️ Supported Platforms

- Laravel Forge
- VPS / Cloud providers (DigitalOcean, AWS, Linode)
- Shared hosting (with proper configuration)

---

## 📁 Project Structure

```
unbwire/
├── app/
│   ├── Console/Commands/     # Artisan commands
│   ├── Http/Controllers/     # Application controllers
│   │   ├── Admin/            # Admin panel controllers
│   │   └── Frontend/         # Frontend controllers
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic services
│   └── Traits/               # Reusable traits
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── views/
│   │   ├── admin/           # Admin panel views
│   │   └── frontend/        # Frontend views
│   └── js/                  # JavaScript assets
├── routes/
│   ├── admin.php            # Admin routes
│   ├── web.php              # Web routes
│   └── api.php              # API routes
└── public/                  # Public assets
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

### 📝 How to Contribute

1. **Fork** the repository
2. Create a new branch: `git checkout -b feature/your-feature`
3. **Commit** your changes: `git commit -m 'Add some feature'`
4. **Push** to the branch: `git push origin feature/your-feature`
5. Open a **Pull Request**

### 📋 Contribution Guidelines

- Follow PSR-12 coding standards
- Write meaningful commit messages
- Include tests for new features
- Update documentation as needed
- Ensure all tests pass before submitting

### 🐛 Reporting Issues

For major changes, please open an issue first to discuss your ideas. Include:
- Clear description of the problem
- Steps to reproduce
- Expected vs actual behavior
- Environment details

---

## 📊 Key Features Breakdown

### Subscription Packages

- **Access Control**: News, Images, Videos, Exclusive content
- **Language Access**: Bangla and English content permissions
- **Features**: Ad-free, priority support, article limits
- **Billing**: Monthly and yearly options

### Analytics System

- **Visitor Tracking**: IP, location, device, browser
- **Page Views**: Detailed page view analytics
- **Reports**: Date-wise, country-wise, organic traffic
- **Security**: Bot detection and IP blocking

### Activity Logs

- **Admin Activities**: Create, update, delete operations
- **User Activities**: View, comment, export actions
- **Top Analytics**: Most viewed and exported news
- **Restoration**: Restore deleted items from logs

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---

## ❤️ Acknowledgements

- [Laravel Team](https://laravel.com) for the amazing framework
- [Bootstrap](https://getbootstrap.com) for the responsive design components
- [Spatie](https://spatie.be) for the permission package
- All contributors who have helped shape this project

---

<p align="center">
  <strong>Made with ❤️ for United News of Bangladesh</strong>
</p>

<p align="center">
  <a href="https://github.com/animeshkarmitun/unbwire/issues">Report Bug</a> •
  <a href="https://github.com/animeshkarmitun/unbwire/issues">Request Feature</a>
</p>
