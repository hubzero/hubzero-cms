<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/index/devenvironment
source-id: 3429
modified: 2017-01-03
-->
# Development environment

The CMS does not run standalone. You develop against a working hub — a web
server, PHP, a database, and a configured `app/` directory — with this
repository checked out in place of its CMS. This page separates what the
repository gives you from what you have to get elsewhere.

## What this repository provides

Everything here is in the tree and you can read it:

| Path | What it does |
|---|---|
| [`core/bin/muse`](../../../core/bin/muse) | The console. `muse install` bootstraps a hub from a checkout; `muse migration` applies schema changes. See the [Muse reference](../../reference/muse/README.md). |
| [`core/bin/composer`](../../../core/bin/composer) | A bundled Composer. Run it as `php core/bin/composer install` from `core/`. |
| [`core/composer.json`](../../../core/composer.json) | The PHP dependencies. The platform is pinned to PHP `8.2.30`; the dev requirements are PHPUnit 11, PHP_CodeSniffer 3.13, PHPStan 2, `parallel-lint`, and Mockery. |
| [`core/bin/php_tests.sh`](../../../core/bin/php_tests.sh) | Runs `phpcs --standard=PSR12` and `parallel-lint` over the files you name. |
| [`tools/lint/`](../../../tools/lint) | `missing-facade-imports.php` and `undefined-language-keys.php`. |
| [`tools/docs/`](../../../tools/docs) and `gh-pages/` | The documentation builder and the reference generators. |
| `.github/workflows/` | The CI. See below. |

## What this repository does not provide

There is **no** Dockerfile, no `docker-compose.yml`, no Vagrantfile, no
provisioning script, and no `make`-style setup target anywhere in the tree.
Nothing here builds you a machine. Nothing here installs Apache, PHP, or
MySQL.

The `.travis.yml` at the root is left over from Travis CI. It declares PHP 8.2
and 8.3 and calls `core/bin/php_tests.sh` on the changed files. Nothing runs
it now.

## Requirements

`muse install` runs a pre-flight check before it does anything, and that
check is the authoritative statement of what the CMS needs. From
[`Install/Preflight.php`](../../../core/libraries/Hubzero/Console/Command/Install/Preflight.php):

- PHP **8.2.0** or later.
- These extensions: `pdo`, `pdo_mysql`, `json`, `mbstring`, `openssl`,
  `curl`, `gd`, `fileinfo`, `zip`.

`pdo_mysql` is the only database driver required, and the installer connects
with a `mysql:` DSN, so in practice the database is MySQL or MariaDB. The PHP
lint workflow runs on 8.3, and Composer's platform is pinned to 8.2.30, so
8.2 and 8.3 are the versions the project actually exercises.

Run `php core/bin/muse install check` on a candidate machine to see the
pre-flight result without installing anything.

## Getting a hub to develop against

Three routes, none of them in this repository:

**Install from packages.** The supported path. Enterprise Linux 8 or a
compatible rebuild, the Hubzero packages, and the `hzcms` command. See the
[Installation](../../installation/README.md) book. This is what a production
hub is, so a development hub built this way behaves the same.

**Autohub.** A script that provisions a hub in a virtual machine using
VirtualBox and Vagrant, with Workspaces and optionally Solr. It lives in a
**separate repository**, [hubzero/autohub](https://github.com/hubzero/autohub),
not here; nothing in this tree calls it or knows about it. See
[Autohub](../../installation/autohub.md).

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

This gets you the CMS. It does not get you the parts of a hub that are not
the CMS: the tool session middleware, Workspaces, the mail gateway, LDAP, or
Solr. For those you need the packages or Autohub.

> **Note:** The old version of this page pointed at a VMware image on
> `hubzero.org/download`. Nothing in this repository builds or references
> such an image; check whether it still exists before relying on it.

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

2. Clone the repository into its place. The repository is
   **`hubzero/hubzero-cms`**; the older `hubzero/hubzero` name in the previous
   version of this page is wrong.

   ```bash
   sudo git clone https://github.com/hubzero/hubzero-cms.git example
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
   `-f` performs the run. The `-i` flag the old page used is deprecated and
   now behaves as `-a` (list all), so `-f` alone is what you want.

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
keeps your own unpushed commits on top of it. Re-run steps 4 and 5 when the
dependencies or the migrations change.

## Continuous integration

Three workflows, in `.github/workflows/`:

- **`php-lint.yml`** — runs on PHP 8.3. Two checks: `php -l` over every PHP
  file under `core/` and `app/` outside `vendor/`, and
  `tools/lint/missing-facade-imports.php`. The second exists because the CMS
  registers its facades as root-namespace aliases, so a namespaced file that
  writes `Route::url()` without importing `Route` parses cleanly and fatals
  only when that line runs.
- **`pages.yml`** — builds this documentation, regenerates the references,
  checks every internal link, and fails if the committed `gh-pages/public/`
  is stale.
- **`dev-push.yml`** — a deployment hook to a Purdue-operated development
  host. It is disabled at the top with `if: false`.

None of the three runs the PHP test suite, and none starts a browser.

## Running the tests

PHPUnit is a dev dependency and the tests live beside the code they cover, as
`*Test.php` under `core/libraries/Hubzero/` and in each extension's `tests/`
directory. Run them with `core/vendor/bin/phpunit`. The database library's
tests need their backend available and are run as a separate suite. See
[Testing](../15-testing.md).

For style, `core/bin/php_tests.sh <files>` gives you the PSR-12 check and the
syntax check in one call, the same pair the old Travis configuration ran.

## Next

[Foundation](../03-foundation/README.md) covers how a request is served.
