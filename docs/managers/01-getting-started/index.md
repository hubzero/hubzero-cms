<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
-->
# Getting started

This book is for the person who runs a hub. It covers the administrator
interface — the back end at `/administrator` — and the settings, screens and
extensions you reach from it. It starts from a hub that is already installed
and answering; installing the software is
[System administrators](../../sysadmins/index.md).

## What a hub manager does

A hub is a Hubzero site: a public front end that members browse, and a private
back end where you configure it. Almost everything in this book happens in the
back end. The recurring jobs are:

- Configure the site: its name, its template, its menus, and the components
  that provide its features.
- Manage members: their accounts, the access groups they belong to, and what
  each group is allowed to do.
- Curate content: articles, resources, publications, and whatever the hub's
  members submit for approval.
- Install and configure extensions: components, modules, plugins, and
  templates.
- Keep the site healthy: support tickets, cron jobs, site notices, and the
  spam filters.

## Read this first

Start with [How a hub fits together](03-how-a-hub-fits-together.md) if you
are new to Hubzero. It explains components, modules, plugins, and templates
with a worked example, then shows what lives in the database and in files.

Next, [The administrator interface](01-administrator-interface.md)
explains how to reach the back end, who is allowed in, and what each of its
menus contains.

Then, if the hub was installed this week, work through
[The first week with a new hub](02-first-week.md). It puts the decisions a new hub
needs — the site name, mail, registration, access, which components to keep,
the front page and menu, and the scheduled jobs — into the order that works,
and links to the chapter covering each one.

## Then

- If the hub is new, go on to [Advanced setup](../02-advancedsetup.md) and
  [Configuring](../05-configuring/index.md). Between them they cover the global
  configuration, the site template, the menus, and registration — the choices
  that are hardest to change once members have started using the hub.
- If you are taking over a hub that is already running, go to
  [Daily maintenance](../03-maintenance/index.md) and
  [Users](../06-users/index.md).
- If you are looking for one particular screen, the
  [Components](../09-components/index.md) and
  [Extensions](../10-extensions/index.md) chapters are organised the same way
  the **Components** and **Extensions** menus are.

Every setting the interface offers is also listed, component by component, in
the generated
[configuration reference](../../reference/configuration/index.md).
