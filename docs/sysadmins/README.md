<!--
status: rewritten
reviewed-against: 2.4-main @ a7cc23ecc4
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

- [Installing a hub](01-installing.md) — from a Linux server with nothing on
  it to a hub with an administrator who can sign in: PHP 8.2 and MariaDB,
  Apache or nginx with PHP-FPM or FrankenPHP in Docker, and the installer in
  the browser, on the command line, or from an answer file.
- [Upgrading a hub](02-upgrading.md) — bringing an installed hub up to the
  head of its release branch, with a backup first and the way back.

On a small hub the system administrator and the hub manager are often the
same person. The split is between the work, not the people: what needs a
shell is here, what needs a browser is there.
