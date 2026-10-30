<!--
status: rewritten
reviewed-against: 2.4-main @ 4b8a132fb4
reviewed: 2026-10-30
screenshots: none
summary: Installing a hub on a Linux distribution the other pages do not name, from what it needs rather than from package names.
-->
# Installing on any other Linux

This page is for a Linux distribution the other installation pages do not
name. It says what a hub needs rather than which packages to install, because
those are the distribution's to name. The steps are the same as on every
other system; only the package names and paths are yours to find.

If your distribution has no PHP 8.2 and no second source of it, run the hub
in Docker instead: [Installing with FrankenPHP in Docker](10-docker.md)
needs no PHP on the server at all.

## What you need

- A Linux server, and an account on it that can use `sudo`.
- A name for the hub, such as `hub.example.org`, pointing at the server, and a
  TLS certificate for it. A hub has to be served over HTTPS: the administrator
  sign-in form always posts to an `https://` address, so on plain HTTP the
  administrator can never sign in.

## 1. Install MariaDB, PHP and the tools

Install, from the distribution or a repository that packages them for it:

- **MariaDB.** The hub was installed against 10.11 and 11.8. MySQL is
  expected to work but was not tried.
- **PHP 8.2**, both its command line and PHP-FPM, with these extensions in
  whatever packages they come in: `pdo_mysql`, `mbstring`, `curl`, `gd`,
  `zip`, `fileinfo`, `openssl`, the XML family (`dom`, `simplexml`,
  `xmlreader`, `xmlwriter`), `sockets` and `posix`. If the distribution ships
  another version of PHP, look for a second repository that packages 8.2 for
  it, as Debian, Ubuntu and Enterprise Linux each have.
- **`git` and `unzip`.**

Start MariaDB and PHP-FPM, and have them start at boot.

Check PHP. This lists any extension the hub needs that is missing, and prints
nothing when all of them are there:

```bash
php -r 'foreach (["pdo_mysql","mbstring","curl","gd","zip","fileinfo","openssl","dom","simplexml","xmlreader","xmlwriter","sockets","posix","json","ctype","iconv","zlib"] as $e) if (!extension_loaded($e)) echo "missing: $e\n";'
```

Then find three things the later steps depend on:

1. **The account PHP-FPM runs as.** It is the `user` line of PHP-FPM's pool
   configuration, usually a file named `www.conf`. The hub's files belong to
   that account, and every command below that changes the hub runs as it.
   This page calls it *the PHP-FPM account*.
2. **The socket PHP-FPM listens on.** It is the `listen` line of the same
   file. The web server's configuration names it.
3. **Where PHP keeps sessions.** `session.save_path`, in PHP-FPM's
   `php.ini` or pool. It has to be a directory the PHP-FPM account can write
   to, outside the hub's directory: the installer keeps its secrets there, and
   stops at a page headed *Server Configuration Required* without one.

## 2. Install the web server

Choose Apache **or** nginx. Install one of them, not both.

**Apache**, with `mod_proxy_fcgi`, `mod_setenvif` and `mod_ssl` enabled.

**Or nginx.**

If the distribution starts with its firewall closed, open ports 80 and 443.

## 3. Create the database

The hub needs a database and an account that owns it. Connect to MariaDB as
its administrator (`sudo mariadb` on most distributions) and create them:

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

## 4. Get the code

The repository is the hub: its top directory is the web server's document
root. Clone it, and give it to the PHP-FPM account:

```bash
sudo git clone --branch 2.4-main https://github.com/hubzero/hubzero-cms.git /var/www/hub
sudo chown -R <the PHP-FPM account>: /var/www/hub
```

The installer writes the hub's configuration into a new `app/` directory
inside it, and the hub later keeps its uploads, logs and cache there, so the
PHP-FPM account has to be able to write to the whole tree.

Now the installer's own check covers everything else it needs, without
changing anything:

```bash
cd /var/www/hub
sudo -u <the PHP-FPM account> php core/bin/muse install check
```

If the distribution runs SELinux or AppArmor, the web server needs to be
allowed to write to `/var/www/hub`, to connect to MariaDB and to the network,
and to send mail. On SELinux, the commands are the ones
[Enterprise Linux 9](08-enterprise-linux-9.md#selinux) uses.

## 5. Configure the web server

Every configuration for the hub does the same four things:

- serves `/var/www/hub` as the document root, over HTTPS;
- passes PHP files to PHP;
- sends every address that is not a file to `index.php`, which is how the
  hub's addresses work;
- refuses requests for the hub's configuration, logs, temporary files,
  command-line programs and hidden files.

The last of those matters: `app/config/database.php` holds the database
password, and nothing else stops a browser from reading it.

Start from the configuration for your web server on
[Debian 12](02-debian-12.md#5-configure-the-web-server), and change only what
is your distribution's:

- where the file goes, and whether it needs enabling;
- the PHP-FPM socket, in `SetHandler` for Apache or `fastcgi_pass` for nginx;
- the log locations, and the certificate paths.

Keep the rest as it is: each of its rules is one of the four things above,
and the hub needs all four. Under nginx, do not route PHP through a
distribution snippet that answers 404 for a `.php` address with no file
behind it: `/administrator/index.php` is answered by the hub's own
`index.php`.

## 6. Install the hub

Use either installer, as the PHP-FPM account. The command line:

```bash
cd /var/www/hub
sudo -u <the PHP-FPM account> php core/bin/muse install
```

or an answer file, or the browser. Each is described step by step on
[Debian 12](02-debian-12.md#6-install-the-hub), and works the same way here,
with the PHP-FPM account in place of `www-data`.

## 7. After installing

Everything on [Debian 12](02-debian-12.md#7-after-installing) applies,
with the PHP-FPM account in place of `www-data`: the welcome page, the cron
entry for scheduled tasks, mail, the database's time zones, and the usage
statistics. Then check that the private paths are refused before the hub is
in use:

```bash
curl -s -o /dev/null -w "%{http_code}\n" https://hub.example.org/app/config/database.php
```

It should print `403`.
