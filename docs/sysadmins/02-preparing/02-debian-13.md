<!--
status: rewritten
reviewed-against: 2.4-main @ d96149df5b
reviewed: 2026-10-30
screenshots: none
summary: Preparing a Debian 13 server for a hub: PHP 8.2 from Ondřej Surý's archive and MariaDB 11.8, behind Apache or nginx.
-->
# Preparing a Debian 13 server

This page prepares a server running Debian 13 for a hub, from nothing to a
web server ready to serve it. It uses PHP 8.2 from Ondřej Surý's archive, Debian 13's own MariaDB 11.8, and Apache or nginx with PHP-FPM. Then
[Installing the hub](../03-installing-the-hub.md) installs it, the same way on every system.

The PHP 8.2 packages below were installed on Debian 13 and every extension the hub needs was present, and a hub was installed against MariaDB 11.8. Full installs, from the browser and the command line, were run with the same Apache and nginx configurations on Ubuntu 24.04.

For another system, or help choosing one, see
[Preparing the server](README.md).

## What you need

- A server running Debian 13, and an account on it that can use `sudo`.
- A name for the hub, such as `hub.example.org`, pointing at the server, and a
  TLS certificate for it. A hub has to be served over HTTPS: the administrator
  sign-in form always posts to an `https://` address, so on plain HTTP the
  administrator can never sign in.

## 1. Install MariaDB, PHP and the tools

Debian 13 ships PHP 8.4. PHP 8.2 comes from Ondřej Surý's archive, which packages it for Debian under the names Debian uses. Add it first:

```bash
sudo apt install curl ca-certificates lsb-release
curl -sSLo /tmp/debsuryorg-archive-keyring.deb https://packages.sury.org/debsuryorg-archive-keyring.deb
sudo dpkg -i /tmp/debsuryorg-archive-keyring.deb
echo "deb [signed-by=/usr/share/keyrings/debsuryorg-archive-keyring.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" \
    | sudo tee /etc/apt/sources.list.d/php.list
sudo apt update
```

Then the packages. MariaDB is Debian 13's own, 11.8.

```bash
sudo apt install mariadb-server git unzip \
    php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-curl \
    php8.2-gd php8.2-zip php8.2-xml
```

`php8.2-xml` is not on the installer's list of requirements, but the libraries
Composer installs need it. `json`, `openssl`, `fileinfo` and `pdo` come with
PHP itself.

## 2. Install the web server

Choose Apache **or** nginx. Install one of them, not both.

**Apache:**

```bash
sudo apt install apache2
sudo a2enmod proxy_fcgi setenvif ssl
```

**Or nginx:**

```bash
sudo apt install nginx
```

## 3. Get the code

The repository is the hub: its top directory is the web server's document
root. Clone it, and give it to `www-data`, the account PHP-FPM runs as:

```bash
sudo git clone --branch 2.4-main https://github.com/hubzero/hubzero-cms.git /var/www/hub
sudo chown -R www-data:www-data /var/www/hub
```

The installer writes the hub's configuration into a new `app/` directory
inside it, and the hub later keeps its uploads, logs and cache there, so
`www-data` has to be able to write to the whole tree.

## 4. Configure the web server

Each configuration below does the same four things:

- serves `/var/www/hub` as the document root, over HTTPS;
- passes PHP files to PHP;
- sends every address that is not a file to `index.php`, which is how the
  hub's addresses work;
- refuses requests for the hub's configuration, logs, temporary files,
  command-line programs and hidden files.

The last of those matters: `app/config/database.php` holds the database
password, and nothing else stops a browser from reading it.

Replace `hub.example.org` and the certificate paths with your own. The
examples use certificates from Let's Encrypt's `certbot`:
`sudo apt install certbot`.

### Before you have a certificate

The hub is always served over HTTPS, but it need not wait for a real
certificate, or for its name to reach the server. Start with a self-signed
one. Debian and Ubuntu make one with the `ssl-cert` package:

```bash
sudo apt install ssl-cert
```

and in the configuration below, use its files in place of the Let's Encrypt
paths:

| | Path |
|---|---|
| Certificate | `/etc/ssl/certs/ssl-cert-snakeoil.pem` |
| Key | `/etc/ssl/private/ssl-cert-snakeoil.key` |

Browsers warn about a self-signed certificate; accept the warning to reach
the installer. When the real certificate arrives, put its paths in instead
and reload the web server.

### If you installed Apache

`/etc/apache2/sites-available/hub.conf`:

```apache
<VirtualHost *:80>
    ServerName hub.example.org
    Redirect permanent / https://hub.example.org/
</VirtualHost>

<VirtualHost *:443>
    ServerName hub.example.org
    DocumentRoot /var/www/hub

    SSLEngine on
    SSLCertificateFile    /etc/letsencrypt/live/hub.example.org/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/hub.example.org/privkey.pem

    <Directory /var/www/hub>
        Options -Indexes +FollowSymLinks
        AllowOverride None
        Require all granted
        FallbackResource /index.php
    </Directory>

    <FilesMatch "\.php$">
        SetHandler "proxy:unix:/run/php/php8.2-fpm.sock|fcgi://localhost"
    </FilesMatch>

    <LocationMatch "^/(app/(config|logs|tmp)|core/(bin|vendor/bin))(/|$)">
        Require all denied
    </LocationMatch>
    <LocationMatch "/\.(?!well-known)">
        Require all denied
    </LocationMatch>

    ErrorLog ${APACHE_LOG_DIR}/hub-error.log
    CustomLog ${APACHE_LOG_DIR}/hub-access.log combined
</VirtualHost>
```

```bash
sudo a2ensite hub
sudo apache2ctl configtest && sudo systemctl reload apache2
```

The repository has no `.htaccess` file. Everything the hub needs is in this
block, so `AllowOverride None` is right, and `FallbackResource` does the work
a rewrite rule would.

### Or, if you installed nginx

`/etc/nginx/sites-available/hub`:

```nginx
server {
    listen 80;
    server_name hub.example.org;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name hub.example.org;
    root /var/www/hub;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/hub.example.org/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/hub.example.org/privkey.pem;

    client_max_body_size 100m;

    location ~ ^/(app/(config|logs|tmp)|core/(bin|vendor/bin))(/|$) { deny all; }
    location ~ /\.(?!well-known) { deny all; }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Addresses such as /administrator/index.php name no file; the hub's own
    # index.php answers them, as it does every other address
    location ~ \.php$ {
        try_files $uri /index.php?$query_string;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_read_timeout 300;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/hub /etc/nginx/sites-enabled/hub
sudo nginx -t && sudo systemctl reload nginx
```

Do not use the distribution's `snippets/fastcgi-php.conf` for the PHP block.
It answers 404 for any `.php` address that is not a file, and the
administrator interface lives at one: `/administrator/index.php` is answered
by the hub's `index.php`, not by a file of that name. Raise `client_max_body_size` if members will upload files
larger than 100 MB.

## 5. Next: install the hub

The server is ready. Go on to [Installing the hub](../03-installing-the-hub.md), which creates
the database and runs the installer, the same way on every system.

## When something goes wrong on Debian 13

**`/administrator` answers 404 under nginx.** The PHP block is passing every
`.php` address straight to PHP, or uses `snippets/fastcgi-php.conf`. Use the block above, which
hands addresses that are not files to `index.php`.

For everything else, see
[When something goes wrong](../03-installing-the-hub.md#when-something-goes-wrong) on the page
that installs the hub.
