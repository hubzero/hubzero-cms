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

It is written for whoever ended up running the hub. That is usually not a
full-time administrator but a researcher, a lab manager, or somebody who
inherited the site along with three other jobs. So each chapter says what a
screen is for before it lists the fields. It says where a change is hard to
undo, reaches people already using the hub, or cannot be tried out first.
And where something does not work — an inert button, a dead filter, a
setting nothing reads — it says so, rather than describing the intention.

This book is not a field-by-field reference; that is the generated
[configuration reference](../reference/configuration/index.md). It is not
about writing code for a hub, which is [Developers](../developers/index.md).

It starts from a hub that is installed and answering. Putting one on a
server, and upgrading it later, is
[System administrators](../sysadmins/index.md).

## Where to start

If the hub is new, read
[The administrator interface](01-getting-started/01-administrator-interface.md) and then
[The first week with a new hub](01-getting-started/02-first-week.md). The first
covers the login and the back end's menus. The second takes you in order
through the choices that are hard to change later: mail, registration,
access, the front page, and the scheduled jobs. It links to the chapter
covering each. [Configuring](05-configuring/index.md) has the settings
screens in full.

If the screens make sense one at a time but not together, read
[How a hub fits together](01-getting-started/03-how-a-hub-fits-together.md). The rest
of this book takes it for granted. It explains the two halves of the
application and how a page is built from a component, a template and its
modules. It also says what lives in the database and what lives on disk,
and what a manager can and cannot break.

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
