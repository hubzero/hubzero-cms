<!--
status: rewritten
reviewed-against: 2.4-main @ 581dd435fc
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
  installing goes: prepare the server, then install the hub.
- [Preparing the server](02-preparing/README.md) — a page for each system,
  and how to choose between them: Debian 12 and 13, Ubuntu 22.04, 24.04 and
  26.04 LTS, Enterprise Linux 8, 9 and 10, any Linux in Docker, and any other
  Linux.
- [Installing the hub](03-installing-the-hub.md) — on a prepared server:
  the database, the installer and what comes after, the same on every
  system.
- [Upgrading a hub](04-upgrading.md) — bringing an installed hub up to the
  head of its release branch, with a backup first and the way back.

On a small hub the system administrator and the hub manager are often the
same person. The split is between the work, not the people: what needs a
shell is here, what needs a browser is there.
