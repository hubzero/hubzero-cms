<!--
status: draft
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
summary: Installation documentation is being rewritten for the new web installer.
-->
# Installing a hub

**Coming soon.**

If the hub you are asking about is already installed and answering, this is
the wrong page and nothing below applies to you. Go to
[The first week with a new hub](01-getting-started.md#the-first-week-with-a-new-hub),
which starts from a hub that runs and walks the decisions that come next in
order.

Hubzero is gaining a web installer that sets a hub up from the browser,
without the package repositories and hand-run configuration steps the old
process needed. This book will be rewritten for it.

The previous version documented installing from packages onto Enterprise
Linux 8. It described a process the web installer replaces, so it has been
withdrawn rather than left to mislead.

## What is documented in the meantime

Everything after a hub is running is covered and current:

- [Hub managers](README.md) — the administrator's guide,
  screen by screen. Start with
  [The administrator interface](01-getting-started.md#the-administrator-interface)
  for the back end itself, then
  [The first week with a new hub](01-getting-started.md#the-first-week-with-a-new-hub),
  which assumes a hub that is installed and answering, and walks the
  decisions that come next in order.
- [Search](09-components/31-search/README.md) — connecting a hub
  to Apache Solr, which is a separate service a hub adds and not part of
  installing the CMS.
- [Authentication](05-configuring/06-authentication.md) and
  [External authentication](05-configuring/07-extauth.md) —
  the sign-in providers, including the Apache configuration a federated
  provider needs in front of it.
- [Developers](../developers/README.md) — for working on the code rather
  than running a hub, including
  [the development environment](../developers/01-getting-started/06-devenvironment.md),
  which sets out what this repository provides and what it does not.

## Requirements

These have not changed and are read from the installer's own preflight
check:

<!--include: core/libraries/Hubzero/Console/Command/Install/Preflight.php:24-24-->

The PHP extensions it requires are `pdo`, `pdo_mysql`, `json`, `mbstring`,
`openssl`, `curl`, `gd`, `fileinfo` and `zip`.
