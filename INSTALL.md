# HUBzero CMS Installation Guide

This guide walks you through installing HUBzero CMS from a fresh git clone.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Quick Start](#quick-start)
3. [Installation Steps](#installation-steps)
4. [Troubleshooting](#troubleshooting)

## Prerequisites

Before installing HUBzero, ensure your system meets the following requirements.

### PHP 8.2 or Higher

HUBzero requires PHP 8.2.0 or later.

**Check your version:**
```bash
php -v
```

**Installation:**

- **Ubuntu/Debian:**
  ```bash
  sudo apt update
  sudo apt install php8.2 php8.2-cli
  ```

- **RHEL/CentOS/Rocky Linux:**
  ```bash
  sudo dnf install php82 php82-cli
  ```

- **macOS (Homebrew):**
  ```bash
  brew install php@8.2
  ```

### Required PHP Extensions

The following PHP extensions must be installed and enabled:

| Extension | Purpose | Installation |
|-----------|---------|--------------|
| `pdo` | Database abstraction layer | Usually included with PHP |
| `pdo_mysql` | MySQL database driver | `apt install php8.2-mysql` |
| `json` | JSON data handling | Usually included with PHP |
| `mbstring` | Multibyte string support | `apt install php8.2-mbstring` |
| `openssl` | Encryption and SSL support | Usually included with PHP |
| `curl` | HTTP client for external APIs | `apt install php8.2-curl` |
| `gd` | Image processing and manipulation | `apt install php8.2-gd` |
| `fileinfo` | MIME type detection | `apt install php8.2-fileinfo` |
| `zip` | Archive handling | `apt install php8.2-zip` |

**Check installed extensions:**
```bash
php -m | grep -E "pdo|json|mbstring|openssl|curl|gd|fileinfo|zip"
```

**Install all required extensions (Ubuntu/Debian):**
```bash
sudo apt install php8.2-mysql php8.2-mbstring php8.2-curl php8.2-gd php8.2-zip
```

**Install all required extensions (RHEL/CentOS/Rocky Linux):**
```bash
sudo dnf install php82-mysqlnd php82-mbstring php82-curl php82-gd php82-zip
```

### Unzip Command

The `unzip` command is required by Composer to extract packages.

**Check if installed:**
```bash
which unzip
```

**Installation:**

- **Ubuntu/Debian:**
  ```bash
  sudo apt install unzip
  ```

- **RHEL/CentOS/Rocky Linux:**
  ```bash
  sudo dnf install unzip
  ```

- **macOS:**
  ```bash
  brew install unzip
  ```

### MySQL Database

HUBzero requires a MySQL 5.7+ or MariaDB 10.3+ database server.

**Installation:**

- **Ubuntu/Debian:**
  ```bash
  sudo apt install mysql-server
  sudo mysql_secure_installation
  ```

- **RHEL/CentOS/Rocky Linux:**
  ```bash
  sudo dnf install mysql-server
  sudo systemctl start mysqld
  sudo mysql_secure_installation
  ```

**Create a database and user:**
```sql
CREATE DATABASE hubzero CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'hubzero'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON hubzero.* TO 'hubzero'@'localhost';
FLUSH PRIVILEGES;
```

### Web Server

HUBzero supports Apache or Nginx.

**Apache (recommended):**
```bash
# Ubuntu/Debian
sudo apt install apache2 libapache2-mod-php8.2
sudo a2enmod rewrite
sudo systemctl restart apache2

# RHEL/CentOS/Rocky Linux
sudo dnf install httpd php82-fpm
sudo systemctl enable httpd php82-fpm
sudo systemctl start httpd php82-fpm
```

**Nginx:**
```bash
# Ubuntu/Debian
sudo apt install nginx php8.2-fpm
sudo systemctl restart nginx php8.2-fpm
```

### Directory Permissions

The web server user must have write access to the installation directory.

**Check the web server user:**
```bash
# Apache on Ubuntu/Debian
ps aux | grep apache  # Usually www-data

# Apache on RHEL/CentOS
ps aux | grep httpd   # Usually apache

# Nginx
ps aux | grep nginx   # Usually www-data or nginx
```

**Set permissions:**
```bash
# Replace www-data with your web server user
sudo chown -R www-data:www-data /path/to/hubzero-cms
sudo chmod -R 775 /path/to/hubzero-cms
```

## Quick Start

For those familiar with HUBzero, here's the quick installation:

```bash
# Clone the repository
git clone https://github.com/hubzero/hubzero-cms.git
cd hubzero-cms

# Run the installer
php core/bin/muse install
```

The installer will guide you through the remaining steps.

## Installation Steps

### Step 1: Clone the Repository

```bash
git clone https://github.com/hubzero/hubzero-cms.git
cd hubzero-cms
```

### Step 2: Run Pre-flight Checks

Before installing, verify your system meets all requirements:

```bash
php core/bin/muse install check
```

This will check:
- PHP version
- Required PHP extensions
- System commands (unzip)
- Directory permissions
- Composer dependencies

Fix any issues reported before proceeding.

### Step 3: Install Composer Dependencies

If you want to install dependencies separately:

```bash
php core/bin/muse install vendor
```

This is optional - the full installer will do this automatically if needed.

### Step 4: Run the Full Installation

```bash
php core/bin/muse install
```

The installer will:

1. **Pre-flight Checks** - Verify all requirements are met
2. **Create App Directory** - Set up the `/app` directory structure with proper permissions
3. **Database Setup** - Prompt for database credentials and test the connection
4. **Load Schema** - Create the database tables
5. **Load Base Data** - Insert required system data
6. **Load Sample Data** (optional) - Add example content
7. **Generate Configuration** - Create configuration files in `/app/config`
8. **Create Admin User** - Set up the initial administrator account
9. **Run Migrations** (optional) - Apply any pending database migrations
10. **Final Verification** - Confirm the installation is complete

### Step 5: Configure Your Web Server

**Apache Virtual Host Example:**

```apache
<VirtualHost *:80>
    ServerName your-hub.example.com
    DocumentRoot /path/to/hubzero-cms

    <Directory /path/to/hubzero-cms>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/hubzero-error.log
    CustomLog ${APACHE_LOG_DIR}/hubzero-access.log combined
</VirtualHost>
```

**Nginx Configuration Example:**

```nginx
server {
    listen 80;
    server_name your-hub.example.com;
    root /path/to/hubzero-cms;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
```

### Step 6: Access Your Hub

Open your browser and navigate to your hub's URL. Log in with the admin credentials you created during installation.

## Installation Commands Reference

| Command | Description |
|---------|-------------|
| `muse install` | Run the full installation process |
| `muse install check` | Run pre-flight checks only |
| `muse install vendor` | Install Composer dependencies only |
| `muse install appdir` | Create the app directory structure only |
| `muse install --force` | Force reinstallation (overwrites existing) |

## Troubleshooting

### Pre-flight Check Failures

**PHP version too low:**
```
[FAIL] PHP 7.4.33 (requires 8.2.0+)
```
Upgrade PHP to version 8.2 or higher.

**Missing PHP extension:**
```
[FAIL] pdo_mysql extension - MySQL database driver
```
Install the missing extension and restart your web server.

**Unzip not found:**
```
[FAIL] unzip command not found
```
Install the unzip package for your operating system.

**Directory not writable:**
```
[FAIL] /path/to/hubzero-cms is not writable
```
Adjust permissions so the web server user can write to the directory.

**Already installed:**
```
[FAIL] HUBzero appears to already be installed
```
Use `--force` flag to reinstall, or manually remove `/app/config/database.php`.

### Database Connection Issues

If you cannot connect to the database:

1. Verify MySQL/MariaDB is running:
   ```bash
   sudo systemctl status mysql
   ```

2. Test the connection manually:
   ```bash
   mysql -u hubzero -p -h localhost hubzero
   ```

3. Check that the user has proper privileges:
   ```sql
   SHOW GRANTS FOR 'hubzero'@'localhost';
   ```

### Permission Issues

If you encounter permission errors during installation:

```bash
# Reset ownership
sudo chown -R www-data:www-data /path/to/hubzero-cms

# Set directory permissions
sudo find /path/to/hubzero-cms -type d -exec chmod 775 {} \;

# Set file permissions
sudo find /path/to/hubzero-cms -type f -exec chmod 664 {} \;

# Secure config directory
sudo chmod 770 /path/to/hubzero-cms/app/config
```

### Getting Help

- Check the logs in `/app/logs/`
- Run `muse install check` to diagnose issues
- Visit [HUBzero Documentation](https://help.hubzero.org/)
- Report issues at [GitHub Issues](https://github.com/hubzero/hubzero-cms/issues)
