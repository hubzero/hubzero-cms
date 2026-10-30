<!--
status: rewritten
reviewed-against: 2.4-main @ a89b23e433
reviewed: 2026-10-30
screenshots: none
summary: Putting a hub on a server and keeping it current: installing it, and upgrading it with a way back.
-->
# System administrators

This book is for the person who looks after the server a hub runs on: the
one with a shell on it, who installs the hub, keeps its code current, and
puts it back when something goes wrong. Everything here happens on the
server, as `www-data` or as root. The hub's own administrator interface, at
`/administrator`, is the subject of [Hub managers](../managers/README.md).

- [Installation overview](01-installing.md) — what a hub needs, and how
  installing goes: prepare the server on your system's page, then install
  the hub. The pages for preparing the server:
  [Debian 12](02-debian-12.md), [Debian 13](03-debian-13.md),
  [Ubuntu 22.04 LTS](04-ubuntu-22-04-lts.md),
  [Ubuntu 24.04 LTS](05-ubuntu-24-04-lts.md),
  [Ubuntu 26.04 LTS](06-ubuntu-26-04-lts.md),
  [Enterprise Linux 8](07-enterprise-linux-8.md),
  [Enterprise Linux 9](08-enterprise-linux-9.md),
  [Enterprise Linux 10](09-enterprise-linux-10.md),
  [any Linux in Docker](10-docker.md), and
  [any other Linux](11-other-linux.md).
- [Installing the hub](12-installing-the-hub.md) — on a prepared server:
  the database, the installer and what comes after, the same on every
  system.
- [Upgrading a hub](13-upgrading.md) — bringing an installed hub up to the
  head of its release branch, with a backup first and the way back.

On a small hub the system administrator and the hub manager are often the
same person. The split is between the work, not the people: what needs a
shell is here, what needs a browser is there.
