# 🌍 Sarak Youth Development Council — Website

<div align="center">

**Empower Youth | Foster Community | Create Impact**

[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-00758F?style=flat&logo=mysql)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat)](LICENSE)
[![GitHub Repo](https://img.shields.io/badge/GitHub-Repository-blue?style=flat&logo=github)](https://github.com/piyushmaji524/organization)

**Full-stack, trilingual (English • हिंदी • বাংলা) website + admin panel**

[🚀 Quick Start](#quick-start) • [📖 Documentation](#documentation) • [🛠️ Features](#features) • [🔒 Security](#security-features)

</div>

---

## 📸 Project Overview

A modern, secure, full-stack web application for **Sarak Youth Development Council** featuring:
- 🌐 **Multilingual Support** - English, Hindi, and Bengali
- 🎯 **Admin Dashboard** - Manage all organization content
- 👥 **Member Directory** - Community engagement
- 📅 **Event Management** - Planning and RSVP tracking
- 💬 **Communication** - Contact forms and messaging
- 🔐 **Secure Authentication** - Role-based access control
- ⚡ **Modern Stack** - PHP 8+, MySQL, RESTful APIs

---

## 🎯 Quick Start

### Prerequisites
- PHP 8.0+
- MySQL 8.0+ or MariaDB 10.6+
- Composer

### 5-Minute Setup
```bash
# 1. Clone repository
git clone https://github.com/piyushmaji524/organization.git
cd organization

# 2. Install dependencies
composer install

# 3. Setup configuration
cp .env.example .env
# Edit .env with your database credentials

# 4. Import database
mysql -u root -p your_db_name < database/schema.sql
mysql -u root -p your_db_name < database/seed.sql

# 5. Start development server
php -S localhost:8000

# 6. Access
# Frontend: http://localhost:8000
# Admin: http://localhost:8000/admin/login.php
```

**For detailed setup instructions, see [SETUP.md](SETUP.md)**

---

## ✨ Features

### 🎨 Frontend
- ✅ Responsive design - Works on all devices
- ✅ **Multilingual Interface** - EN • हिंदी • বাংলা
- ✅ Modern UI with custom fonts
- ✅ Fast vanilla JavaScript (no jQuery)
- ✅ SEO-optimized pages

### 👨‍💼 Admin Panel
| Feature | Details |
|---------|---------|
| 👥 **User Management** | Manage admins, roles, and permissions |
| 📰 **Content Management** | News, blog posts, and announcements |
| 🎉 **Event Management** | Create events, track RSVPs, manage registrations |
| 🖼️ **Gallery** | Upload and organize image galleries |
| 📊 **Member Directory** | View and manage member information |
| 💰 **Donations** | Track donation records |
| 📧 **Messages** | Inbox for contact form submissions |
| 📋 **Applications** | Review join applications |
| ⚙️ **Settings** | Configure site-wide settings |
| 🔑 **RBAC** | 9-tier role-based access control |

### 🔌 API Endpoints
```
/api/members.php     - Member management
/api/events.php      - Event data
/api/news.php        - News/Blog posts
/api/gallery.php     - Gallery images
/api/rsvp.php        - Event RSVPs
/api/contact.php     - Contact form
/api/apply.php       - Join applications
/api/donate.php      - Donation tracking
```

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

---

## 🛠️ Tech Stack

<table>
<tr>
<td><strong>Backend</strong></td>
<td>PHP 8+ with PDO</td>
</tr>
<tr>
<td><strong>Database</strong></td>
<td>MySQL 8+ / MariaDB 10.6+</td>
</tr>
<tr>
<td><strong>Frontend</strong></td>
<td>HTML5 • CSS3 • JavaScript ES2020</td>
</tr>
<tr>
<td><strong>Authentication</strong></td>
<td>bcrypt • Sessions • RBAC</td>
</tr>
<tr>
<td><strong>Email</strong></td>
<td>PHPMailer 6.x (SMTP)</td>
</tr>
<tr>
<td><strong>Package Manager</strong></td>
<td>Composer</td>
</tr>
<tr>
<td><strong>Fonts</strong></td>
<td>Playfair Display • Mukta</td>
</tr>
</table>

---

## 🔒 Security Features

> **Security is our top priority. This project follows OWASP guidelines and best practices.**

| Feature | Details |
|---------|---------|
| 🛡️ **PDO Prepared Statements** | Protection against SQL injection |
| 🔐 **bcrypt Password Hashing** | Industry-standard password encryption |
| 👥 **Role-Based Access Control** | 9 roles with granular permissions |
| ⏱️ **Session Timeout** | Auto-logout after 2 hours |
| 📁 **File Upload Validation** | Type and size restrictions |
| 🔑 **Environment Variables** | Credentials via `.env` (never committed) |
| 🚫 **Access Blocking** | `.htaccess` blocks sensitive directories |
| 📋 **Security Headers** | X-Frame-Options, XSS-Protection, etc. |

**🚨 Vulnerability Report?** See [SECURITY.md](SECURITY.md) for reporting process

---

## 📚 Documentation

| Document | Purpose |
|----------|---------|
| [📖 SETUP.md](SETUP.md) | Complete setup & deployment guide |
| [🤝 CONTRIBUTING.md](CONTRIBUTING.md) | How to contribute to the project |
| [🔐 SECURITY.md](SECURITY.md) | Security policy & vulnerability reporting |
| [✅ GITHUB_CHECKLIST.md](GITHUB_CHECKLIST.md) | Pre-upload verification |
| [📋 LICENSE](LICENSE) | MIT License |

---

## 🚀 Deployment

### Production Checklist
- [ ] Change default admin password
- [ ] Update `.env` with production credentials
- [ ] Enable HTTPS (uncomment in `.htaccess`)
- [ ] Set strong database password
- [ ] Use app-specific SMTP password
- [ ] Enable database backups
- [ ] Set appropriate file permissions
- [ ] Test all functionality

**See [SETUP.md](SETUP.md) for detailed deployment guide**

---

## 🤝 Contributing

We welcome contributions! Here's how:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/awesome-feature`)
3. **Commit** your changes (`git commit -m 'Add awesome feature'`)
4. **Push** to the branch (`git push origin feature/awesome-feature`)
5. **Open** a Pull Request

**See [CONTRIBUTING.md](CONTRIBUTING.md) for detailed guidelines**

---

## 💡 Project Statistics

```
📁 Total Files:     100+ source files
🐘 PHP Files:       25+ endpoints
📊 Database Tables: 13
👥 User Roles:      9
🌍 Languages:       3 (EN/HI/BN)
📄 Pages:           15+ admin pages
🔌 API Endpoints:   8+
```

---

## 🎓 Learn More

- **PHP Security:** [php.net/security](https://www.php.net/manual/en/security.php)
- **OWASP Guidelines:** [owasp.org](https://owasp.org/)
- **MySQL Best Practices:** [dev.mysql.com](https://dev.mysql.com/)

---

## 📞 Support & Contact

- 🐛 **Bug Reports:** [Open an Issue](https://github.com/piyushmaji524/organization/issues)
- 💬 **Questions?** Check [SETUP.md](SETUP.md) or [CONTRIBUTING.md](CONTRIBUTING.md)
- 🔐 **Security Issues:** See [SECURITY.md](SECURITY.md)

---

## 📄 License

This project is licensed under the **MIT License** - see [LICENSE](LICENSE) file for details.

```
Copyright (c) 2024-2026 Sarak Youth Development Council
Permission is hereby granted, free of charge, to any person obtaining a copy...
```

---

## ⭐ Show Your Support

If you find this project helpful, please star ⭐ the repository!

---

<div align="center">

**Made with ❤️ for Sarak Youth Development Council**

[🔗 Visit Website](#) • [📧 Contact](#) • [🐦 Follow](#)

</div>
