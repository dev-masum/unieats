# UniEats – Smart Campus Food Ordering System

A university assignment built with plain PHP and MySQL.

---

## Prerequisites

- **PHP 8.x** with `mysqli` extension enabled
- **MySQL 8.0** (via XAMPP)

---

## Database Setup (XAMPP)

1. Start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Open **phpMyAdmin** at `http://localhost/phpmyadmin`.
3. Create a new database called `unieats_db` (collation: `utf8mb4_unicode_ci`).
4. Import the SQL files from the `migrations/` folder as described in Step 3 below.

---

## Step 1 – Configure Database Connection

Edit `config/config.php` if your credentials differ from the defaults:

```php
<?php
return [
    'db_host' => 'localhost',
    'db_user' => 'root',
    'db_pass' => '',
    'db_name' => 'unieats_db',
];
```

## Step 2 – Create the Database

If you haven't already created the database, run this query first:

```sql
CREATE DATABASE IF NOT EXISTS unieats_db;

USE unieats_db;
```

## Step 3 – Create Tables and Seed Data

To set up the database schema and populate it with sample data, import the SQL files located in the `migrations/` directory in the following order:

1. Import `migrations/01_create_tables.sql` to create all necessary tables.
2. Import `migrations/02_seed_data.sql` to insert the sample test data.

If you are using phpMyAdmin (with XAMPP), you can do this by selecting the `unieats_db` database, clicking the **Import** tab, and uploading these two files one by one.

If you are using a MySQL CLI, you can run:
```bash
mysql -h localhost -u root unieats_db < migrations/01_create_tables.sql
mysql -h localhost -u root unieats_db < migrations/02_seed_data.sql
```

---

## Step 5 – Start the Application

From the project root directory, run:

```bash
php -S localhost:8000 -t public
```

Open in your browser: **http://localhost:8000**

---

## Test Accounts

| Role    | Username   | Password      |
|---------|------------|---------------|
| Student | `student1` | `password123` |
| Admin   | `admin1`   | `admin123`    |

---

## Features

- User registration and login
- Menu browsing with category and availability filters
- Shopping cart with quantity update and removal
- Order checkout with collection time and payment method
- Order history for users
- Admin dashboard to update order status and payment status
- Admin activity logging

## Notes

- This is a university assignment implementation with plain PHP and minimal styling.
- Passwords are hashed using PHP `password_hash`.
- No external frameworks are required.
