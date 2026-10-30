<!--
status: rewritten
reviewed-against: 2.4-main @ 4b8a132fb4
reviewed: 2026-10-30
screenshots: none
summary: Choosing how to install a hub: what it needs, the systems the installation pages cover, and what installing does.
-->
# Installing a hub

A hub runs on a Linux server with PHP 8.2, MariaDB, and a web server in front
of PHP. Each system below has a page of its own that takes a server with
nothing on it to a hub with an administrator who can sign in. Every page is
complete, and they repeat each other rather than send you between them: find
your system and follow its page from the top.

If the hub you are asking about is already installed and answering, go to
[The first week with a new hub](../managers/01-getting-started.md#the-first-week-with-a-new-hub)
instead.

## Choose your system

| System | PHP 8.2 from | MariaDB | Web server |
|---|---|---|---|
| [Debian 12](02-debian-12.md) | Debian | 10.11 | Apache or nginx |
| [Debian 13](03-debian-13.md) | Ondřej Surý's archive | 11.8 | Apache or nginx |
| [Ubuntu 22.04 LTS](04-ubuntu-22-04-lts.md) | Ondřej Surý's PPA | 10.6 | Apache or nginx |
| [Ubuntu 24.04 LTS](05-ubuntu-24-04-lts.md) | Ondřej Surý's PPA | 10.11 | Apache or nginx |
| [Ubuntu 26.04 LTS](06-ubuntu-26-04-lts.md) | FrankenPHP's Docker image | 11.8 | FrankenPHP |
| [Enterprise Linux 8](07-enterprise-linux-8.md) | AppStream, `php:8.2` | 10.11 | Apache or nginx |
| [Enterprise Linux 9](08-enterprise-linux-9.md) | AppStream, `php:8.2` | 10.11 | Apache or nginx |
| [Enterprise Linux 10](09-enterprise-linux-10.md) | Remi's repository | 10.11 | Apache or nginx |
| [Any Linux, in Docker](10-docker.md) | FrankenPHP's Docker image | the distribution's | FrankenPHP |
| [Any other Linux](11-other-linux.md) | wherever it is packaged | the distribution's | Apache or nginx |

Enterprise Linux means Red Hat Enterprise Linux and its rebuilds, Rocky
Linux and AlmaLinux. Ubuntu is covered in its long-term support releases
only.

Where a system has PHP 8.2 of its own, its page uses it. Where it does not,
the page adds the repository that packages it for that system: Ondřej Surý's
for Debian and Ubuntu, Remi Collet's for Enterprise Linux. Where there is no
such repository, as on Ubuntu 26.04 LTS, the hub runs in FrankenPHP's PHP 8.2
Docker image, which needs no PHP on the server. That image is also a choice on
any system.

## What every system needs

- A Linux server. Hubzero does not run on Windows; the installer refuses to
  start there.
- A name for the hub, pointing at the server, and a TLS certificate for it.
  A hub has to be served over HTTPS: the administrator sign-in form always
  posts to an `https://` address, so on plain HTTP the administrator can
  never sign in.
- MariaDB.
- `git` and `unzip`. Composer itself ships in the repository as
  `core/bin/composer`; you do not install it separately.
- PHP 8.2 and the extensions the installer checks for, read here from its
  own requirements check:

<!--include: core/libraries/Hubzero/System/Requirements.php:25-40-->

The libraries Composer installs need the XML extensions, `sockets` and
`posix` as well. Each system's page installs all of them.

## What installing does

Every page ends the same way, with one of two installers that do the same
job: `muse install` on the command line, which can also take its answers from
a file, or the installer in the browser. Both load the database, write the
hub's configuration into `app/config/`, bring the database up to date with
the migrations, and create the administrator. The hub's code is a git
checkout of its release branch, `2.4-main`, which is what
[Upgrading a hub](12-upgrading.md) brings up to date later.

## What was tested

- **Full installs, browser and command line:** Ubuntu 24.04 LTS behind
  Apache and nginx, with Ubuntu's own PHP 8.3, and FrankenPHP's PHP 8.2
  Docker image.
- **Full installs, command line:** Enterprise Linux 8 and 10 behind Apache,
  and 9 behind nginx, with PHP 8.2 from the packages their pages name, in
  Rocky Linux containers. A container has no SELinux, firewall or systemd, so
  those steps were not run.
- **PHP 8.2 packages installed and every extension checked:** Debian 12 and
  13, Ubuntu 22.04 LTS and 24.04 LTS.
- **Databases:** MariaDB 10.11 and 11.8.

## Warnings during the migrations

Both installers, and the upgrade, show the same four warnings on a new hub.
None of them stops the hub working:

| Warning | Why |
|---|---|
| *onBeforeMigrate hook 'UpdateTimezoneDatabase.php' ... Insufficient privileges to update timezone tables* | Loading MariaDB's time zone tables needs its `root` account. Each system's page shows how to load them yourself |
| *Migration20150330124145Core.php: Unable to write out new configuration file* | It updates a configuration file from an older version of the hub, which a new hub does not have |
| *Migration20150904235936ComUsage.php: Failed to run with the necessary privileges* | The `_metrics` database does not exist, and creating one needs `root`. Create it, as each system's page shows, and run the migrations again |
| *Migration20170627153900ComHubgraph.php: Failed to run with the necessary privileges* | It removes database triggers that older hubs had. A new hub has none to remove |

A migration that ended in a warning is tried again the next time the
migrations run; one that succeeded is not.
