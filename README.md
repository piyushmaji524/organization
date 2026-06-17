# Sarak Youth Development Council — Website

Full-stack, trilingual (EN / HI / BN) website + admin panel.  
Built with PHP + MySQL + vanilla HTML/CSS/JS.

---

## Folder Structure

```
sarak-youth/
├── database/
│   ├── schema.sql          # Full DB schema (run first)
│   └── seed.sql            # Initial data + super admin
├── config/
│   ├── db.php              # PDO connection
│   ├── auth.php            # RBAC middleware
│   └── mailer.php          # PHPMailer helpers
├── api/                    # JSON REST endpoints
│   ├── settings.php
│   ├── members.php
│   ├── events.php
│   ├── rsvp.php
│   ├── gallery.php
│   ├── news.php
│   ├── contact.php
│   └── apply.php
├── admin/                  # Admin panel (PHP)
│   ├── login.php / logout.php / 403.php
│   ├── index.php           # Dashboard
│   ├── members.php / events.php / news.php / gallery.php
│   ├── messages.php / applications.php / rsvp.php
│   ├── donate.php / settings.php / content.php
│   ├── role-permissions.php / admin-users.php
│   ├── includes/
│   │   ├── header.php
│   │   └── footer.php
│   └── assets/
│       ├── css/admin.css
│       └── js/admin.js
├── assets/
│   ├── css/style.css       # Public frontend CSS
│   └── js/
│       ├── api.js          # API fetch helpers
│       ├── i18n.js         # Language system
│       ├── main.js         # Shared utilities
│       └── pages/home.js   # Home page loader
├── lang/
│   ├── en.json
│   ├── hi.json
│   └── bn.json
├── uploads/                # User uploads (auto-created)
├── index.html              # Home
├── about.html
├── committee.html
├── events.html
├── news.html
├── gallery.html
├── join.html
├── donate.html
├── contact.html
├── .htaccess
├── composer.json
└── README.md
```

---

## Quick Start

### Development Setup
Complete setup instructions available in [SETUP.md](SETUP.md).

**Quick steps:**
```bash
# 1. Install dependencies
composer install

# 2. Copy environment template
cp .env.example .env

# 3. Update .env with your database & SMTP credentials
nano .env

# 4. Import database schema
mysql -u root -p sarak_youth < database/schema.sql
mysql -u root -p sarak_youth < database/seed.sql

# 5. Access admin panel
# http://localhost/admin/login.php
```

### ⚠️ Security First
- **NEVER commit `.env` file** - Use `.env.example` only
- Change default admin credentials immediately after login
- Update database password in production
- Use strong SMTP credentials
- Enable HTTPS on production servers

### Admin Panel
```
URL: /admin/login.php
Default credentials: See database/seed.sql
```

---

## Tech Stack
- **Backend:** PHP 8+ (PDO, bcrypt, sessions)
- **Database:** MySQL 8+ / MariaDB 10.6+
- **Frontend:** Vanilla HTML5 + CSS3 + JavaScript (ES2020)
- **Email:** PHPMailer 6.x (SMTP)
- **Package Manager:** Composer
- **Fonts:** Playfair Display + Mukta (Google Fonts)

---

## Security Features
- ✅ PDO prepared statements (no SQL injection)
- ✅ bcrypt password hashing
- ✅ RBAC (9 roles, per-section permissions)
- ✅ Session timeout (2 hours)
- ✅ File upload validation (type + size)
- ✅ Environment-based configuration (.env)
- ✅ `.htaccess` blocks access to `config/`, `database/`, `vendor/`
- ✅ Security headers (X-Frame-Options, XSS-Protection, etc.)
- ✅ Prepared statement for all database queries

---

## Documentation
- [Setup Instructions](SETUP.md) - Local development & deployment
- [Contributing Guide](CONTRIBUTING.md) - How to contribute
- [Security Policy](SECURITY.md) - Reporting vulnerabilities
- [License](LICENSE) - MIT License

---

## Features
### Frontend
- 🌍 Multilingual support (English, Hindi, Bengali)
- 📱 Responsive design
- 🎨 Modern UI with custom fonts
- ⚡ Fast vanilla JavaScript (no jQuery)

### Admin Panel
- 👥 User & role management
- 📰 News/Blog management
- 🎉 Event management & RSVP tracking
- 🖼️ Gallery management
- 📧 Message inbox
- 📊 Member directory
- 💰 Donation tracking
- ⚙️ Site settings & configuration
- 🔐 Role-based access control

---

## Support
For issues, feature requests, or security concerns:
1. Check [Existing Issues](https://github.com/gunayatan/sarak-youth/issues)
2. Review [Security Policy](SECURITY.md) for security issues
3. Submit new issue with clear description

---

## License
MIT License - See [LICENSE](LICENSE) file for details

© 2024-2026 Sarak Youth Development Council
