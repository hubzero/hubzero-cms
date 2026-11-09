<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
-->
# Hub managers

The administrator's guide. A hub manager configures the hub, manages its
members and their access, curates its content, installs and configures
extensions, and keeps the site healthy. Almost all of that happens in the
administrator interface at `/administrator`, which this book walks through
screen by screen.

You do not need to write PHP or administer a server to use this book. You
need an account allowed into the administrator interface; an ordinary member
account may not have that permission. Each chapter explains what a screen
does before listing its fields, and identifies changes that delete content,
send messages, or affect people already using the hub.

This book is not a field-by-field reference; that is the generated
[configuration reference](../reference/configuration/index.md). It is not
about writing code for a hub, which is [Developers](../developers/index.md).

It starts from a hub that is installed and answering. Putting one on a
server, and upgrading it later, is
[System administrators](../sysadmins/index.md).

## Where to start

For a new hub, or if you are new to Hubzero, read these in order:

1. [How a hub fits together](01-getting-started/03-how-a-hub-fits-together.md)
   explains the public site, administrator interface, and the extensions
   that build a page. It also explains where the hub stores its content.
2. [The administrator interface](01-getting-started/01-administrator-interface.md)
   covers signing in, the menus, and the controls shared by many screens.
3. [The first week with a new hub](01-getting-started/02-first-week.md)
   takes you through mail, registration, access, the front page, and
   scheduled jobs, with links to the detailed settings chapters.

[Configuring](05-configuring/index.md) covers the settings screens in full.

If you are taking over a running hub, start with
[Daily maintenance](03-maintenance/index.md) and
[Users](06-users/index.md).

If you are here because something has gone wrong, try
[Frequently asked questions](04-faq.md) first, then the chapter for the
screen in question. If a hub that has just gone public fills up with junk
accounts and posts, read [Spam](11-spam.md). Better still, read it before
that happens.

## In this book

- [Getting started](01-getting-started/index.md) — the administrator
  interface and the first configuration.
- [Integrations](02-advancedsetup.md) — connecting the hub to analytics,
  Google Drive, the rich text editor, and CAPTCHA.
- [Daily maintenance](03-maintenance/index.md) — approving content,
  support tickets, cron jobs, notices, and tools.
- [Frequently asked questions](04-faq.md)
- [Configuring](05-configuring/index.md) — hub settings, components,
  modules, plugins, registration, and authentication.
- [Users](06-users/index.md) — members, access groups and levels, groups
  and super groups, member import, and user notes.
- [Menus](07-menus.md)
- [Content](08-content/index.md) — articles and URLs.
- [Components](09-components/index.md) — one chapter per component's
  administrative side, from Answers to Wishlist.
- [Extensions](10-extensions/index.md) — the extension, module, template,
  and plugin managers.
- [Spam](11-spam.md) — the spam filters and how to tune them.
- [Security](security/index.md) — hardening the operating system and the
  CMS.

Every parameter you can set is also listed in the generated
[configuration reference](../reference/configuration/index.md).

> **Note:** Where this book gives a default, it is the value a hub installed
> from the shipped data actually has. That is not always the default a
> component's manifest declares, and the generated reference is built from
> the manifests. Where the two disagree, the stored value wins and this book
> says so. Check the setting on your own hub before relying on either.
> The two clearest cases are in
> [Check the default, do not trust it](01-getting-started/02-first-week.md#check-the-default-do-not-trust-it).
