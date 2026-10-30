<!--
status: rewritten
reviewed-against: 2.4-main @ 4b8a132fb4
reviewed: 2026-10-30
screenshots: ok
summary: Installing a hub on Ubuntu 26.04 LTS, which has no PHP 8.2 packages, in FrankenPHP's PHP 8.2 Docker image with MariaDB 11.8.
-->
# Installing on Ubuntu 26.04 LTS

This page takes a server running Ubuntu 26.04 LTS with nothing on it to a hub
that answers at its own address and has an administrator who can sign in.
Ubuntu 26.04 ships PHP 8.5, and Ondřej Surý's PPA, which packages PHP 8.2 for
earlier Ubuntu releases, has none for it. So the hub runs in FrankenPHP's
PHP 8.2 Docker image: FrankenPHP is a web server with PHP built in, and the
image brings PHP 8.2, so the server needs none. MariaDB is Ubuntu's own,
11.8. Everything you need is on this page.

The PPA was checked for Ubuntu 26.04 and has no packages for it. Full installs in this image, from the browser and the command line, were run on an Ubuntu 24.04 server, and a hub was installed against MariaDB 11.8.

For another system, see [Installing a hub](01-installing.md).

## What you need

- A server running Ubuntu 26.04 LTS, and an account on it that can use `sudo`.
- Nothing else listening on ports 80 and 443: FrankenPHP is the web server.
- A name for the hub, such as `hub.example.org`, pointing at the server, and a
  TLS certificate for it. A hub has to be served over HTTPS: the administrator
  sign-in form always posts to an `https://` address, so on plain HTTP the
  administrator can never sign in.

## 1. Install MariaDB, the tools and Docker

```bash
sudo apt install mariadb-server git unzip docker.io
```

MariaDB is Ubuntu's own, 11.8. Install no PHP and no web server: the image
brings both.

## 2. Create the database

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


## 3. Get the code

The repository is the hub: its top directory is the document root. Clone it,
and give it to user and group 33, which is the `www-data` account inside the
image the hub runs in:

```bash
sudo git clone --branch 2.4-main https://github.com/hubzero/hubzero-cms.git /var/www/hub
sudo chown -R 33:33 /var/www/hub
```

On Ubuntu that is the `www-data` account on the server too.

## 4. Build the image

FrankenPHP is a web server with PHP built in, and its PHP 8.2 image is the
starting point. Add the extensions the hub needs that it does not already
have. Put this in `/opt/hub-frankenphp/Dockerfile`:

```dockerfile
FROM dunglas/frankenphp:1-php8.2-bookworm

# The extensions the hub needs that the image does not already have, and the
# tools Composer uses
RUN install-php-extensions pdo_mysql gd zip sockets opcache \
    && apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

# PHP's production settings, and a place for sessions outside the document
# root: the image has neither, and the installer keeps its secrets there
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && mkdir -p /var/lib/php/sessions \
    && chown www-data:www-data /var/lib/php/sessions \
    && echo 'session.save_path = "/var/lib/php/sessions"' > "$PHP_INI_DIR/conf.d/hub.ini"

# Run as the account that owns the hub's files, still able to listen on 80/443
RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
    && chown -R www-data:www-data /config/caddy /data/caddy

USER www-data
```

```bash
sudo docker build -t hub-frankenphp /opt/hub-frankenphp
```

The session directory is not optional. Without a writable place outside the
document root, the installer stops at a page headed *Server Configuration
Required* before it asks anything.

## 5. Write the Caddyfile

FrankenPHP reads a Caddyfile. Put the hub's in
`/opt/hub-frankenphp/Caddyfile`, with your own name for the hub:

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

It does the same four things every configuration for the hub does: serves
`/var/www/hub` over HTTPS, passes PHP to PHP, sends every address that is not
a file to `index.php` (`php_server` does both), and refuses the hub's
configuration, logs, temporary files, command-line programs and hidden files.
The last matters: `app/config/database.php` holds the database password.

FrankenPHP obtains and renews the certificate for the name itself, so there
are no certificate paths to give it. Its regular expressions have no
lookahead, which is why the hidden-file rule is a matcher of two lines.

## 6. Start the container

