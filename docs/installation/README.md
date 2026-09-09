<!--
status: rewritten
reviewed: 2026-09-09
-->
# Installation

How to install the Hubzero platform on your own servers from the Hubzero
packages, configure it for the first time, and keep it up to date.

Hubzero is installed on Enterprise Linux 8 or a compatible rebuild
(RHEL, Rocky, AlmaLinux). The packages set up the web server, PHP, the
database, and the CMS, and the `hzcms` command configures each piece.
Optional services such as the mail gateway, LDAP, WebDAV, the tool
execution hosts, and Solr search are separate packages that you add as
your hub needs them.

## In this book

- [Enterprise Linux 8](el8/README.md) — the installation walk-through:
  operating system, web server, PHP, database, CMS, mail, and then the
  optional services and add-ons.
- [Upgrading from EL7 to EL8](el8/upgrade7to8.md) and
  [Updates](el8/updates.md) — moving an existing hub forward.
- [Autohub](autohub.md) — the scripted installer.

## Before you start

- A server or virtual machine with a clean Enterprise Linux 8 install, root
  access, and a fully qualified hostname that resolves in DNS.
- A TLS certificate for that hostname. Hubs run over HTTPS only.
- Outbound mail delivery, if the hub is to send registration and
  notification email.

> **Note:** These pages were imported from help.hubzero.org and describe the
> EL8 packages as they were documented there. Several of them were never
> published on the old site; each says so at the top. Check the package
> names against the current repository before following them on a
> production system.
