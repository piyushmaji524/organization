# Local Development Setup

## Prerequisites
- PHP 7.4+ (8.0+ recommended)
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Web Server (Apache with mod_rewrite or Nginx)

## Installation Steps

### 1. Clone the Repository
```bash
git clone https://github.com/gunayatan/sarak-youth.git
cd sarak-youth
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Copy Environment Configuration
```bash
# Copy the example env file
cp .env.example .env

# Edit with your database credentials
nano .env  # or open in your editor
```

### 4. Create Database
```bash
# Using MySQL CLI
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql

# Or using phpMyAdmin
# 1. Create new database: sarak_youth
# 2. Import database/schema.sql
# 3. Import database/seed.sql
```

### 5. Configure Database Connection
Edit `.env` file with your database credentials:
```env
DB_HOST=localhost
DB_NAME=sarak_youth
DB_USER=your_db_user
DB_PASS=your_db_password
```

### 6. Configure Email (Optional)
Edit `.env` file with your SMTP credentials:
```env
SMTP_HOST=smtp.hostinger.com
SMTP_PORT=993
SMTP_USERNAME=your_email@domain.com
SMTP_PASSWORD=your_app_password
```

### 7. Create Uploads Directory
```bash
mkdir -p uploads
chmod 755 uploads
```

### 8. Set Up Web Server

**Apache:**
```bash
# Ensure .htaccess is in the root directory
# Enable mod_rewrite:
a2enmod rewrite
systemctl restart apache2
```

**Nginx:**
```nginx
server {
    listen 80;
    server_name localhost;
    root /path/to/sarak-youth;
    index index.html index.php;
    
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### 9. Access the Application
- **Frontend:** http://localhost
- **Admin Panel:** http://localhost/admin/login.php
- **Default Credentials:** (Change immediately after login!)
  - Email: admin@sarak.local
  - Password: See admin/seed.sql

## Development Commands

```bash
# Check PHP syntax
php -l admin/index.php

# View error logs
tail -f /var/log/php-errors.log

# Composer update
composer update
```

## Database Schema
- `site_settings` - Global configuration
- `roles` - User roles (Admin, Manager, Moderator)
- `admin_users` - Admin users
- `role_permissions` - RBAC permissions
- `events` - Events management
- `members` - Members directory
- `news` - News/Blog posts
- `gallery` - Gallery images
- `messages` - Contact form submissions
- `applications` - Join applications
- `rsvp` - Event RSVPs
- `donations` - Donation records

## Troubleshooting

### Database Connection Failed
- Check DB credentials in .env
- Verify MySQL is running
- Check database name exists

### SMTP Email Not Sending
- Check SMTP credentials in .env
- Verify firewall allows port 993/587
- Check error logs: error_log('error')

### Admin Login Issues
- Clear browser cookies/cache
- Check admin_users table has records
- Verify password is bcrypt hashed

### Permission Denied on Uploads
```bash
chmod -R 755 uploads/
chmod -R 755 admin/assets/
```

## Security Checklist
- [ ] Change default admin password
- [ ] Update .env with production credentials
- [ ] Set APP_ENV=production
- [ ] Enable HTTPS via .htaccess
- [ ] Set strong database passwords
- [ ] Backup database regularly
- [ ] Update PHP and dependencies

## Additional Resources
- [PHP PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)
- [OWASP Security Guidelines](https://owasp.org/)
- [PDO Security](https://www.php.net/manual/en/pdo.prepared-statements.php)