```bash
sudo docker run -d --name hub --restart unless-stopped --network host \
    -v /var/www/hub:/var/www/hub \
    -v /opt/hub-frankenphp/Caddyfile:/etc/frankenphp/Caddyfile:ro \
    -v hub_caddy_data:/data -v hub_caddy_config:/config \
    hub-frankenphp
```

- `--network host` lets it answer on the server's own ports 80 and 443, and
  reach MariaDB at `127.0.0.1` as if it were not in a container.
- The hub's directory is mounted at the same path inside, so paths the
  installer writes into the configuration are right on both sides. The
  container runs as user 33, which owns the files.
- `hub_caddy_data` keeps the certificates FrankenPHP obtains across restarts
  and rebuilds.

Every `php` command for the hub runs inside the container, as
`sudo docker exec -w /var/www/hub hub php ...`, with `-it` added when it asks
questions. MariaDB's socket is not inside the container, so the hub reaches
the database at `127.0.0.1`.

## 7. Install the hub

Two installers do the same job: one on the command line, one in the browser.
Both load the database, write the configuration into `app/config/`, bring
the database up to date and create the administrator. Use one of the three
ways below.

### From the command line

```bash
sudo docker exec -it -w /var/www/hub hub php core/bin/muse install
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
   database, user and password you created. Give the host as `127.0.0.1`.
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

The file holds two passwords, and has to be inside the container to be read.
Copy it in, use it, and remove both copies:

```bash
sudo docker cp /var/www/hub-install.json hub:/tmp/hub-install.json
sudo docker exec -u root hub chown www-data /tmp/hub-install.json
sudo docker exec -u root hub chmod 600 /tmp/hub-install.json
sudo docker exec -w /var/www/hub hub php core/bin/muse install --config=/tmp/hub-install.json
sudo docker exec -u root hub rm /tmp/hub-install.json
sudo rm /var/www/hub-install.json
```

It runs every step the questions would, Composer included, and stops at the
first answer it cannot use, saying which. It never creates the database or
its account: create those first.

### Or from the browser

Open `https://hub.example.org/`. A hub that is not installed yet answers every
address with its installer.

The first thing it asks is that you prove you can change files on the server,
so that nobody who merely finds the address can install a hub on it. It shows
the name of a file to create in the hub's directory:

![The installer's ownership check, showing the name of the file to create, the directory to create it in, a ten-minute countdown, and the touch command that creates it](media/install-verify.png)

Create it within ten minutes, then select **Verify & Continue**:

```bash
sudo touch /var/www/hub/hubzero-verify-XXXXXXXX.txt
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

**Database.** Choose **Standard (localhost)**, which connects to `127.0.0.1` over TCP. Then choose **I have credentials ready** and give
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
select **Continue**. The output also carries warnings that session settings *"cannot be
changed after headers have already been sent"*: they come from FrankenPHP's own
command line, which the installer runs the migrations with, and are harmless.

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
* * * * * root docker exec -w /var/www/hub hub php core/bin/muse cron:jobs tick
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
sudo docker exec -w /var/www/hub hub php core/bin/muse migration run -f
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

**The installer shows *Server Configuration Required*.** PHP in the container
has no writable place outside the document root. The image was built without
the `session.save_path` lines in the Dockerfile; add them and rebuild.

**The installer cannot connect to the database through the socket.**
MariaDB's socket is not inside the container. Use `127.0.0.1`.

**The container will not start, saying an address is already in use.**
Something else on the server already listens on port 80 or 443, or on 2019,
where FrankenPHP takes its own administration requests. Stop it, or move the
hub to a server of its own.

**A page says the file or directory is not writable.** Something in
`/var/www/hub` belongs to an account other than 33. Run
`sudo chown -R 33:33 /var/www/hub` again.

**The browser installer's migrations stop with *Connection error*.** The
migrations may still have finished. Reload the page; if it still lists
migrations as pending, finish them with
`sudo docker exec -w /var/www/hub hub php core/bin/muse migration run -f`.

**Requests for `/app/config/database.php` do not answer 403.** The web
server's rules for private paths are missing. Check before the hub is in use:

```bash
curl -s -o /dev/null -w "%{http_code}\n" https://hub.example.org/app/config/database.php
```
