<!--
status: rewritten
reviewed-against: 2.4-main @ 4b8a132fb4
reviewed: 2026-10-30
screenshots: ok
summary: Installing a hub on Enterprise Linux 10 (RHEL, Rocky Linux, AlmaLinux) with PHP 8.2 from Remi's repository and MariaDB 10.11, behind Apache or nginx.
-->
# Installing on Enterprise Linux 10

This page takes a server running Enterprise Linux 10 with nothing on it to a hub that
answers at its own address and has an administrator who can sign in.
Enterprise Linux 10 means Red Hat Enterprise Linux 10 and its rebuilds, Rocky Linux and AlmaLinux. It uses PHP 8.2 from Remi Collet's repository, the distribution's own MariaDB 10.11, and Apache or nginx with PHP-FPM. Everything you need is on this page.

A full install from the command line was run in a Rocky Linux 10 container with these packages behind this Apache configuration, and nginx accepted this nginx configuration. A container has no SELinux, firewall or systemd, so those steps are written from the distribution's documentation and were not run. Remi's repository was tried on Rocky Linux, not on Red Hat Enterprise Linux itself.

For another system, see [Installing a hub](01-installing.md).

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

## 4. Create the database

The hub needs a database and an account that owns it. MariaDB's `root`
account signs in through the operating system rather than with a password,
so create them with `sudo`:

```bash
sudo mariadb
```

```sql
CREATE DATABASE hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'hub'@'localhost' IDENTIFIED BY 'a-long-random-password';
GRANT ALL PRIVILEGES ON hub.* TO 'hub'@'localhost';
```

The usage statistics keep a second database of their own, named after the
first with `_metrics` on the end. A hub runs without it, but its usage pages
stay empty. If you want them, create it now as well:

```sql
CREATE DATABASE hub_metrics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON hub_metrics.* TO 'hub'@'localhost';
```

The browser installer can also create the database and account itself, if you
give it an administrator account that has a password and the right to grant
privileges. Creating them by hand, as above, needs neither.


## 5. Get the code

The repository is the hub: its top directory is the web server's document
root. Clone it, and give it to `apache`, the account PHP-FPM runs as:

```bash
sudo git clone --branch 2.4-main https://github.com/hubzero/hubzero-cms.git /var/www/hub
sudo chown -R apache:apache /var/www/hub
```

The installer writes the hub's configuration into a new `app/` directory
inside it, and the hub later keeps its uploads, logs and cache there, so
`apache` has to be able to write to the whole tree. Run every command
that changes the hub as `apache` (`sudo -u apache ...`), so
nothing ends up owned by an account the web server cannot write as.

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

## 6. Configure the web server

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

## 7. Install the hub

Two installers do the same job: one on the command line, one in the browser.
Both load the database, write the configuration into `app/config/`, bring
the database up to date and create the administrator. Use one of the three
ways below.

### From the command line

```bash
cd /var/www/hub
sudo -u apache php core/bin/muse install
```

`muse install` asks its questions one at a time and checks each answer before
moving on. In order:

1. **Composer.** If the libraries are not installed yet, it installs them
   first, which takes a minute or two. A warning that Composer *"Cannot create
   cache directory"* is harmless: the account has no home of its own to keep
   a cache in, so Composer downloads without one.
2. **Requirements.** The PHP version, each extension, `unzip`, and whether it
   can write to the hub's directory. It lists every check, and stops if any of
   them failed.
3. **Site settings.** The hub's name, the administrator's email address, the
   address it is served at (`https://hub.example.org`), the time zone, and a
   description for search engines.
4. **Database.** It looks for an account it can use without asking, then
   offers to use an existing account or to create one with an
   administrator's credentials. Choose the existing account and give it the
   database, user and password you created. 
5. **Schema.** It creates about 380 tables and loads the data every hub
   needs, then offers **sample data**: a front page, menus and articles to
   look at. Answer no for a hub you are going to fill yourself.
