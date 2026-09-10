<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/index
source-id: 3336
imported: 2026-09-09
-->
# Getting started

This book is for the person who runs a hub. It covers the administrator
interface — the back end at `/administrator` — and the settings, screens and
extensions you reach from it. It starts from a hub that is already installed
and answering; it does not cover installing the software.

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

[The administrator interface](administrator.md) explains how to reach the back
end, who is allowed in, and what each of its menus contains. Read it before
anything else in this book; every other chapter assumes you can find your way
around those screens.

Then, if the hub was installed this week, work through
[The first week with a new hub](02-newhub.md). It puts the decisions a new hub
needs — the site name, mail, registration, access, which components to keep,
the front page and menu, and the scheduled jobs — into the order that works,
and links to the chapter covering each one.

## Then

- If the hub is new, go on to [Advanced setup](../02-advancedsetup.md) and
  [Configuring](../05-configuring/README.md). Between them they cover the global
  configuration, the site template, the menus, and registration — the choices
  that are hardest to change once members have started using the hub.
- If you are taking over a hub that is already running, go to
  [Daily maintenance](../03-maintenance/README.md) and
  [Users](../06-users/README.md).
- If you are looking for one particular screen, the
  [Components](../09-components/README.md) and
  [Extensions](../10-extensions/README.md) chapters are organised the same way
  the **Components** and **Extensions** menus are.

Every setting the interface offers is also listed, component by component, in
the generated
[configuration reference](../../reference/configuration/README.md).
