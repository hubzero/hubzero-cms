<!--
status: rewritten
reviewed-against: 2.4-main @ 4b8a132fb4
reviewed: 2026-10-30
screenshots: none
summary: How installing a hub goes: what it needs, preparing the server on your system's page, then installing the hub.
-->
# Installation overview

A hub runs on a Linux server with PHP 8.2, MariaDB, and a web server in front
of PHP. Installing one takes two pages:

1. **Your system's page**, from the table below, which takes a server with
   nothing on it to one ready for the hub: the packages, the code, and the
   web server. Each is complete for its system, and repeats the others
   rather than send you between them.
2. **[Installing the hub](12-installing-the-hub.md)**, which every system's
   page ends at: the database, the installer, and what comes after. It is the
   same on every system.

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

[Installing the hub](12-installing-the-hub.md) runs one of two installers
that do the same job: `muse install` on the command line, which can also
take its answers from a file, or the installer in the browser. Both load the database, write the
hub's configuration into `app/config/`, bring the database up to date with
the migrations, and create the administrator. The hub's code is a git
checkout of its release branch, `2.4-main`, which is what
[Upgrading a hub](13-upgrading.md) brings up to date later.

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
