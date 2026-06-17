# 🌍 Sarak Youth Development Council

<div align="center">

[![GitHub](https://img.shields.io/badge/GitHub-Repository-blue?style=flat-square&logo=github)](https://github.com/piyushmaji524/organization)
[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat-square&logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-00758F?style=flat-square&logo=mysql)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)
[![Contributors](https://img.shields.io/badge/Contributors-Welcome-brightgreen?style=flat-square)](CONTRIBUTING.md)

**Empower Youth • Foster Community • Create Impact**

### 🚀 Full-Stack Trilingual Website & Admin Panel
*English • हिंदी • বাংলা*

[Quick Start](#-quick-start) | [Features](#-features) | [Documentation](#-documentation) | [Security](#-security) | [Support](#-support)

</div>

---

## ✨ Highlights

| Feature | Details |
|---------|---------|
| 🌐 **Multilingual** | English, Hindi, Bengali support |
| 👥 **Member Management** | Directory, profiles, communications |
| 📅 **Event Management** | Create events, track RSVPs |
| 🖼️ **Gallery** | Image uploads and management |
| 📊 **Admin Dashboard** | 15+ management pages |
| 🔐 **Secure** | RBAC, bcrypt, PDO, environment config |
| ⚡ **Modern Stack** | PHP 8+, MySQL, vanilla JS |
| 📱 **Responsive** | Works on all devices |

---

## 🚀 Quick Start

### Prerequisites
```bash
PHP 8.0+
MySQL 8.0+ / MariaDB 10.6+
Composer
```

### Installation (5 minutes)
```bash
# Clone
git clone https://github.com/piyushmaji524/organization.git
cd organization

# Install dependencies
composer install

# Setup environment
cp .env.example .env
# Edit .env with your database credentials

# Import database
mysql -u root -p your_db_name < database/schema.sql
mysql -u root -p your_db_name < database/seed.sql

# Run
php -S localhost:8000

# Access
# Frontend: http://localhost:8000
# Admin: http://localhost:8000/admin/login.php
```

**See [SETUP.md](SETUP.md) for detailed instructions**

---

## 🎯 Core Features

### 🎨 Frontend
- ✅ Responsive design
- ✅ Multilingual interface
- ✅ Modern UI components
- ✅ Fast vanilla JavaScript
- ✅ SEO optimized

### 🛠️ Admin Panel
- 👥 User & role management
- 📰 News/Blog posts
- 🎉 Event management
- 📸 Gallery management
- 💬 Message inbox
- 📋 Member directory
- 💰 Donation tracking
- ⚙️ Site settings
- 🔐 RBAC (9 roles)

### 🔌 API Endpoints
```
/api/members.php     - Member data
/api/events.php      - Event management
/api/news.php        - News/Blog
/api/gallery.php     - Gallery images
/api/rsvp.php        - Event RSVPs
/api/contact.php     - Contact forms
/api/apply.php       - Applications
/api/donate.php      - Donations
```

---

## 🛠️ Tech Stack

```
Backend:     PHP 8+ with PDO
Database:    MySQL 8+ / MariaDB 10.6+
Frontend:    HTML5 • CSS3 • JavaScript ES2020
Auth:        bcrypt • Sessions • RBAC
Email:       PHPMailer 6.x
Config:      Environment variables (.env)
```

---

## 🔒 Security Features

> **Enterprise-grade security following OWASP guidelines**

- ✅ **PDO Prepared Statements** - SQL injection protection
- ✅ **bcrypt Hashing** - Secure password storage
- ✅ **RBAC System** - 9 roles with granular permissions
- ✅ **Session Management** - 2-hour timeout
- ✅ **File Validation** - Type & size checks
- ✅ **Environment Config** - No hardcoded credentials
- ✅ **Access Blocking** - .htaccess rules
- ✅ **Security Headers** - XSS, MIME type protection
- ✅ **OWASP Compliance** - Industry standards

---

## 📚 Documentation

| Document | Purpose |
|----------|---------|
| [📖 SETUP.md](SETUP.md) | Installation & deployment guide |
| [🤝 CONTRIBUTING.md](CONTRIBUTING.md) | Contribution guidelines |
| [🔐 SECURITY.md](SECURITY.md) | Security policy & reporting |
| [📋 GITHUB_CHECKLIST.md](GITHUB_CHECKLIST.md) | Pre-upload checklist |
| [📄 LICENSE](LICENSE) | MIT License |

---

## 📊 Project Stats

```
📁 Source Files:    100+
🐘 PHP Endpoints:   25+
📊 Database Tables: 13
👥 User Roles:      9
🌍 Languages:       3
📄 Admin Pages:     15+
🔌 API Routes:      8+
```

---

## 🚀 Deployment

### Production Checklist
```
[ ] Change default admin password
[ ] Update .env with production credentials
[ ] Enable HTTPS (uncomment in .htaccess)
[ ] Set strong database password
[ ] Use app-specific SMTP password
[ ] Enable database backups
[ ] Set file permissions (755 dirs, 644 files)
[ ] Test all functionality
```

See [SETUP.md](SETUP.md) for complete deployment guide.

---

## 🤝 Contributing

We welcome contributions!

1. **Fork** the repository
2. **Create** feature branch: `git checkout -b feature/awesome-feature`
3. **Commit** changes: `git commit -m 'Add awesome feature'`
4. **Push** branch: `git push origin feature/awesome-feature`
5. **Submit** Pull Request

See [CONTRIBUTING.md](CONTRIBUTING.md) for detailed guidelines.

---

## 🐛 Issues & Support

- **Bug Report:** [Open Issue](https://github.com/piyushmaji524/organization/issues)
- **Questions:** Check [SETUP.md](SETUP.md)
- **Security:** See [SECURITY.md](SECURITY.md)

---

## 📄 License

MIT License - See [LICENSE](LICENSE) for details

```
Copyright (c) 2024-2026 Sarak Youth Development Council
Permission is hereby granted, free of charge, to any person obtaining a copy...
```

---

## ⭐ Show Your Support

If you find this helpful, please ⭐ star the repository!

<div align="center">

### 💜 Made with ❤️ for Community Empowerment

**[View on GitHub](https://github.com/piyushmaji524/organization) • [Documentation](SETUP.md) • [Security](SECURITY.md)**

</div>