6. **Migrations.** It brings the database up to date, which takes a minute or
   so. Four warnings are expected; see
   [Warnings during the migrations](#warnings-during-the-migrations).
7. **Administrator.** A name, a username, an email address and a password of
   at least eight characters.

It ends by printing the site's address and the administrator's username.

### Or from the command line, with an answer file

`muse install` can take every answer from a file instead of asking for them,
which is how to install a hub from a script, or the same hub more than once.
The file is JSON:

```json
{
    "site": {
        "sitename": "My Hub",
        "mailfrom": "admin@hub.example.org",
        "live_site": "https://hub.example.org",
        "offset": "America/New_York"
    },
    "database": {
        "host": "127.0.0.1",
        "database": "hub",
        "username": "hub",
        "password": "a-long-random-password"
    },
    "admin": {
        "name": "Ada Administrator",
        "username": "admin",
        "email": "admin@hub.example.org",
        "password": "another-long-password"
    },
    "sample": false
}
```

| Section | Keys |
|---|---|
| `site` | `sitename`, `mailfrom` (the administrator's address), `live_site` (the hub's address; leave it out to have the hub work it out from each request), `offset` (a time zone such as `UTC` or `Europe/London`), and optionally `MetaDesc`, the description for search engines |
| `database` | `host`, `database`, `username`, `password`, and optionally `port`, `socket` and `prefix` (the table prefix, `jos_` unless you say otherwise) |
| `admin` | `name`, `username`, `email`, `password` (at least eight characters) |
| `sample` | `true` to load the sample data, `false` not to |

Keep the file outside the hub's directory, where only `apache` can read it:
it holds two passwords.

```bash
sudo chown apache /var/www/hub-install.json
sudo chmod 600 /var/www/hub-install.json
cd /var/www/hub
sudo -u apache php core/bin/muse install --config=/var/www/hub-install.json
sudo rm /var/www/hub-install.json
```

It runs every step the questions would, Composer included, and stops at the
first answer it cannot use, saying which. It never creates the database or its
account: create those first. Any single step takes the file too, so one can
be repeated on its own, for example `muse install admin --config=...`.

### Or from the browser

Open `https://hub.example.org/`. A hub that is not installed yet answers every
address with its installer.

The first thing it asks is that you prove you can change files on the server,
so that nobody who merely finds the address can install a hub on it. It shows
the name of a file to create in the hub's directory:

![The installer's ownership check, showing the name of the file to create, the directory to create it in, a ten-minute countdown, and the touch command that creates it](media/install-verify.png)

Create it within ten minutes, then select **Verify & Continue**:

```bash
sudo -u apache touch /var/www/hub/hubzero-verify-XXXXXXXX.txt
```

The installer removes the file once it has seen it.

If the libraries are not installed yet, the next page is **Hubzero Bootstrap
Setup**. It checks what Composer needs and has a **Run Composer** button,
which installs the libraries and shows Composer's output as it goes. When it
reports **Success!**, select **Continue**.

![The bootstrap page after Composer has run: its requirements all passed, a success message, and the end of Composer's output above a Continue button](media/install-composer.png)

The installation wizard follows. It moves through **Verify**, **Welcome**,
**Requirements**, **Database**, **Settings**, **Schema**, **Admin** and
**Complete**, and goes straight to the first step that still needs doing, so
after the bootstrap page it usually opens at **Database**.

**Database.** Choose how to reach MariaDB: **Standard (localhost)** connects to `127.0.0.1` over TCP; **Unix Socket** uses MariaDB's socket file. Then choose **I have credentials ready** and give
it the database name, user and password you created. **Test Connection**
checks them without moving on.

![The Database step's credentials page with the database name, username and password filled in, and Test Connection, Back and Next buttons](media/install-database.png)

**Settings.** The hub's name, the administrator's email address, the time
zone, and a description for search engines. Leave **Site URL** blank to have
the hub work out its address from each request, or give it, which the
scheduled tasks need later.

**Schema.** Tick **Load sample data** if you want a front page, menus and
articles to look at, then select **Load Database**. It creates the tables,
loads the data and runs the migrations, showing their output as they go. This
takes a minute or two; leave the page open until it says **Success!**, then
select **Continue**.

![The Schema step after it has finished: schema, base data, sample data and migrations all ticked, a success message, and the end of the migration output](media/install-schema.png)

**Admin.** The administrator's username is always `admin`. Give a name, an
email address and a password of at least eight characters, then select
**Create Account**. **Complete** follows, with the address of the
administrator interface.

![The Admin step: the fixed username admin, and fields for the full name, email address, password and its confirmation](media/install-admin.png)

Once the hub is installed, the installer refuses to change anything.
`/install` still answers, behind the same ownership check, but every step is
read-only.

## 8. After installing

**The welcome page.** Until you say otherwise, every page of the new hub shows
a welcome page that points at this documentation. Select **Jump to Your Hub**
on it to switch the hub to its real template. With sample data, that takes
you to a Getting Started article; without it, to the hub's front page.

**Sign in.** The administrator interface is at
`https://hub.example.org/administrator`. The same account signs in to the
site at `/login`.

**Scheduled tasks.** Nothing that runs on a schedule happens until the server
ticks the hub every minute. Add this to `/etc/crontab`:

```
* * * * * apache /var/www/hub/core/bin/muse cron:jobs tick
```

It needs the hub's address in `live_site` in `app/config/app.php`, which the
browser installer leaves blank unless you filled in **Site URL**. What the
tick does, and the jobs it runs, are in
[Scheduled tasks](../managers/03-maintenance/05-cron.md#how-it-works).

**Mail.** The hub sends mail with PHP's `mail()` until you configure
something else. See
[Mail Settings](../managers/05-configuring/01-hub.md#mail-settings).

**Time zones in the database.** MariaDB starts without its time zone tables.
The migrations load them only when they are run by the system's `root`,
which the installers are not. Load them once yourself:

```bash
mariadb-tzinfo-to-sql /usr/share/zoneinfo | sudo mariadb mysql
```

**Usage statistics.** If you created the `_metrics` database after
installing, run the migrations again to set it up. Only the ones that did
not finish run again:

```bash
cd /var/www/hub
sudo -u apache php core/bin/muse migration run -f
```

**Keeping it up to date.** [Upgrading a hub](12-upgrading.md) brings the hub
up to the latest code on its branch, with a backup first and a way back.

Then go on to
[The first week with a new hub](../managers/01-getting-started.md#the-first-week-with-a-new-hub).

## Warnings during the migrations

Both installers show the same four warnings on a new hub. None of them stops
the hub working:

| Warning | Why |
|---|---|
| *onBeforeMigrate hook 'UpdateTimezoneDatabase.php' ... Insufficient privileges to update timezone tables* | Loading MariaDB's time zone tables needs its `root` account. Load them yourself, as described above |
| *Migration20150330124145Core.php: Unable to write out new configuration file* | It updates a configuration file from an older version of the hub, which a new hub does not have |
| *Migration20150904235936ComUsage.php: Failed to run with the necessary privileges* | The `_metrics` database does not exist, and creating one needs `root`. Create it as described above and run the migrations again |
| *Migration20170627153900ComHubgraph.php: Failed to run with the necessary privileges* | It removes database triggers that older hubs had. A new hub has none to remove |

A migration that ended in a warning is tried again the next time the
migrations run; one that succeeded is not.

## When something goes wrong

**The administrator sign-in page does nothing, or the browser shows a
connection error after signing in.** The hub is being served over plain HTTP.
The administrator sign-in form posts to `https://`; serve the hub over HTTPS.

**`/administrator` answers 404 under nginx.** The PHP block is passing every
`.php` address straight to PHP. Use the block above, which
hands addresses that are not files to `index.php`.

**A page says the file or directory is not writable, or images and uploads
fail.** Something in `/var/www/hub` belongs to an account the web server does
not run as, usually because a command was run as yourself or as `root`. Run
`sudo chown -R apache:apache /var/www/hub` again. If the owner is right, SELinux is the next suspect: see
[SELinux](#selinux).

**The browser installer's migrations stop with *Connection error*.** The
migrations may still have finished. Reload the page; if it still lists
migrations as pending, finish them from the command line with
`sudo -u apache php core/bin/muse migration run -f`.

**Requests for `/app/config/database.php` do not answer 403.** The web
server's rules for private paths are missing. Check before the hub is in use:

```bash
curl -s -o /dev/null -w "%{http_code}\n" https://hub.example.org/app/config/database.php
```
