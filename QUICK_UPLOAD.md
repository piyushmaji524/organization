# ⚡ Quick GitHub Upload Guide

**TL;DR** - Get your project on GitHub in 5 minutes

## Prerequisites
- Git installed on your computer
- GitHub account (free at github.com)
- Project folder: `d:\0000\sarak-youth`

## Step 1: Configure Git (First Time Only)
```bash
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

## Step 2: Initialize Repository
```bash
cd d:\0000\sarak-youth
git init
```

## Step 3: Verify .env is Ignored
```bash
# Check that .env will NOT be committed
cat .gitignore | grep ".env"
# Output should show: .env
```

## Step 4: Create Initial Commit
```bash
# Stage all files
git add .

# Verify .env is NOT staged
git status

# Commit
git commit -m "Initial commit: Sarak Youth Development Council website"
```

## Step 5: Create GitHub Repository
1. Go to https://github.com/new
2. Fill in:
   - **Repository name:** `sarak-youth`
   - **Description:** `Full-stack, trilingual website + admin panel`
   - **Visibility:** Public
3. **Skip:** "Initialize with README" (we have one)
4. Click "Create repository"

## Step 6: Add Remote & Push
```bash
# Copy the command from GitHub (looks like this):
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/sarak-youth.git
git push -u origin main
```

## ✅ Verify Upload Complete
- [ ] Visit `https://github.com/YOUR_USERNAME/sarak-youth`
- [ ] README displays correctly
- [ ] No `.env` file visible in repository
- [ ] All documentation files present

## 🔒 After Upload: Security Setup
```bash
# On your local machine only (NEVER commit these)
cp .env.example .env

# Edit with YOUR credentials
nano .env  # or open in editor

# Update:
# DB_NAME = your_database_name
# DB_USER = your_db_user
# DB_PASS = your_strong_password
# SMTP_USERNAME = your_email
# SMTP_PASSWORD = your_app_password
```

## 📚 Documentation
- **Setup Instructions:** See [SETUP.md](SETUP.md)
- **For Developers:** See [CONTRIBUTING.md](CONTRIBUTING.md)
- **Security & CVE:** See [SECURITY.md](SECURITY.md)
- **Detailed Checklist:** See [GITHUB_CHECKLIST.md](GITHUB_CHECKLIST.md)
- **Full Report:** See [GITHUB_UPLOAD_REPORT.md](GITHUB_UPLOAD_REPORT.md)

## ⚠️ Critical Security Reminders

❌ **DO NOT:**
- Commit `.env` file
- Share `.env` in email or Slack
- Use personal passwords for SMTP
- Leave default admin password unchanged

✅ **DO:**
- Use `.env.example` as template
- Store `.env` securely (password manager)
- Use app-specific SMTP passwords
- Change admin password immediately
- Update dependencies regularly

## Common Issues & Fixes

### "fatal: not a git repository"
```bash
git init  # in project root directory
```

### ".env file appears in git status"
```bash
git rm --cached .env
git status  # Should now be ignored
```

### "error: failed to push"
```bash
# Verify remote is correct
git remote -v

# Should show origin pointing to GitHub
# If wrong, remove and add correct one:
git remote remove origin
git remote add origin https://github.com/YOUR_USERNAME/sarak-youth.git
```

### "commits appear before first push"
```bash
# Check branch name
git branch

# Switch to main if needed
git checkout -m main

# Then push
git push -u origin main
```

## 🆘 Need Help?
- Git documentation: https://git-scm.com/doc
- GitHub guides: https://guides.github.com/
- This project's SECURITY.md: See this repository

---

**You're all set!** 🚀

Once uploaded, share the GitHub link with your team and start collaborating!
