<!--
status: rewritten
reviewed: 2026-09-09
-->
# Hub managers

The administrator's guide. A hub manager configures the hub, manages its
members and their access, curates its content, installs and configures
extensions, and keeps the site healthy. Almost all of that happens in the
administrator interface at `/administrator`, which this book walks through
screen by screen.

## Where to start

If the hub is new, read
[The administrator interface](01-getting-started.md#the-administrator-interface) and then
[The first week with a new hub](01-getting-started.md#the-first-week-with-a-new-hub). The first
covers the login and the back end's menus; the second is the ordered path
through the choices that are hard to change later — mail, registration,
access, the front page, and the scheduled jobs — with a link to the chapter
covering each. [Configuring](05-configuring/README.md) has the settings
screens in full.

If the screens make sense one at a time but not together, read
[How a hub fits together](01-getting-started.md#how-a-hub-fits-together). It is the
mental model the rest of this book takes for granted — the two halves of the
application, how a page is built from a component, a template and its
modules, what lives in the database and what lives on disk, and what a
manager can and cannot break.

If you are taking over a running hub, start with
[Daily maintenance](03-maintenance/README.md) and
[Users](06-users/README.md).

## In this book

- [Getting started](01-getting-started.md) — the administrator
  interface and the first configuration.
- [Integrations](02-advancedsetup.md) — connecting the hub to analytics,
  Google Drive, the rich text editor, and CAPTCHA.
- [Daily maintenance](03-maintenance/README.md) — approving content,
  support tickets, cron jobs, notices, and tools.
- [Frequently asked questions](04-faq.md)
- [Configuring](05-configuring/README.md) — hub settings, components,
  modules, plugins, registration, and authentication.
- [Users](06-users/README.md) — members, access groups and levels, groups
  and super groups, member import, and user notes.
- [Menus](07-menus.md)
- [Content](08-content/README.md) — articles and URLs.
- [Components](09-components/README.md) — one chapter per component's
  administrative side, from Answers to Wishlist.
- [Extensions](10-extensions/README.md) — the extension, module, template,
  and plugin managers.
- [Spam](11-spam.md) — the spam filters and how to tune them.
- [Security](security.md) — hardening the operating system and the
  CMS.

Every parameter you can set is also listed in the generated
[configuration reference](../reference/configuration/README.md).
