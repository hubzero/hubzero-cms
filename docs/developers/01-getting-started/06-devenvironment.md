<!--
status: rewritten
reviewed-against: 2.4-main @ 5d7f20276e
reviewed: 2026-10-30
screenshots: none
-->
# Development environment

This page gets a local Hubzero CMS running so you can edit its code and
try the result in a browser. The installation needs a web server, PHP, a
database, and a configured `app/` directory. The Docker setup below creates
all of these from a fresh checkout.

For a first installation on your own machine, go straight to
[Developing in Docker](#developing-in-docker). For a server that will serve
other people, use the [installation overview](../../sysadmins/01-installing.md).
If you already have a development hub, see
[Replacing a hub's CMS with a checkout](#replacing-a-hub-s-cms-with-a-checkout).

## What this repository provides

Everything here is in the tree and you can read it:

| Path | What it does |
|---|---|
| [`core/bin/muse`](../../../core/bin/muse) | The console. `muse install` bootstraps a hub from a checkout; `muse migration` applies schema changes. See the [Muse reference](../../reference/muse.md). |
| [`core/bin/composer`](../../../core/bin/composer) | A bundled Composer, the PHP dependency manager. From the checkout root, run `php core/bin/composer install --working-dir=core`; it reads `core/composer.json` and creates `core/vendor/`. |
| [`core/composer.json`](../../../core/composer.json) | The PHP dependencies. The platform is pinned to PHP `8.2.30`; the dev requirements are PHPUnit 11, PHP_CodeSniffer 3.13, PHPStan 2, `parallel-lint`, and Mockery. |
| [`core/bin/php_tests.sh`](../../../core/bin/php_tests.sh) | Runs `phpcs --standard=PSR12` and `parallel-lint` over the files you name. |
| [`core/bin/lint/`](../../../core/bin/lint) | `missing-facade-imports.php` and `undefined-language-keys.php`. |
| [`docs/_tools/`](../../_tools) and `gh-pages/` | The documentation builder and the reference generators. |
| [`compose.yaml`](../../../compose.yaml) and [`.docker/`](../../../.docker) | A development hub in Docker: this checkout served by FrankenPHP's PHP 8.2, with MariaDB and a mail catcher. See [Developing in Docker](#developing-in-docker). |
| `.github/workflows/` | The CI. See below. |

## What this repository does not provide

Apart from the Docker setup for development, there is no Vagrantfile, no
provisioning script, and no `make`-style setup target anywhere in the tree.
Nothing here installs Apache, PHP, or MySQL on a machine; for a server, see
the [System administrators](../../sysadmins/index.md) book.

## Requirements

`muse install` checks the environment during installation. The CMS checks
include the following, declared in
[`Install/Preflight.php`](../../../core/libraries/Hubzero/Console/Command/Install/Preflight.php):

- PHP **8.2.0** or later.
- These extensions: `pdo`, `pdo_mysql`, `json`, `mbstring`, `openssl`,
  `curl`, `gd`, `fileinfo`, `zip`.

Composer also checks the extensions required by the locked dependencies.
The [server preparation pages](../../sysadmins/02-preparing/index.md)
install these along with the CMS requirements. `pdo_mysql` is the installer's
database driver, so use MySQL or MariaDB for the hub. The PHPUnit workflow
runs on PHP 8.2 and the PHP lint workflow runs on 8.3.

Run `php core/bin/muse install check` from the checkout root to see the
pre-flight result without installing anything. On a fresh checkout it also
reports that Composer dependencies are missing; `muse install` installs them.

## Getting a hub to develop against

Four routes:

**Docker Compose.** `docker compose up -d` in a checkout starts a hub
serving that checkout, with its database and a mail catcher beside it. It is
the quickest way to a hub you can change; see
[Developing in Docker](#developing-in-docker).

**Install one.** The [installation overview](../../sysadmins/01-installing.md) takes a
Linux server from nothing to a running hub from a checkout of this
repository, behind Apache, nginx or FrankenPHP in Docker, using the web
installer or `muse install`. A development hub is the same, on a machine of your own.

**Autohub.** A script that provisions a hub in a virtual machine using
VirtualBox and Vagrant, with Workspaces and optionally Solr. It lives in a
**separate repository**, [hubzero/autohub](https://github.com/hubzero/autohub),
not here; nothing in this tree calls it or knows about it, and its own
README is the only current description of it.

**`muse install` on your own LAMP stack.** If you already have PHP 8.2+, a
web server, and MySQL or MariaDB, the console can take a checkout of this
repository the rest of the way. From the checkout root:

```bash
php core/bin/composer install --working-dir=core
php core/bin/muse install
```

`muse install` runs interactively and can also be driven a step at a time:

| Task | What it does |
|---|---|
| `muse install check` | Pre-flight checks only, no changes |
| `muse install vendor` | Composer dependencies only |
| `muse install appdir` | Create the `app/` directory structure |
| `muse install database` | Configure the database connection |
| `muse install settings` | Write the site settings and configuration files |
| `muse install schema` | Load the schema and essential data |
| `muse install migrations` | Run migrations to bring the schema up to date |
| `muse install sample` | Load sample data |
| `muse install admin` | Create the first administrator account |
| `muse install verify` | Check an installed hub: how it is served, its scheduled tasks, database server, mail and file ownership |

This gets you the CMS. Tool sessions, Workspaces, an external mail gateway,
LDAP, and Solr need separate services and configuration. Ask the people who
operate your target hub which integrations it uses before developing against
one of them.

## Developing in Docker

You need Git and a running Docker engine with its Compose plugin. Check
that `docker compose version` and `docker info` work in your terminal.
The documented setup was tested on Linux; see the verification notes below
for its limits. Then, from a fresh clone:

```bash
git clone --branch 2.4-main https://github.com/hubzero/hubzero-cms.git
cd hubzero-cms
docker compose up -d
docker compose logs -f hub
```

Put `sudo` in front of each `docker` command if your account is not in the
`docker` group.

The first start takes a few minutes. It builds the image, installs the
Composer libraries into `core/vendor/`, installs the hub into `app/` with the
sample data, and starts the web server. When the log shows
**Installation Complete!** and FrankenPHP's own lines follow it, press Ctrl+C
to stop watching:

| | |
|---|---|
| The hub | `https://localhost:8443` |
| Its administrator interface | `https://localhost:8443/administrator`, as `admin` with the password `hubzero-admin` |
| The mail it sends | `http://localhost:8025`, Mailpit's inbox |
| Its database | `docker compose exec db mariadb -u hub -phub hub` |

Open the hub's address in your browser and check that the home page loads.
Then sign in at `/administrator` with the credentials above. You now have
a working CMS to develop against. The database command opens MariaDB's
interactive SQL prompt; type `exit` to leave it.

The certificate is one FrankenPHP makes itself, so your browser may ask you
to accept it for local development. The published web ports answer only on
`127.0.0.1`, and every password is in
[`compose.yaml`](../../../compose.yaml) for anyone to read: this is a hub for
your own machine, not for serving. For a real hub in Docker, see
[A self-contained hub in Docker](../../sysadmins/05-docker-image.md).

### What runs

| Service | What it is |
|---|---|
| `hub` | FrankenPHP's PHP 8.2 image with the hub's extensions, Xdebug and Composer's tools, built from [`.docker/Dockerfile`](../../../.docker/Dockerfile). It holds no code: the checkout is mounted over `/var/www/hub`, so a change you save is the next request's code. [`.docker/hub-dev`](../../../.docker/hub-dev) starts it. |
| `db` | `mariadb:10.11`, with the database in a Docker volume. It waits to be healthy before the hub starts, and creates the usage statistics' `hub_metrics` database on its first start. |
| `mail` | Mailpit. PHP's `mail()` in the hub's container hands every message to it, so nothing the hub sends leaves your machine. |

Each start runs `muse install --env`. The first time, that installs the hub
from the `HUB_*` settings in `compose.yaml`. Every time after, it only runs
the migrations, so restarting brings the database up to date:
`docker compose restart hub`. After pulling code that changes the libraries
too, run `docker compose exec hub php core/bin/muse upgrade`, which installs
them, runs the migrations and clears the cache.

The hub runs as the account that owns the checkout, not as `www-data`, so
`app/` and `core/vendor/` stay yours, as they would on your own LAMP stack.
The scheduled tasks are ticked every minute inside the container; the ticks
show in `docker compose logs hub`.

The checkout root is the directory containing `compose.yaml`, `core/`, and
`index.php`. Run every `docker compose` command on this page from that
directory, in your host terminal. `docker compose exec hub` runs the rest
of its command inside the `hub` container, whose working directory is
`/var/www/hub`.

### Your first edit

The checkout is mounted into the container, so saving a PHP source file
changes what the next request runs. Reload the affected page to try it.
You do not need to rebuild the Docker image for an ordinary PHP edit.

For a contribution to Hubzero, edit the shipped code under `core/`. For a
customization used only by your own hub, use `app/`; read
[Application structure](../03-foundation/01-structure.md#overriding-a-core-extension)
before copying an extension there, because an extension override replaces
the whole extension. Changes under `app/` are Git-ignored.

### Commands

`muse` runs in the hub's container:

```bash
docker compose exec hub php core/bin/muse migration -f
```

`docker compose exec` runs as root, and `muse`, as on a server, becomes the
account that owns the checkout before it does anything. Other commands do
not, so give them your own user number, which `id -u` prints, with
`--user`. For example, with the number 1000:

```bash
docker compose exec --user 1000 --workdir /var/www/hub/core hub vendor/bin/phpunit -c phpunit.xml.dist libraries/Hubzero/Utility/Tests
```

Replace `1000` with your own user number. This runs the utility tests with
the project's test configuration. See [Testing](../15-testing.md) for the
full suite and how to select a single test.

### Xdebug

Xdebug is installed and off. Start the hub with it on, and your editor
listening on port 9003:

```bash
XDEBUG_MODE=debug docker compose up -d
```

Map `/var/www/hub` in the container to the checkout in your editor.
`docker compose up -d` without it turns Xdebug off again.

### Changing the ports, or leaving out the sample data

Compose reads a file named `.env` beside `compose.yaml`, if there is one.
Set any of these in it before the first start:

```dotenv
HUB_DEV_PORT=8444
HUB_DEV_MAIL_PORT=8026
HUB_DEV_SAMPLE_DATA=0
```

The hub is served on `HUB_DEV_PORT` both inside its container and out, and
the installer writes the address into `app/config/app.php`. Change the port
after the first start and you must change `live_site` there too.

### Stopping, and starting over

`docker compose stop` stops the three containers and `docker compose start`
starts them again. `docker compose down` removes the containers but keeps the
database. To start again from nothing, remove the database volume and the
hub's `app/` directory as well:

> **Warning:** The following reset deletes this development hub's database,
> accounts, uploads, configuration, and customizations under `app/`. Back up
> anything you want to keep. Run it only in the development checkout you
> intend to reset.

```bash
docker compose down -v
rm -rf app
docker compose up -d
```

The setup was run on Ubuntu 24.04 with Docker 29 and Compose 2.40, from a
clone with no `core/vendor/` and no `app/`: the first start, restarts, the
administrator interface, mail reaching Mailpit, the scheduled tasks, `muse`
through `docker compose exec`, and Xdebug switched on and off. Nothing it
wrote in the checkout belonged to root. It was not tried on macOS or Windows.

## Replacing a hub's CMS with a checkout

On a hub installed from packages, the CMS directory is a snapshot from when
the package was built. To develop against current code, replace it with a
clone. The hub's own state lives in `app/`, which is not in the repository —
[`.gitignore`](../../../.gitignore) excludes `app/**` — so `app/` is what you
carry across.

> **Warning:** Do this on a development hub only. Take a database dump first;
> `muse migration` changes the schema and reversing a migration is difficult.

1. Change into the web root and move the existing installation aside. The
   directory is named for the hub name given to `hzcms install`:

   ```bash
   cd /var/www
   sudo mv example example.bak
   ```

2. Clone the release branch into its place:

   ```bash
   sudo git clone --branch 2.4-main https://github.com/hubzero/hubzero-cms.git example
   ```

3. Copy the hub's own state back in. This is the configuration, the uploaded
   files, and the site's templates and extensions.

   ```bash
   sudo cp -a example.bak/app example/
   ```

4. Install the PHP dependencies. `core/vendor/` is not in the repository
   either.

   ```bash
   cd /var/www/example/core
   sudo php ./bin/composer install
   ```

5. Bring the database schema up to the code. Run this from the hub root, not
   from `core/`:

   ```bash
   cd /var/www/example
   sudo php core/bin/muse migration -f
   ```

   `muse migration` with no options is a dry run that lists what it would do;
   `-f` performs the run. If you encounter `-i` in older instructions, leave
   it out: it is deprecated and now behaves as `-a` (list all).

6. Give the web server ownership and let your account write:

   ```bash
   cd /var/www
   sudo chown -R apache:apache example
   sudo chmod -R 775 example
   sudo usermod -aG apache $USER
   ```

   The user is `apache` on Enterprise Linux and `www-data` on Debian and
   Ubuntu.

> **Warning:** Group-writable files owned by the web server user are for a
> development machine only. Do not carry this permission scheme to a hub
> anyone else uses.

Afterwards, `git pull --rebase` from the hub root brings in new code. Rebase
keeps your own unpushed commits on top of it. Then `php core/bin/muse upgrade`
does steps 4 and 5 again, and clears the cache, for whatever the new code
needs.

## Continuous integration

Three active workflows live in `.github/workflows/`. The workflow files
declare the paths that trigger each check:

- **`php-lint.yml`** — runs on PHP 8.3. Three checks: `php -l` over every PHP
  file under `core/` and `app/` outside `vendor/`;
  `core/bin/lint/missing-facade-imports.php`, which fails on any namespaced file
  that uses a facade without importing it; and
  `core/bin/lint/undefined-language-keys.php`, which fails if
  the number of undefined language keys rises above the workflow's ceiling.
  The second exists because such a file parses cleanly and misbehaves only
  when the line runs — see
  [Facades](../03-foundation/06-facades.md#importing-a-facade). The
  third is a ceiling rather than a gate: lower the number as keys are
  defined.
- **[`tests.yml`](../../../.github/workflows/tests.yml)** — installs the
  Composer dependencies and runs the PHPUnit suite on PHP 8.2, using
  `core/phpunit.xml.dist`.
- **`pages.yml`** — builds this documentation, regenerates the references,
  checks every internal link, and fails if the committed `gh-pages/public/`
  is stale.
A fourth workflow, `dev-push.yml`, is a deployment hook to a Purdue-operated
development host. It is disabled with `if: false`. None of these workflows
starts a browser, so try interface changes on your local hub as well.

## Running the tests

PHPUnit is a dev dependency and the tests live beside the code they cover, as
`*Test.php` under `core/libraries/Hubzero/` and in each extension's `tests/`
directory. From the checkout root, run the suite with:

```bash
php core/vendor/bin/phpunit -c core/phpunit.xml.dist
```

The shipped test database helper uses SQLite fixtures rather than your hub's
MariaDB database. See [Testing](../15-testing.md) for dependencies, selecting
a single test, and the limits of that coverage.

For style, `core/bin/php_tests.sh <files>` gives you the PSR-12 check and the
syntax check in one call. No workflow runs it: much of the older code is
indented with tabs, which PSR-12 does not allow, so run it on files you write
or bring up to the standard.

## Next

[Foundation](../03-foundation/index.md) covers how a request is served.
