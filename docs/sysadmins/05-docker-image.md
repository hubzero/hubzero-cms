<!--
status: rewritten
reviewed-against: 2.4-main @ 5d7f20276e
reviewed: 2026-10-30
screenshots: none
summary: A hub built into a Docker image of its own, beside MariaDB's image: built from the release branch, installed from environment variables on its first start, upgraded by building it again.
-->
# A self-contained hub in Docker

This page builds the whole hub into one Docker image: PHP 8.2 and the web
server from FrankenPHP's image, the hub's code, and the libraries Composer
installs. MariaDB runs beside it in its own official image. The server needs
nothing but Docker.

The first time the hub's container starts, it installs the hub, taking its
answers from a file of settings. Every time after, it brings the database up
to date and serves the hub. Upgrading means building the image again from
newer code and starting a new container from it.

This differs from [Preparing a server for FrankenPHP in Docker](02-preparing/09-docker.md),
where the hub's code is a checkout on the server and the container only runs
PHP and the web server over it. Here the code is inside the image and nothing
can change it. Only what the hub writes is kept outside the image, in Docker
volumes: its configuration, uploads, logs and cache, and the database. You
do not follow [Installing the hub](03-installing-the-hub.md) or
[Upgrading a hub](04-upgrading.md); this page does both.

The image was built and run on an Ubuntu 24.04 server with Docker 29 and
MariaDB 10.11. That covered a first start that installs the hub, restarts and
a rebuilt image that bring it up to date, administrator sign-in, the
scheduled tasks, and the backup and restore below. It was served with a
certificate FrankenPHP made itself, not a real one.

## What you need

- A Linux server, and an account on it that can use `sudo`.
- Nothing else listening on ports 80 and 443: the hub's container is the web
  server.
