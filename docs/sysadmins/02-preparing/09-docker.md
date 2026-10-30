<!--
status: rewritten
reviewed-against: 2.4-main @ d96149df5b
reviewed: 2026-10-30
screenshots: none
summary: Preparing any Linux server for a hub in FrankenPHP's PHP 8.2 Docker image, with MariaDB from the server's own distribution.
-->
# Preparing a server for FrankenPHP in Docker

This page runs the hub in FrankenPHP's PHP 8.2 Docker image, on any Linux
server that can run Docker. FrankenPHP is a web server with PHP built in, and
the image brings PHP 8.2, so the server needs no PHP of its own and no other
web server. It is the way to run a hub on a system whose own PHP is not 8.2
and has no second source of it, and a way to keep the hub's PHP apart from
the server's on any system. MariaDB comes from the server's distribution.
Then [Installing the hub](../03-installing-the-hub.md) installs it, the same
way on every system.

Full installs in this image, from the browser and the command line, were run on an Ubuntu 24.04 server with PHP 8.2.35, and the image rebuild the upgrade page describes was run on it too. Docker on Enterprise Linux was not tried.

For another system, or help choosing one, see
[Preparing the server](README.md).

## What you need

- A Linux server, and an account on it that can use `sudo`.
- Nothing else listening on ports 80 and 443: FrankenPHP is the web server.
- A name for the hub, such as `hub.example.org`, pointing at the server, and a
  TLS certificate for it. A hub has to be served over HTTPS: the administrator
  sign-in form always posts to an `https://` address, so on plain HTTP the
  administrator can never sign in.

## 1. Install MariaDB, the tools and Docker

Install MariaDB, `git` and `unzip` from the distribution, and Docker. Install
no PHP and no web server: the image brings both.

**On Debian or Ubuntu:**

```bash
sudo apt install mariadb-server git unzip docker.io
```

**Or on Enterprise Linux 8, 9 or 10**, MariaDB from the distribution and
Docker from Docker's own repository, which replaces the Podman Enterprise
Linux installs by default. On 8 and 9, enable the `mariadb:10.11` stream
first, with `sudo dnf module enable mariadb:10.11`; 10 has MariaDB 10.11 as it
is.

```bash
sudo dnf install mariadb-server git unzip
sudo systemctl enable --now mariadb
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

**Or on any other distribution**, its own MariaDB, `git`, `unzip` and Docker
packages.

## 2. Get the code

The repository is the hub: its top directory is the document root. Clone it,
and give it to user and group 33, which is the `www-data` account inside the
image the hub runs in:

```bash
sudo git clone --branch 2.4-main https://github.com/hubzero/hubzero-cms.git /var/www/hub
sudo chown -R 33:33 /var/www/hub
```

On Debian and Ubuntu that is the `www-data` account on the server too. On Enterprise Linux no account has the number; use it as it is.

## 3. Build the image

FrankenPHP is a web server with PHP built in, and its PHP 8.2 image is the
starting point. Add the extensions the hub needs that it does not already
have. Put this in `/opt/hub-frankenphp/Dockerfile`:

```dockerfile
FROM dunglas/frankenphp:1-php8.2-bookworm

# The extensions the hub needs that the image does not already have, the
# tools Composer uses, and the MariaDB client muse backs up and restores with
RUN install-php-extensions pdo_mysql gd zip sockets opcache \
    && apt-get update \
    && apt-get install -y --no-install-recommends git unzip mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# PHP's production settings, and a place for sessions outside the document
# root: the image has neither, and the installer keeps its secrets there
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && mkdir -p /var/lib/php/sessions \
    && chown www-data:www-data /var/lib/php/sessions \
    && echo 'session.save_path = "/var/lib/php/sessions"' > "$PHP_INI_DIR/conf.d/hub.ini"

# Run as the account that owns the hub's files, still able to listen on
# 80/443, and run the hub's commands in its directory
RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
    && chown -R www-data:www-data /config/caddy /data/caddy

WORKDIR /var/www/hub
USER www-data
```

```bash
sudo docker build -t hub-frankenphp /opt/hub-frankenphp
```

The session directory is not optional. Without a writable place outside the
document root, the installer stops at a page headed *Server Configuration
Required* before it asks anything.

## 4. Write the Caddyfile

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

Before the name reaches the server, or for a hub with no public name, add
`tls internal` inside the site block, on the line after `root`. FrankenPHP
then makes the certificate itself, from a local authority of its own, and
browsers warn about it; accept the warning to reach the installer. When the
name reaches the server, take the line out and restart the container, and
FrankenPHP obtains a real certificate in its place. The hub is served over
HTTPS either way.

## 5. Start the container

```bash
sudo docker run -d --name hub --restart unless-stopped --network host \
    -v /var/www/hub:/var/www/hub \
    -v /opt/hub-frankenphp/Caddyfile:/etc/frankenphp/Caddyfile:ro \
    -v hub_caddy_data:/data -v hub_caddy_config:/config \
    hub-frankenphp
```

- `--network host` lets it answer on the server's own ports 80 and 443, and
  reach MariaDB at `127.0.0.1` as if it were not in a container. MariaDB's
  socket is not inside the container, so `127.0.0.1` is how the hub reaches
  the database.
- The hub's directory is mounted at the same path inside, so paths the
  installer writes into the configuration are right on both sides. The
  container runs as user 33, which owns the files.
- `hub_caddy_data` keeps the certificates FrankenPHP obtains across restarts
  and rebuilds.

## 6. How to run the hub's commands

The hub's PHP is inside the container, so its commands run there, and the
container already runs as the account that owns the hub's files. Wherever
[Installing the hub](../03-installing-the-hub.md) and
[Upgrading a hub](../04-upgrading.md) say

```bash
sudo php core/bin/muse ...
```

run instead

```bash
sudo docker exec -it hub php core/bin/muse ...
```

`-it` is what lets `muse` ask its questions; leave it out in a script, which
has no terminal to give it.

The scheduled tasks need a line of their own too. Once the hub is installed,
add this to `/etc/crontab` in place of the one Installing the hub gives:

```
* * * * * root docker exec hub php core/bin/muse cron:jobs tick
```

## 7. Next: install the hub

The server is ready. Go on to [Installing the hub](../03-installing-the-hub.md), which creates
the database and runs the installer, the same way on every system.

## When something goes wrong in Docker

**The installer shows *Server Configuration Required*.** PHP in the container
has no writable place outside the document root. The image was built without
the `session.save_path` lines in the Dockerfile; add them and rebuild.

**The installer cannot connect to the database through the socket.**
MariaDB's socket is not inside the container. Use `127.0.0.1`.

**The container will not start, saying an address is already in use.**
Something else on the server already listens on port 80 or 443, or on 2019,
where FrankenPHP takes its own administration requests. Stop it, or move the
hub to a server of its own.

**The browser installer's migration output carries warnings that session
settings *"cannot be changed after headers have already been sent"*.** They
come from FrankenPHP's own command line, which the installer runs the
migrations with, and are harmless.

For everything else, see
[When something goes wrong](../03-installing-the-hub.md#when-something-goes-wrong) on the page
that installs the hub.
