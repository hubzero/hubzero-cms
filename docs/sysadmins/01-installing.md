<!--
status: rewritten
reviewed-against: 2.4-main @ d96149df5b
reviewed: 2026-10-30
screenshots: none
summary: How installing a hub goes: what it needs, preparing the server on your system's page, then installing the hub.
-->
# Installation overview

A hub runs on a Linux server with PHP 8.2, MariaDB, and a web server in front
of PHP. Installing one takes two steps, each with its own part of this book:

1. **[Preparing the server](02-preparing/README.md).** Choose your system
   there and follow its page, which takes a server with nothing on it to one
   ready for the hub: the packages, the code, and the web server. Each
   system's page is complete on its own. The section also helps you choose,
   if you have not already: hosted hubs currently run on Enterprise Linux 8.
2. **[Installing the hub](03-installing-the-hub.md)**, which every system's
   page ends at: the database, the installer, and what comes after. It is the
   same on every system.

If the hub you are asking about is already installed and answering, go to
[The first week with a new hub](../managers/01-getting-started.md#the-first-week-with-a-new-hub)
instead.

## What every system needs

- A Linux server. Hubzero does not run on Windows; the installer refuses to
  start there.
- A name for the hub, pointing at the server, and a TLS certificate for it.
  A hub is always served over HTTPS: the administrator sign-in form always
  posts to an `https://` address, so on plain HTTP the administrator can
  never sign in. A self-signed certificate will do to start with, before the
  real one or the name is ready; each system's page shows where to find or
  make one.
- MariaDB.
- SELinux turned off, on a system that has it. Hubs run with it off and are
  not tested with it on.
- `git` and `unzip`. Composer itself ships in the repository as
  `core/bin/composer`; you do not install it separately.
- PHP 8.2 and the extensions the installer checks for, read here from its
  own requirements check:

<!--include: core/libraries/Hubzero/System/Requirements.php:25-40-->

The libraries Composer installs need the XML extensions, `sockets` and
`posix` as well. Each system's page installs all of them.

## What installing does

[Installing the hub](03-installing-the-hub.md) runs one of two installers
that do the same job: `muse install` on the command line, which can also
take its answers from a file, or the installer in the browser. Both load the database, write the
hub's configuration into `app/config/`, bring the database up to date with
the migrations, and create the administrator. The hub's code is a git
checkout of its release branch, `2.4-main`, which is what
[Upgrading a hub](04-upgrading.md) brings up to date later.

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
