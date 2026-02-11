# BxCode CMS

A powerful and flexible Content Management System built with Laravel.

## Installation Guide

Follow these steps to install and run BxCode CMS on your local machine.

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL or compatible database server
- Node.js & NPM (for compiling assets)

### Step 1: Clone the Repository

Download the project source code using Git.

```bash
git clone https://github.com/shubham-ralli/bxcode.git
cd bxcode
```

### Step 2: Install Dependencies

Install the necessary PHP packages using Composer.

```bash
composer install
```

### Step 3: Configure Environment

We need to copy the example environment file.

```bash
cp .env.example .env
```

or on Windows:

```cmd
copy .env.example .env
```

### Step 4: Configure Permissions (Mac/Linux only)

If you are on Mac or Linux, you may need to grant write permissions to the web server.

```bash
chmod -R 777 storage bootstrap/cache
chmod 777 .env
```

### Step 5: Generate Application Key

Generate the encryption key required by Laravel.

```bash
php artisan key:generate
```

### Step 5: Run the Installer

Start the local development server.

```bash
php artisan serve
```

Now, open your browser and navigate to:

[http://127.0.0.1:8000/install](http://127.0.0.1:8000/install)

Follow the on-screen instructions to complete the installation:
1. Enter your Database credentials.
2. Create your Admin account.
3. The installer will automatically run migrations and set up the application for you.

### Alternate Method (Using XAMPP/MAMP)

1. Clone the project into your `htdocs` folder.
2. Run `composer install`.
3. Create a database in phpMyAdmin (e.g., `bxcode`).
4. Navigate to `http://localhost/bxcode/install` in your browser.
5. Follow the installer instructions.
