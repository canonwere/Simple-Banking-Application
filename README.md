# Simple Banking Application

A multi-page PHP/MySQL banking web application with user authentication, account management, and full transaction tracking. Built as a learning project demonstrating backend web development with PHP, MySQL, and Tailwind CSS.

---

## Features

- **User Registration & Login** — secure session-based authentication with password hashing
- **Dashboard** — live balance overview, deposit/withdrawal totals, recent transactions
- **Deposit** — add funds with optional description
- **Withdraw** — debit funds with insufficient-balance check
- **Transfer** — send money to another account by account number
- **Transaction History** — full log with type filters (deposit, withdrawal, transfer in/out)
- **Profile** — edit name, email, and change password
- **Flash Messages** — success/error feedback using Post-Redirect-Get pattern
- **Data Persistence** — all balances and transactions stored in MySQL

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8+ |
| Database | MySQL (via XAMPP) |
| Frontend | HTML5, Tailwind CSS (CDN) |
| Auth | PHP Sessions + `password_hash()` |
| Server | Apache (XAMPP) |

---

## Project Structure

```
Simple-Banking-Application/
├── index.php               ← Login page
├── register.php            ← New account registration
├── logout.php
├── pages/
│   ├── dashboard.php       ← Balance overview + recent transactions
│   ├── deposit.php
│   ├── withdraw.php
│   ├── transfer.php        ← Send to another account by number
│   ├── history.php         ← Full transaction log with filters
│   └── profile.php         ← Edit profile + change password
├── includes/
│   ├── config.php          ← Database connection + session start
│   ├── auth.php            ← requireLogin(), getCurrentUser()
│   ├── functions.php       ← logTransaction(), flash messages, helpers
│   ├── header.php          ← Sidebar navigation layout
│   └── footer.php
├── assets/
│   ├── css/main.css        ← Custom styles
│   └── js/main.js          ← UX enhancements
└── database/
    └── schema.sql          ← Database setup script
```

---

## Database Schema

Three tables:

- **`users`** — account holder name, email, hashed password
- **`accounts`** — account number, balance, account type, linked user
- **`transactions`** — type, amount, balance snapshot, description, timestamps

---

## Setup (XAMPP)

**Requirements:** XAMPP with Apache and MySQL running.

```bash
# 1. Clone the repository
git clone https://github.com/canonwere/Simple-Banking-Application.git

# 2. Move to XAMPP web root
#    Place folder in: C:\xampp\htdocs\  or  D:\xampp\htdocs\

# 3. Import the database
#    Open phpMyAdmin → Import → select database/schema.sql

# 4. Open in browser
http://localhost/Simple-Banking-Application/
```

> No Composer or Node.js required. Tailwind CSS is loaded via CDN.

---

## Screenshots

| Login | Dashboard | Transaction History |
|-------|-----------|-------------------|
| Email + password login | Balance card, quick actions, recent transactions | Filterable full transaction log |

---

## Security

- Passwords hashed with `password_hash()` (bcrypt)
- All database queries use PDO prepared statements (SQL injection safe)
- Session-based authentication with `requireLogin()` guard on every page
- Post-Redirect-Get pattern prevents duplicate form submissions
- Confirm dialog before destructive actions (reset, clear history)

---

## Future Improvements

- [ ] Add admin panel for multi-user management
- [ ] Email notifications on transactions
- [ ] Export statement as PDF
- [ ] Migrate to Laravel + PostgreSQL for production-grade deployment

---

*Part of a banking systems portfolio. See also: [digital-banking-system](https://github.com/canonwere/digital-banking-system) — enterprise Laravel + Docker version.*
