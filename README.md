# BxCode CMS

A modern, WordPress-style Content Management System built with Laravel. Features an intuitive admin panel, plugin system, theme support, and one-click installation.

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-%3E%3D8.1-purple)
![Laravel](https://img.shields.io/badge/Laravel-10.x-red)

## ✨ Features

- 🚀 **One-Click Installation** - WordPress-style setup wizard
- 📝 **Posts & Pages** - Create and manage content easily
- 🎨 **Theme System** - Switch themes without code changes
- 🔌 **Plugin Architecture** - Extend functionality with plugins
- 👥 **User Management** - Role-based access control
- 🖼️ **Media Library** - Manage images and files
- 📱 **Responsive Admin** - Modern, mobile-friendly dashboard
- 🔍 **SEO Ready** - Built-in SEO meta management
- 🏷️ **Tags & Categories** - Organize content efficiently
- 🎯 **Custom Post Types** - Create your own content types
- 🍔 **Menu Builder** - Visual drag-and-drop menu management

---

## 📦 Quick Start

### Installation (3 Steps)

```bash
# 1. Clone the repository
git clone https://github.com/shubham-ralli/bxcode.git
cd bxcode

# 2. Set permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache

# 3. Visit in browser
# Open: http://localhost/bxcode/public
```

The installer will automatically appear and guide you through:
- Database configuration
- Admin account creation
- Initial setup

**That's it!** No `composer install` or manual commands needed.

[📖 Detailed Installation Guide](INSTALLATION.md)

---

## 🎯 How to Use BxCode CMS

### 1️⃣ **Login to Admin Panel**

After installation, login at:
```
http://your-site.com/login
```

Use the admin email and password you created during installation.

### 2️⃣ **Dashboard Overview**

The dashboard shows:
- Total posts, pages, and users
- Recent activity
- Quick action buttons

### 3️⃣ **Creating Content**

#### **Posts**
1. Go to **Posts** → **Add New**
2. Enter title and content
3. Add featured image (optional)
4. Add tags and categories
5. Set SEO meta (title, description)
6. Click **Publish**

#### **Pages**
1. Go to **Pages** → **Add New**
2. Enter title and content
3. Choose page template (if available)
4. Set parent page (for nested pages)
5. Click **Publish**

#### **Custom Post Types**
1. Go to **Post Types** → **Add New**
2. Define your custom post type (e.g., Products, Portfolio)
3. Start creating content in your new post type

### 4️⃣ **Media Management**

1. Go to **Media** → **Library**
2. Click **Upload** to add images/files
3. Use media in posts by clicking "Set Featured Image" or inserting into content

### 5️⃣ **Building Menus**

1. Go to **Appearance** → **Menus**
2. Create a new menu
3. Drag items from the left (Pages, Posts, Custom Links)
4. Arrange items by dragging
5. Save menu

### 6️⃣ **Installing Plugins**

1. Go to **Plugins** → **Add New**
2. Upload plugin ZIP or select from available plugins
3. Click **Activate**
4. Configure plugin settings (if needed)

### 7️⃣ **Changing Themes**

1. Go to **Appearance** → **Themes**
2. Select a theme
3. Click **Activate**
4. Customize theme settings (if available)

### 8️⃣ **Managing Users**

1. Go to **Users** → **All Users**
2. Click **Add New** to create users
3. Assign roles (Admin, Editor, Author)
4. Manage permissions

### 9️⃣ **SEO Settings**

For each post/page:
1. Scroll to **SEO Meta** section
2. Set custom title and description
3. Add focus keywords
4. Configure robots meta (index/noindex)

### 🔟 **Site Settings**

1. Go to **Settings** → **General**
2. Update:
   - Site title
   - Site description
   - Admin email
   - Timezone
   - Default theme

---

## 🚀 Deployment to Production

### Option 1: Traditional Hosting (cPanel/Shared Hosting)

1. **Upload files** via FTP to `public_html/`
2. **Create database** in cPanel → MySQL Databases
3. **Visit your domain** - Installer will run automatically
4. **Complete setup** via web wizard

### Option 2: VPS/Cloud Server

```bash
# Clone on server
git clone https://github.com/shubham-ralli/bxcode.git /var/www/html/

# Set permissions
sudo chown -R www-data:www-data /var/www/html/
sudo chmod -R 775 storage bootstrap/cache

# Configure web server (Apache/Nginx) to point to /public directory
# Visit domain to complete installation
```

### Option 3: Deploy from GitHub

```bash
# On your server
git clone https://github.com/shubham-ralli/bxcode.git
cd bxcode

# Set permissions for web server
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Point web server to /public directory
# Visit URL to install
```

---

## 🛠️ Development

### Local Development Setup

```bash
# Clone repo
git clone https://github.com/shubham-ralli/bxcode.git
cd bxcode

# Dependencies already included (vendor/ in repo)
# Just set permissions
chmod -R 775 storage bootstrap/cache

# Start local server (XAMPP, MAMP, or built-in)
php -S localhost:8000 -t public
```

### Creating a Plugin

1. Create folder: `resources/views/plugins/YourPlugin/`
2. Add `plugin.json`:
```json
{
  "name": "Your Plugin",
  "description": "Plugin description",
  "version": "1.0.0",
  "author": "Your Name"
}
```
3. Add `functions.php` with your plugin code
4. Activate in admin panel

### Creating a Theme

1. Create folder: `resources/views/themes/your-theme/`
2. Add required files:
   - `functions.blade.php` - Theme functions
   - `index.blade.php` - Main template
   - `single.blade.php` - Single post
   - `page.blade.php` - Page template
3. Activate in **Appearance** → **Themes**

---

## 📋 Requirements

- **PHP:** >= 8.1
- **MySQL:** >= 5.7 or MariaDB >= 10.3
- **Web Server:** Apache (with mod_rewrite) or Nginx
- **PHP Extensions:**
  - PDO
  - OpenSSL
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath

---

## 📂 Project Structure

```
bxcode/
├── app/                 # Application logic
│   ├── Http/
│   │   ├── Controllers/ # Route controllers
│   │   └── Middleware/  # Middleware
│   ├── Models/          # Database models
│   └── Helpers/         # Helper functions
├── config/              # Configuration files
├── database/
│   └── migrations/      # Database migrations
├── public/              # Web root (point here)
│   └── theme/          # Public theme assets
├── resources/
│   └── views/
│       ├── admin/      # Admin panel views
│       ├── plugins/    # Installed plugins
│       └── themes/     # Installed themes
├── routes/              # Route definitions
├── storage/             # Logs, cache, sessions
└── vendor/              # Composer dependencies (included)
```

---

## 🤝 Contributing

Contributions welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

---

## 📄 License

This project is open-source software.

---

## 🆘 Support

- **Documentation:** [INSTALLATION.md](INSTALLATION.md)
- **Issues:** [GitHub Issues](https://github.com/shubham-ralli/bxcode/issues)

---

## 🎯 Roadmap

- [ ] Multi-language support
- [ ] Advanced plugin marketplace
- [ ] Theme customizer
- [ ] REST API
- [ ] GraphQL support
- [ ] Block editor (Gutenberg-style)

---

**Built with ❤️ using Laravel**
