<!--
status: rewritten
reviewed-against: 2.4-main @ d96149df5b
reviewed: 2026-10-30
screenshots: none
summary: Preparing a server for a hub: what each system's page covers, and how to choose between them.
-->
# Preparing the server

Before the hub can be installed, the server needs MariaDB, PHP 8.2 with the
extensions the hub uses, the hub's code, and a web server ready to serve it.
Each page in this section does that for one system, from a server with
nothing on it, and each is complete on its own. Follow the one for your
system; it ends by sending you on to
[Installing the hub](../03-installing-the-hub.md), which is the same on every
system.

## What each page covers

Every page works through the same steps, with its own packages, accounts and
paths:

1. **MariaDB, PHP and the tools.** PHP 8.2 from the system where it has it,
   or from the repository that packages it for that system where it does not.
2. **The web server.** Apache **or** nginx, with PHP-FPM.
3. **The firewall and SELinux**, on Enterprise Linux, which starts with both
   closed to a web application.
4. **The code**, cloned from the release branch and owned by the account PHP
   runs as.
5. **The web server's configuration**, written for that system: where the
   file goes, the PHP-FPM socket, the logs.


The Docker pages replace the PHP and web server steps with an image built
from FrankenPHP's PHP 8.2 image, which brings both, and say how to run the
hub's commands inside the container.

| Page | PHP 8.2 from | MariaDB | Web server |
|---|---|---|---|
| [Debian 12](01-debian-12.md) | Debian | 10.11 | Apache or nginx |
| [Debian 13](02-debian-13.md) | Ondřej Surý's archive | 11.8 | Apache or nginx |
| [Ubuntu 22.04 LTS](03-ubuntu-22-04-lts.md) | Ondřej Surý's PPA | 10.6 | Apache or nginx |
| [Ubuntu 24.04 LTS](04-ubuntu-24-04-lts.md) | Ondřej Surý's PPA | 10.11 | Apache or nginx |
| [Ubuntu 26.04 LTS](05-ubuntu-26-04-lts.md) | FrankenPHP's Docker image | 11.8 | FrankenPHP |
| [Enterprise Linux 8](06-enterprise-linux-8.md) | AppStream, `php:8.2` | 10.11 | Apache or nginx |
| [Enterprise Linux 9](07-enterprise-linux-9.md) | AppStream, `php:8.2` | 10.11 | Apache or nginx |
| [Enterprise Linux 10](08-enterprise-linux-10.md) | Remi's repository | 10.11 | Apache or nginx |
| [Any Linux, in Docker](09-docker.md) | FrankenPHP's Docker image | the distribution's | FrankenPHP |
| [Any other Linux](10-other-linux.md) | wherever it is packaged | the distribution's | Apache or nginx |

Enterprise Linux means Red Hat Enterprise Linux and its rebuilds, Rocky
Linux and AlmaLinux. Ubuntu is covered in its long-term support releases
only.

## How to choose

**Use the system you already run.** If your organization has a standard
server build, or the people who will look after the hub know one of these
systems well, that matters more than any difference between them. Every page
ends at the same hub.

**If you want to match the hubs Hubzero hosts, choose Enterprise Linux 8.**
Hosted hubs currently run on it. It is also one of the three systems on
which PHP 8.2 comes from the distribution itself.

**If you would rather not add a repository for PHP, choose Debian 12,
Enterprise Linux 8 or Enterprise Linux 9.** They ship PHP 8.2 themselves. The
others get it from a repository maintained outside the distribution: Ondřej
Surý's for Debian 13 and Ubuntu, Remi Collet's for Enterprise Linux 10. Both
are long established and widely used, but they are one more source to trust
and to keep up to date.

**If you are setting up a new server for years of service, weigh how long the
release is supported.** Approximately, as of this writing:

| System | Supported until |
|---|---|
| Ubuntu 22.04 LTS | April 2027 |
| Debian 12 | June 2028, in its long-term support |
| Enterprise Linux 8 | May 2029 |
| Ubuntu 24.04 LTS | April 2029 |
| Debian 13 | 2030, in its long-term support |
| Ubuntu 26.04 LTS | April 2031 |
| Enterprise Linux 9 | May 2032 |
| Enterprise Linux 10 | May 2035 |

Check the distribution's own lifecycle page before you decide; vendors
sometimes extend them, and paid extended support runs longer still.

**If the system's PHP is not 8.2 and nothing packages 8.2 for it, or you want
the hub's PHP kept apart from the server's, use Docker.** The
[Docker page](09-docker.md) works on any Linux server, and it is the only
route on Ubuntu 26.04 LTS.

**Apache or nginx makes little difference to the hub.** Every page gives a
complete configuration for each. Choose the one you already know how to
run.
