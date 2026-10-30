<!--
status: rewritten
reviewed-against: 2.4-main @ 581dd435fc
reviewed: 2026-10-30
screenshots: none
summary: Preparing an Enterprise Linux 10 server (RHEL, Rocky Linux, AlmaLinux) for a hub: PHP 8.2 from Remi's repository and MariaDB 10.11, behind Apache or nginx.
-->
# Preparing an Enterprise Linux 10 server

This page prepares a server running Enterprise Linux 10 for a hub, from nothing to a
web server ready to serve it. Enterprise Linux 10 means Red Hat Enterprise Linux 10 and its rebuilds, Rocky Linux and AlmaLinux. It uses PHP 8.2 from Remi Collet's repository, the distribution's own MariaDB 10.11, and Apache or nginx with PHP-FPM. Then
[Installing the hub](../03-installing-the-hub.md) installs it, the same way on every system.

A full install from the command line was run in a Rocky Linux 10 container with these packages behind this Apache configuration, and nginx accepted this nginx configuration. A container has no SELinux, firewall or systemd, so those steps are written from the distribution's documentation and were not run. Remi's repository was tried on Rocky Linux, not on Red Hat Enterprise Linux itself.

For another system, or help choosing one, see
[Preparing the server](README.md).

## What you need

- A server running Enterprise Linux 10, and an account on it that can use `sudo`.
- A name for the hub, such as `hub.example.org`, pointing at the server, and a
  TLS certificate for it. A hub has to be served over HTTPS: the administrator
  sign-in form always posts to an `https://` address, so on plain HTTP the
  administrator can never sign in.

## 1. Install MariaDB, PHP and the tools

Enterprise Linux 10 has PHP 8.3 and no PHP 8.2 of its own. PHP 8.2 comes from Remi Collet's repository, which needs EPEL. Add both, and choose Remi's PHP 8.2:

```bash
sudo dnf install epel-release
sudo dnf install https://rpms.remirepo.net/enterprise/remi-release-10.rpm
sudo dnf module enable php:remi-8.2
```

On Red Hat Enterprise Linux itself, where `epel-release` is not in the repositories, install it from `https://dl.fedoraproject.org/pub/epel/epel-release-latest-10.noarch.rpm` instead.

Then the packages. MariaDB is the distribution's own, 10.11.

```bash
sudo dnf install mariadb-server git unzip \
    php-cli php-fpm php-mysqlnd php-mbstring php-gd php-xml php-process php-pecl-zip
sudo systemctl enable --now mariadb php-fpm
```

`php-process` brings the POSIX functions the command line uses, and `php-xml`
the XML extensions the libraries Composer installs need. `curl`, `sockets`,
`json`, `openssl`, `fileinfo` and `pdo` come with `php-cli` and its common
package.

## 2. Install the web server

Choose Apache **or** nginx. Install one of them, not both.

**Apache:**

```bash
sudo dnf install httpd mod_ssl
sudo systemctl enable --now httpd
```

**Or nginx:**

```bash
sudo dnf install nginx
sudo systemctl enable --now nginx
```

## 3. Open the firewall

Enterprise Linux starts with its firewall closed. Open the web ports:

```bash
sudo firewall-cmd --permanent --add-service=http --add-service=https
sudo firewall-cmd --reload
```

## 4. Get the code

The repository is the hub: its top directory is the web server's document
root. Clone it, and give it to `apache`, the account PHP-FPM runs as:

```bash
sudo git clone --branch 2.4-main https://github.com/hubzero/hubzero-cms.git /var/www/hub
sudo chown -R apache:apache /var/www/hub
```

The installer writes the hub's configuration into a new `app/` directory
inside it, and the hub later keeps its uploads, logs and cache there, so
`apache` has to be able to write to the whole tree.

### SELinux

SELinux lets the web server read files under `/var/www` but not write them,
and not open network connections. The hub writes to its own directory, and
the installer writes to the whole of it; the hub connects to MariaDB over
TCP, and the browser installer downloads Composer's libraries. Allow those:

```bash
sudo dnf install policycoreutils-python-utils
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/hub(/.*)?"
sudo restorecon -R /var/www/hub
sudo setsebool -P httpd_can_network_connect_db 1
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_can_sendmail 1
```

These were not run: the test installs were in containers, which have no
SELinux. If something the web server does fails with a permission error that
the file permissions do not explain, `sudo ausearch -m avc -ts recent` shows
what SELinux refused. The command-line installer does its work as a login
user rather than through the web server, so it is the safer of the two
installers until SELinux is set up for the hub.

## 5. Configure the web server

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
`sudo dnf install epel-release && sudo dnf install certbot`, certbot being in EPEL.

### If you installed Apache

`/etc/httpd/conf.d/hub.conf`:

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
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
    </FilesMatch>

    <LocationMatch "^/(app/(config|logs|tmp)|core/(bin|vendor/bin))(/|$)">
        Require all denied
    </LocationMatch>
    <LocationMatch "/\.(?!well-known)">
        Require all denied
    </LocationMatch>

    ErrorLog logs/hub-error.log
    CustomLog logs/hub-access.log combined
</VirtualHost>
```

```bash
sudo apachectl configtest && sudo systemctl reload httpd
```

The repository has no `.htaccess` file. Everything the hub needs is in this
block, so `AllowOverride None` is right, and `FallbackResource` does the work
a rewrite rule would. `mod_ssl` already listens on port 443, and logs are relative to
`/var/log/httpd`.

### Or, if you installed nginx

`/etc/nginx/conf.d/hub.conf`:

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
        fastcgi_pass unix:/run/php-fpm/www.sock;
        fastcgi_read_timeout 300;
    }
}
```

```bash
sudo nginx -t && sudo systemctl reload nginx
```

nginx reads `conf.d` without a link, and PHP-FPM's pool already lets nginx
use its socket. The stock `/etc/nginx/nginx.conf` also has a server on port
80 for any other name; it does not get in the way, but you can delete it. Raise `client_max_body_size` if members will upload files
larger than 100 MB.

## 6. Set the values the installation uses

The rest of the installation is the same on every system, and runs the hub's
commands through two values. Set them in the shell you will carry on in:

```bash
export HUB_OWNER=apache:apache
export HUB_PHP="sudo -u apache php"
```

`HUB_OWNER` is the account and group that own the hub's files, and `HUB_PHP`
is how to run the hub's PHP. If you open a new shell before you have
finished, set them again.

Once the hub is installed, scheduled tasks need this line in `/etc/crontab`.
Keep it for then:

```
* * * * * apache /var/www/hub/core/bin/muse cron:jobs tick
```

## 7. Next: install the hub

The server is ready. Go on to [Installing the hub](../03-installing-the-hub.md), which creates
the database and runs the installer, the same way on every system.

## When something goes wrong on Enterprise Linux 10

**`/administrator` answers 404 under nginx.** The PHP block is passing every
`.php` address straight to PHP. Use the block above, which
hands addresses that are not files to `index.php`.

**Something the web server does fails with a permission error, though the
files belong to `apache`.** SELinux is refusing it. See [SELinux](#selinux).

For everything else, see
[When something goes wrong](../03-installing-the-hub.md#when-something-goes-wrong) on the page
that installs the hub.
