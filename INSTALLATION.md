# BxCode CMS - Installation Guide

## Quick Install (WordPress-Style)

### Step 1: Download
```bash
git clone https://github.com/shubham-ralli/bxcode.git
cd bxcode
```

### Step 2: Set Permissions (Important!)
```bash
chmod -R 775 storage bootstrap/cache
chmod 666 .env.example
```

### Step 3: Visit Your Site
Open your browser and visit your site URL (e.g., `http://localhost/bxcode/public`)

**The system will automatically:**
- Detect that you haven't configured the database yet
- Redirect you to `/install`
- Show you the installation wizard

### Step 4: Complete the Wizard
Fill in the form with:
- **Database Name** - Your MySQL database name
- **Database Username** - Your MySQL username  
- **Database Password** - Your MySQL password
- **Admin Email** - Your admin login email
- **Admin Password** - Your admin password (min 8 characters)

Click **Install** and you're done! 🎉

---

## That's It!

**No need to run:**
- ❌ `composer install` (dependencies are already included)
- ❌ `php artisan key:generate` (done automatically)
- ❌ `php artisan migrate` (done automatically)

Everything is automated like WordPress!

---

## Manual .env Setup (Alternative to Web Installer)

If you prefer to configure manually instead of using the web installer:

### 1. Create .env file
```bash
cp .env.example .env
```

### 2. Edit .env and update database credentials
```bash
nano .env  # or use any text editor
```

Update these lines:
```env
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Generate application key
```bash
php artisan key:generate
```

### 4. Run migrations
```bash
php artisan migrate
```

### 5. Create admin user (run in terminal)
```bash
php artisan tinker
```
Then in tinker console:
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('your-password')
]);
exit
```

---

## Troubleshooting

### Permission Issues
If you get permission errors with chmod, use sudo:
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache  # Linux/Ubuntu
# OR
sudo chown -R _www:_www storage bootstrap/cache  # macOS
# OR  
sudo chown -R daemon:daemon storage bootstrap/cache  # XAMPP
```

For XAMPP on Mac specifically:
```bash
sudo chmod -R 777 storage bootstrap/cache  # Less secure but works for local dev
```

---

## Requirements

- PHP >= 8.1
- MySQL >= 5.7
- Web server (Apache/Nginx) with mod_rewrite enabled
