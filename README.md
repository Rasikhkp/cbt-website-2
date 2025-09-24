# 🎓 Laravel Exam System

A comprehensive online examination system built with Laravel, featuring role-based access control, real-time exam taking with autosave, and automated grading capabilities.

## ✨ Features

- **Role-based Authentication** - Admin, Committee, and Examinee roles with distinct permissions
- **Question Bank Management** - Shared repository for MCQ, Short Answer, and Long Answer questions
- **Flexible Exam Creation** - Fixed or randomized question selection with customizable time limits
- **Real-time Exam Taking** - Auto-save functionality
- **Automated Grading** - Auto-grade MCQ, manual grading for short answers and essays
- **Result Management** - Committee can review, grade, and release results to Examinee

## 🛠 Tech Stack

- **Backend**: Laravel 12+ with MySQL
- **Frontend**: Blade Templates + Tailwind CSS + jQuery
- **Bundling**: Laravel Vite
- **Containerization**: Docker (Production)

## 📋 Prerequisites

- PHP 8.2+
- Composer
- Node.js 16+
- MySQL 8.0+
- Docker (for production)

## 🚀 Development Setup

### 1. Clone the Repository
```bash
git clone https://github.com/Rasikhkp/cbt-website-2.git
cd cbt-website-2
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=exam_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Database Setup
```bash
# Run migrations and seed the database
php artisan migrate --seed
```

### 5. Start Development Servers
```bash
# Terminal 1: Start Laravel development server
php artisan serve

# Terminal 2: Start Vite development server (for hot reloading)
npm run dev
```

Your application will be available at `http://localhost:8000`

## 🐳 Production Deployment (Docker)

### 1. Clone the Repository
```bash
git clone https://github.com/Rasikhkp/cbt-website-2.git
cd cbt-website-2
```

### 2. Setup DB environment
```bash
# Copy environment file
cp .env.example .env.prod

# Configure .env.prod to production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Configure your database in .env.prod, for example:
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=cbt_web
DB_USERNAME=laravel
DB_PASSWORD=secret123
MYSQL_DATABASE=cbt_web
MYSQL_USER=laravel
MYSQL_PASSWORD=secret123
MYSQL_ROOT_PASSWORD=moresecret123
```

### 3. Run Initial Setup (First Time Only)
```bash
# Generate application key
sed -i "s|^APP_KEY=.*|APP_KEY=base64:$(openssl rand -base64 32)|" .env.prod

# Run the container
docker compose up -d
```

## 🔧 Available Commands

### Development Commands
```bash
# Start development environment
php artisan serve          # Start Laravel server
npm run dev                # Start Vite dev server with hot reload
npm run watch              # Watch for file changes

# Database operations
php artisan migrate        # Run migrations
php artisan migrate:fresh  # Fresh migration (drops all tables)
php artisan db:seed        # Run seeders

# Clear caches
php artisan config:clear   # Clear config cache
php artisan route:clear    # Clear route cache
php artisan view:clear     # Clear view cache
```

### Production Commands
```bash
# View logs
docker logs -f cbt_web

# Access application container
docker exec cbt_web sh

# Stop all services
docker compose down

# Update application (pull new code)
git pull
docker compose up -d
```

## 📝 Default Users (After Seeding)

- **Admin**: admin@example.com / password
- **Committee**: committee@example.com / password  
- **Examinee 1**: examinee1@example.com / password
- **Examinee 2**: examinee2@example.com / password
- **Examinee 3**: examinee3@example.com / password
- **Examinee 4**: examinee4@example.com / password
- **Examinee 5**: examinee5@example.com / password
