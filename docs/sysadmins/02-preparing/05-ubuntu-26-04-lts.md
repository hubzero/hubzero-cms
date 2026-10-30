<!--
status: rewritten
reviewed-against: 2.4-main @ d96149df5b
reviewed: 2026-10-30
screenshots: none
summary: Preparing an Ubuntu 26.04 LTS server for a hub, which has no PHP 8.2 packages: FrankenPHP's PHP 8.2 Docker image, with MariaDB 11.8.
-->
# Preparing an Ubuntu 26.04 LTS server

This page takes a server running Ubuntu 26.04 LTS with nothing on it to a hub
that answers at its own address and has an administrator who can sign in.
Ubuntu 26.04 ships PHP 8.5, and Ondřej Surý's PPA, which packages PHP 8.2 for
earlier Ubuntu releases, has none for it. So the hub runs in FrankenPHP's
PHP 8.2 Docker image: FrankenPHP is a web server with PHP built in, and the
image brings PHP 8.2, so the server needs none. MariaDB is Ubuntu's own,
11.8. Then [Installing the hub](../03-installing-the-hub.md) installs it, the same
way on every system.

The PPA was checked for Ubuntu 26.04 and has no packages for it. Full installs in this image, from the browser and the command line, were run on an Ubuntu 24.04 server, and a hub was installed against MariaDB 11.8.

For another system, or help choosing one, see
[Preparing the server](README.md).

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

## 2. Get the code

The repository is the hub: its top directory is the document root. Clone it,
and give it to user and group 33, which is the `www-data` account inside the
image the hub runs in:

```bash
sudo git clone --branch 2.4-main https://github.com/hubzero/hubzero-cms.git /var/www/hub
sudo chown -R 33:33 /var/www/hub
```

On Ubuntu that is the `www-data` account on the server too.

## 3. Build the image

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
sudo docker exec -it -w /var/www/hub hub php core/bin/muse ...
```

`-it` is what lets `muse` ask its questions; leave it out in a script, which
has no terminal to give it.

The scheduled tasks need a line of their own too. Once the hub is installed,
add this to `/etc/crontab` in place of the one Installing the hub gives:

```
* * * * * root docker exec -w /var/www/hub hub php core/bin/muse cron:jobs tick
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
