<!--
status: rewritten
reviewed-against: 2.4-main @ 6dd778fb19
reviewed: 2026-10-29
screenshots: ok
summary: Installing a hub on a Linux server with MariaDB, behind Apache, nginx or FrankenPHP, using either the browser installer or muse install.
-->
# Installing a hub

This page takes a Linux server with nothing on it to a hub that answers at
its own address and has an administrator who can sign in. It uses MariaDB for
the database and gives three ways to serve the hub: Apache with PHP-FPM, nginx
with PHP-FPM, or FrankenPHP. The installation itself can be run from the
browser or from the command line. Every combination gives the same hub, so
choose the web server you already know how to run, and whichever installer
suits you.

What it installs is the CMS. The parts of a hub that are not the CMS — the
tool session middleware, Workspaces, the mail gateway, LDAP and Solr — are
separate services, and none of them is needed for a hub to run. Search on
Solr is covered in [Search](09-components/31-search/README.md).

If the hub you are asking about is already installed and answering, this is
the wrong page. Go to
[The first week with a new hub](01-getting-started.md#the-first-week-with-a-new-hub),
which starts from a hub that runs and walks the decisions that come next in
order.

The commands below were tested on Ubuntu 24.04 with MariaDB 10.11, PHP 8.3,
Apache 2.4, nginx 1.24 and FrankenPHP 1.11. On another distribution the
package names differ; the steps do not.

## What you need

- A Linux server. Hubzero does not run on Windows; the installer refuses to
  start there.
- A name for the hub, such as `hub.example.org`, pointing at the server, and a
  TLS certificate for it. A hub has to be served over HTTPS: the administrator
  sign-in form always posts to an `https://` address, so on plain HTTP the
  administrator can never sign in.
- MariaDB, or MySQL.
- `unzip` and `git`. Composer itself ships in the repository as
  `core/bin/composer`; you do not install it separately.
- PHP, with the extensions the installer checks for.

The PHP version and extensions, read here from the installer's own
requirements check:

<!--include: core/libraries/Hubzero/System/Requirements.php:25-40-->

## 1. Install the packages

The database, PHP and the tools:

```bash
sudo apt install mariadb-server git unzip \
    php8.3-cli php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-curl \
    php8.3-gd php8.3-zip php8.3-xml
```

`php8.3-xml` is not on the installer's list, but the libraries Composer
installs need it. `json`, `openssl`, `fileinfo` and `pdo` come with PHP itself.

Then the web server. Install one of them:

- **Apache:** `sudo apt install apache2`, then
  `sudo a2enmod proxy_fcgi setenvif ssl`.
- **nginx:** `sudo apt install nginx`.
- **FrankenPHP:** download the binary from
  [frankenphp.dev](https://frankenphp.dev/). It is a web server with its own
  PHP built in, so it does not use `php8.3-fpm`, and it serves HTTPS with a
  certificate it obtains itself. Its PHP already has every extension listed
  above.

## 2. Create the database

Hubzero needs a database and an account that owns it. On Ubuntu, MariaDB's
`root` account signs in through the operating system rather than with a
password, so create them with `sudo`:

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

## 3. Get the code

The repository is the hub: its top directory is the web server's document
root. Clone it, and give it to the account the web server runs PHP as, which
on Ubuntu is `www-data`:

```bash
sudo git clone --branch 2.4-main https://github.com/hubzero/hubzero-cms.git /var/www/hub
sudo chown -R www-data:www-data /var/www/hub
```

The installer writes the hub's configuration into a new `app/` directory
inside it, and the hub later keeps its uploads, logs and cache there, so the
web server's account has to be able to write to the whole tree. Run every
command that changes the hub as that account (`sudo -u www-data ...`), so
nothing ends up owned by someone the web server cannot write as.

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
examples use certificates from Let's Encrypt's `certbot`.

### Apache with PHP-FPM

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
        SetHandler "proxy:unix:/run/php/php8.3-fpm.sock|fcgi://localhost"
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

### nginx with PHP-FPM

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
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_read_timeout 300;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/hub /etc/nginx/sites-enabled/hub
sudo nginx -t && sudo systemctl reload nginx
```

Do not use Ubuntu's `snippets/fastcgi-php.conf` for this block. It answers
404 for any `.php` address that is not a file, and the administrator
interface lives at one: `/administrator/index.php` is answered by the hub's
`index.php`, not by a file of that name. Raise `client_max_body_size` if
members will upload files larger than 100 MB.

### FrankenPHP

FrankenPHP reads a Caddyfile. Give it its own, such as
`/etc/frankenphp/Caddyfile`:

```caddyfile
{
	frankenphp
	order php_server before file_server
}

hub.example.org {
	root * /var/www/hub

	@private path_regexp ^/(app/(config|logs|tmp)|core/(bin|vendor/bin))(/|$)
	@dotfiles {
		path_regexp /\.
		not path /.well-known/*
	}
	respond @private 403
	respond @dotfiles 403

	php_server
}
```

```bash
sudo -u www-data frankenphp run --config /etc/frankenphp/Caddyfile
```

FrankenPHP obtains and renews the certificate for `hub.example.org` itself,
so there are no certificate paths to give it. Its regular expressions have no
lookahead, which is why the hidden-file rule is a matcher of two lines rather
than the single pattern Apache and nginx use. Run it as the account that owns
the hub's files, and under a service manager rather than a terminal once the
hub is in use; FrankenPHP's own documentation describes a systemd unit.

FrankenPHP's PHP looks for MariaDB's socket in `/tmp`, where Ubuntu does not
put it. When an installer asks how to reach the database, choose
`127.0.0.1` rather than `localhost` or the socket. The account created above
accepts either.

## 5. Install

Two installers do the same job: one in the browser, one on the command line.
Both load the database, write the configuration into `app/config/`, bring the
database up to date and create the administrator.

### From the command line

```bash
cd /var/www/hub
sudo -u www-data php core/bin/muse install
```

With FrankenPHP, use its own PHP: `sudo -u www-data frankenphp php-cli
core/bin/muse install`. It prints warnings that session settings *"cannot be
changed after headers have already been sent"*. They are harmless: FrankenPHP's
command line reports headers as sent before anything has been, which PHP's own
does not.

`muse install` asks its questions one at a time and checks each answer before
moving on. In order:

1. **Composer.** If the libraries are not installed yet, it installs them
   first, which takes a minute or two. A warning that Composer *"Cannot create
   cache directory"* is harmless: `www-data` has no home of its own to keep
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
   database, user and password from step 2.
5. **Schema.** It creates about 380 tables and loads the data every hub
   needs, then offers **sample data**: a front page, menus and articles to
   look at. Answer no for a hub you are going to fill yourself.
6. **Migrations.** It brings the database up to date, which takes a minute or
   so. Four warnings are expected; see
   [Warnings during the migrations](#warnings-during-the-migrations).
7. **Administrator.** A name, a username, an email address and a password of
   at least eight characters.

It ends by printing the site's address and the administrator's username.

### From the command line, without questions

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

```bash
sudo -u www-data php core/bin/muse install --config=/var/www/hub-install.json
```

It runs every step the questions would, Composer included on a fresh
checkout, and stops at the first answer it cannot use, saying which. It never
creates the database or its account: create those first, as in step 2. Any
single step takes the file too, so one can be repeated on its own, for
example `muse install admin --config=...`.

The file holds two passwords. Keep it outside the hub's directory, where only
the hub's account can read it, and delete it once the hub is installed:

```bash
sudo chown www-data /var/www/hub-install.json && sudo chmod 600 /var/www/hub-install.json
```

### From the browser

Open `https://hub.example.org/`. A hub that is not installed yet answers every
address with its installer.

The first thing it asks is that you prove you can change files on the server,
so that nobody who merely finds the address can install a hub on it. It shows
the name of a file to create in the hub's directory:

![The installer's ownership check, showing the name of the file to create, the directory to create it in, a ten-minute countdown, and the touch command that creates it](media/install-verify.png)

Create it as the account that owns the hub, within ten minutes, then select
**Verify & Continue**:

```bash
sudo -u www-data touch /var/www/hub/hubzero-verify-XXXXXXXX.txt
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

**Database.** Choose how to reach MariaDB: **Standard (localhost)** connects
to `127.0.0.1` over TCP; **Unix Socket** uses MariaDB's socket file. Then
choose **I have credentials ready** and give it the database name, user and
password from step 2. **Test Connection** checks them without moving on.

![The Database step's credentials page with the database name, username and password filled in, and Test Connection, Back and Next buttons](media/install-database.png)

**Settings.** The hub's name, the administrator's email address, the time
zone, and a description for search engines. Leave **Site URL** blank to have
the hub work out its address from each request, or give it, which
`muse cron:jobs tick` needs later.

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

## 6. After installing

**The welcome page.** Until you say otherwise, every page of the new hub shows
a welcome page that points at this documentation. Select **Jump to Your Hub**
on it to switch the hub to its real template. With sample data, that takes
you to a Getting Started article; without it, to the hub's front page.

**Sign in.** The administrator interface is at
`https://hub.example.org/administrator`. The same account signs in to the
site at `/login`.

**Scheduled tasks.** Nothing that runs on a schedule happens until the server
ticks the hub every minute. Add the cron entry described in
[Scheduled tasks](03-maintenance/05-cron.md#how-it-works). It needs the
hub's address in `live_site` in `app/config/app.php`. The browser installer
leaves that blank unless you filled in **Site URL**.

**Mail.** The hub sends mail with PHP's `mail()` until you configure
something else. See [Mail Settings](05-configuring/01-hub.md#mail-settings).

**Time zones in the database.** MariaDB on Ubuntu starts without its time
zone tables. The migrations load them only when they are run by the system's
`root`, which the installers are not. Load them once yourself:

```bash
mariadb-tzinfo-to-sql /usr/share/zoneinfo | sudo mariadb mysql
```

**Usage statistics.** If you created the `_metrics` database after
installing, run the migrations again to set it up. Only the ones that did
not finish run again:

```bash
cd /var/www/hub
sudo -u www-data php core/bin/muse migration run -f
```

**Keeping it up to date.** [Upgrading a hub](03-maintenance/06-upgrading.md)
brings the hub up to the latest code on its branch, with a backup first and a
way back.

Then go on to
[The first week with a new hub](01-getting-started.md#the-first-week-with-a-new-hub).

## Warnings during the migrations

Both installers show the same four warnings on a new hub. None of them stops
the hub working:

| Warning | Why |
|---|---|
| *onBeforeMigrate hook 'UpdateTimezoneDatabase.php' ... Insufficient privileges to update timezone tables* | Loading MariaDB's time zone tables needs its `root` account. Load them yourself, as described above |
| *Migration20150330124145Core.php: Unable to write out new configuration file* | It updates a configuration file from an older version of the hub, which a new hub does not have |
| *Migration20150904235936ComUsage.php: Failed to run with the necessary privileges* | The `_metrics` database does not exist, and creating one needs `root`. Create it as in step 2 and run the migrations again |
| *Migration20170627153900ComHubgraph.php: Failed to run with the necessary privileges* | It removes database triggers that older hubs had. A new hub has none to remove |

A migration that ended in a warning is tried again the next time the
migrations run; one that succeeded is not.

## When something goes wrong

**The administrator sign-in page does nothing, or the browser shows a
connection error after signing in.** The hub is being served over plain HTTP.
The administrator sign-in form posts to `https://`; serve the hub over HTTPS.

**`/administrator` answers 404 under nginx.** The PHP block is passing every
`.php` address straight to PHP, or uses Ubuntu's `fastcgi-php.conf`. Use the
block above, which hands addresses that are not files to `index.php`.

**A page says the file or directory is not writable, or images and uploads
fail.** Something in `/var/www/hub` belongs to an account the web server does
not run as, usually because a command was run as yourself or as `root`. Run
`sudo chown -R www-data:www-data /var/www/hub` again.

**The browser installer's migrations stop with *Connection error*.** The
migrations may still have finished. Reload the page; if it still lists
migrations as pending, finish them from the command line with
`sudo -u www-data php core/bin/muse migration run -f`.

**Under FrankenPHP, the installer cannot connect to the database through
`localhost`.** Use `127.0.0.1`, as described under [FrankenPHP](#frankenphp).

**Requests for `/app/config/database.php` do not answer 403.** The web
server's rules for private paths are missing. Check before the hub is in use:

```bash
curl -s -o /dev/null -w "%{http_code}\n" https://hub.example.org/app/config/database.php
```