- A name for the hub, such as `hub.example.org`, pointing at the server. A
  hub has to be served over HTTPS: the administrator sign-in form always
  posts to an `https://` address. FrankenPHP obtains the certificate itself
  once the name reaches the server. Until then, it can make one of its own;
  see [step 3](#3-write-the-image-s-files).

## 1. Install Docker and git

**On Debian or Ubuntu:**

```bash
sudo apt install docker.io git
```

**Or on Enterprise Linux 8, 9 or 10**, Docker from Docker's own repository,
which replaces the Podman Enterprise Linux installs by default:

```bash
sudo dnf install git
sudo dnf remove podman buildah runc
sudo dnf install dnf-plugins-core
sudo dnf config-manager --add-repo https://download.docker.com/linux/rhel/docker-ce.repo
sudo dnf install docker-ce docker-ce-cli containerd.io
sudo systemctl enable --now docker
sudo firewall-cmd --permanent --add-service=http --add-service=https
sudo firewall-cmd --reload
```

Turn SELinux off as well. Hubs run with it off and are not tested with it
on. On Enterprise Linux 8, `sudo setenforce 0` and then `SELINUX=disabled` in
`/etc/selinux/config`; on 9 and 10, `sudo setenforce 0` and then
`sudo grubby --update-kernel ALL --args selinux=0`. Reboot when it suits you;
`getenforce` should then print `Disabled`.

**Or on any other distribution**, its own Docker and `git` packages.

## 2. Get the code

Everything the image is built from goes in `/opt/hub-image`. Start with the
hub's code, cloned from its release branch:

```bash
sudo mkdir /opt/hub-image
sudo git clone --branch 2.4-main https://github.com/hubzero/hubzero-cms.git /opt/hub-image/hubzero-cms
```

The checkout stays on the server only to build from. The hub runs from the
copy inside the image.

## 3. Write the image's files

Four files go beside the checkout, in `/opt/hub-image`.

**`Dockerfile`**, which builds the image:

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

# The hub's code, and the libraries it needs. It belongs to root, so the web
# server cannot change it; only app/, where the hub keeps everything it
# writes, belongs to www-data.
COPY hubzero-cms /var/www/hub
WORKDIR /var/www/hub
RUN php core/bin/muse install vendor \
    && mkdir app \
    && chown www-data:www-data app

COPY Caddyfile /etc/frankenphp/Caddyfile
COPY hub-start /usr/local/bin/hub-start

# Run as www-data, still able to listen on 80 and 443
RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
    && chown -R www-data:www-data /config/caddy /data/caddy

USER www-data
VOLUME /var/www/hub/app

CMD ["hub-start"]
```

**`.dockerignore`**, which keeps the checkout's history, and any `app/`
directory, out of the image:

```
hubzero-cms/.git
hubzero-cms/app
```

**`Caddyfile`**, FrankenPHP's configuration. It is the one the other Docker
page uses, except that it takes the hub's name from the container's
settings:

```caddyfile
{
	frankenphp
	order php_server before file_server
}

{$SERVER_NAME} {
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

It serves the hub over HTTPS and refuses the hub's configuration, logs,
temporary files, command-line programs and hidden files.
`app/config/database.php` holds the database password, so the last part matters.

Before the name reaches the server, or for a hub with no public name, add
`tls internal` inside the site block, on the line after `root`. FrankenPHP
then makes the certificate itself, from a local authority of its own, and
browsers warn about it. When the name reaches the server, take the line out,
then build the image and start the container again as in
[Upgrading](#upgrading). FrankenPHP then obtains a real certificate in its
place.

**`hub-start`**, what the container runs each time it starts:

```sh
#!/bin/sh
# Install the hub the first time the container starts, bring its database up
# to date every time after, then serve it
set -e

# MariaDB takes a few seconds to start the first time
php core/bin/muse install --env --wait=60

# The hub's scheduled tasks, every minute
while sleep 60; do
	php core/bin/muse cron:jobs tick || true
done &

exec frankenphp run --config /etc/frankenphp/Caddyfile
```

```bash
sudo chmod 755 /opt/hub-image/hub-start
```

`muse install --env` does the work. It takes the installer's answers from
the container's `HUB_*` settings instead of asking for them, after waiting up
to a minute for MariaDB to answer. On a hub that is already installed, it
only runs the migrations. If the install cannot
finish, the container stops before it serves anything. That matters because
a hub that is not installed answers every address with its installer.

And one more file, for MariaDB rather than the image.
**`metrics.sql`** creates the second database the usage statistics keep:

```sql
CREATE DATABASE hub_metrics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON hub_metrics.* TO 'hub'@'%';
```

MariaDB runs it once, the first time its container starts with an empty
database volume.

## 4. Build the image

```bash
sudo docker build -t hub /opt/hub-image
```

Building takes a few minutes the first time, most of them spent by Composer.
A warning that the legacy builder is deprecated is harmless.

## 5. Write the settings

Both containers read their settings from one file,
`/opt/hub-image/hub.env`. Put your own names and passwords in it. The
database password appears twice: once for MariaDB to create the account, and
once for the hub to use it.

```
# MariaDB: the database and account it creates the first time it starts
MARIADB_DATABASE=hub
MARIADB_USER=hub
MARIADB_PASSWORD=a-long-random-password
MARIADB_RANDOM_ROOT_PASSWORD=1

# The hub's name, for its certificate
SERVER_NAME=hub.example.org

# The installer's answers
HUB_SITENAME=My Hub
HUB_MAILFROM=admin@hub.example.org
HUB_LIVE_SITE=https://hub.example.org
HUB_TIMEZONE=America/New_York
HUB_DB_HOST=hub-db
HUB_DB_NAME=hub
HUB_DB_USER=hub
HUB_DB_PASSWORD=a-long-random-password
HUB_ADMIN_NAME=Ada Administrator
HUB_ADMIN_USERNAME=admin
HUB_ADMIN_EMAIL=admin@hub.example.org
HUB_ADMIN_PASSWORD=another-long-password
```

Write the values without quotation marks; Docker would keep them as part of
the value. The file holds passwords, so only root should be able to read it:

```bash
sudo chmod 600 /opt/hub-image/hub.env
```

Each `HUB_` setting answers one of the installer's questions, as a key in an
[answer file](03-installing-the-hub.md#or-from-the-command-line-with-an-answer-file)
would:

| Setting | Answer-file key | What it is |
|---|---|---|
| `HUB_SITENAME` | `site.sitename` | The hub's name |
| `HUB_MAILFROM` | `site.mailfrom` | The address the hub's mail comes from |
| `HUB_LIVE_SITE` | `site.live_site` | The hub's address. The scheduled tasks need it |
| `HUB_TIMEZONE` | `site.offset` | A time zone such as `UTC` or `Europe/London` |
| `HUB_DB_HOST` | `database.host` | `hub-db`, the name of MariaDB's container |
| `HUB_DB_PORT` | `database.port` | Only if it is not 3306 |
| `HUB_DB_NAME` | `database.database` | The database |
| `HUB_DB_USER` | `database.username` | Its account |
| `HUB_DB_PASSWORD` | `database.password` | The account's password |
| `HUB_DB_PREFIX` | `database.prefix` | The table prefix, `jos_` if left out |
| `HUB_ADMIN_NAME` | `admin.name` | The administrator's full name |
| `HUB_ADMIN_USERNAME` | `admin.username` | Their username |
| `HUB_ADMIN_EMAIL` | `admin.email` | Their email address |
| `HUB_ADMIN_PASSWORD` | `admin.password` | Their password, at least eight characters |
| `HUB_SAMPLE_DATA` | `sample` | `1` to load the sample data. Leave it out for a hub you will fill yourself |

They are read once, on the first start. Changing them after the hub is
installed changes nothing in the hub. Change its settings in its
administrator interface instead.

## 6. Start the hub

Make a network for the two containers. Start MariaDB, then the hub:

```bash
sudo docker network create hub

sudo docker run -d --name hub-db --network hub --restart unless-stopped \
    --env-file /opt/hub-image/hub.env \
    -v hub_db:/var/lib/mysql \
    -v /opt/hub-image/metrics.sql:/docker-entrypoint-initdb.d/metrics.sql:ro \
    mariadb:10.11

sudo docker run -d --name hub --network hub --restart unless-stopped \
    --env-file /opt/hub-image/hub.env \
    -p 80:80 -p 443:443 \
    --add-host hub.example.org:127.0.0.1 \
    -v hub_app:/var/www/hub/app \
    -v hub_caddy_data:/data -v hub_caddy_config:/config \
    hub
```

- `hub_db` keeps the database, and `hub_app` everything the hub writes.
  `hub_caddy_data` keeps the certificates FrankenPHP obtains. All three
  outlast the containers, which can be removed and started again from the
  image at any time.
- `--add-host` gives the hub's own name the address `127.0.0.1` inside its
  container, with your hub's name in place of `hub.example.org`. The
  scheduled tasks reach the hub at its own address. The hub accepts that
  request only from the server itself, and through Docker's port mapping it
  would come from somewhere else.

Watch the first start:

```bash
sudo docker logs -f hub
```

After waiting a few seconds for MariaDB, the installer runs its checks,
writes the configuration, creates about 380 tables, runs the migrations and
creates the administrator. Then it prints **Installation Complete!**, and
FrankenPHP's log lines follow. Press Ctrl+C to stop watching; the container
goes on.

None of the
[warnings during the migrations](03-installing-the-hub.md#warnings-during-the-migrations)
should appear: `metrics.sql` created the usage statistics' database, and
MariaDB's image loads its time zone tables itself.

A few minutes later, once the scheduled tasks have run, check the hub:

```bash
sudo docker exec hub php core/bin/muse install verify
```

It should report no failures, and a warning about mail: the image has no
mail server. See [Mail](#running-the-hub-s-commands) below. While the
Caddyfile says `tls internal`, it warns about the certificate too.

Open `https://hub.example.org/`, and sign in at
`https://hub.example.org/administrator` with the administrator's username and
password. Then go on to
[The first week with a new hub](../managers/01-getting-started.md#the-first-week-with-a-new-hub).

## Running the hub's commands

The hub's PHP is inside its container, so `muse` runs there, already as the
account that owns the hub's files:

```bash
sudo docker exec -it hub php core/bin/muse cache clear
```

`-it` is what lets `muse` ask its questions; leave it out in a script, which
has no terminal to give it.

The scheduled tasks need nothing more. `hub-start` ticks the hub every minute
inside the container, and each tick shows in `sudo docker logs hub`.

**Mail.** The image has no mail server, and PHP's `mail()` has nothing to
hand mail to. Until you give the hub an SMTP server, it cannot send mail;
see [Mail Settings](../managers/05-configuring/01-hub.md#mail-settings).

## Backing up

Two things hold the hub: the database, and the `hub_app` volume. Copy both
into `/var/backups`:

```bash
sudo docker exec hub-db sh -c 'mariadb-dump -u hub -p"$MARIADB_PASSWORD" hub' | sudo tee /var/backups/hub.sql > /dev/null
sudo docker run --rm -u root -v hub_app:/app:ro -v /var/backups:/backup hub tar czf /backup/hub-app.tar.gz -C /app .
sudo chmod 600 /var/backups/hub.sql /var/backups/hub-app.tar.gz
```

The single quotes matter: `$MARIADB_PASSWORD` is read inside MariaDB's
container, from its settings, not by your shell.

## Upgrading

The image holds the code, so upgrading means building a new image from newer
code and starting a new container from it. `hub-start` runs the migrations
as it starts.

1. Back up, as above.

2. Keep the image you have, so that you can go back to it:

   ```bash
   sudo docker tag hub hub:previous
   ```

3. Bring the code up to date, and build:

   ```bash
   sudo git -C /opt/hub-image/hubzero-cms pull
   sudo docker build -t hub /opt/hub-image
   ```

4. Replace the container. The command that starts it is the same as in
   [step 6](#6-start-the-hub):

   ```bash
   sudo docker rm -f hub
   sudo docker run -d --name hub --network hub --restart unless-stopped \
       --env-file /opt/hub-image/hub.env \
       -p 80:80 -p 443:443 \
       --add-host hub.example.org:127.0.0.1 \
       -v hub_app:/var/www/hub/app \
       -v hub_caddy_data:/data -v hub_caddy_config:/config \
       hub
   sudo docker logs -f hub
   ```

   This time the log says **The hub is already installed. Bringing its
   database up to date.** The migrations run, and FrankenPHP starts.

The same four commands apply a change to `Caddyfile` or `hub-start`. After
such a change, `git pull` simply has nothing new.

### Going back

If the new image does not work, put the database back as it was and start the
previous image:

```bash
sudo docker rm -f hub
sudo cat /var/backups/hub.sql | sudo docker exec -i hub-db sh -c 'mariadb -u hub -p"$MARIADB_PASSWORD" hub'
sudo docker run -d --name hub --network hub --restart unless-stopped \
    --env-file /opt/hub-image/hub.env \
    -p 80:80 -p 443:443 \
    --add-host hub.example.org:127.0.0.1 \
    -v hub_app:/var/www/hub/app \
    -v hub_caddy_data:/data -v hub_caddy_config:/config \
    hub:previous
```

## When something goes wrong

**The hub's container keeps restarting.** The install stopped, and the
container stops with it rather than serve a hub that is not installed.
`sudo docker logs hub` says why, at the end of the installer's output: most
often a database password that differs between `MARIADB_PASSWORD` and
`HUB_DB_PASSWORD`, or an administrator password shorter than eight
characters. Correct `hub.env`, then remove the container and start it again
as in step 6. A container reads `hub.env` only when it is created, so
restarting it is not enough.

MariaDB, too, reads its settings only once, on its first start. If
`MARIADB_PASSWORD` was wrong then, correcting it later changes nothing.
Before the hub is installed, the simplest way out is to start the database
again: remove both containers and the `hub_db` volume
(`sudo docker volume rm hub_db`), and start again from step 6.

**The log says *"The hub is configured, but its database cannot be
reached"*.** The hub is installed, and MariaDB is not answering. Check that
`hub-db` is running with `sudo docker ps`, and look at
`sudo docker logs hub-db`.

**The browser warns about the certificate.** The Caddyfile still says
`tls internal`, or FrankenPHP has not obtained a certificate for the name
yet. Its log, in `sudo docker logs hub`, says what happened when it tried.
It needs the name to reach the server, and ports 80 and 443 open to the
internet.

**The scheduled tasks' lines in the log say *HTTP 404*.** The tick reached
the hub from somewhere other than the server itself. Check that
`--add-host` names the hub's own name, the one in `HUB_LIVE_SITE`.

**The container will not start, saying an address is already in use.**
Something else on the server listens on port 80 or 443. Stop it, or move the
hub to a server of its own.

For everything else, see
[When something goes wrong](03-installing-the-hub.md#when-something-goes-wrong)
on the page that installs the hub.
