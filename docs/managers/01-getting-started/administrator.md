<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/index/administrator
source-id: 3337
imported: 2026-09-09
-->
# The administrator interface

The administrator interface, or back end, is where you set up, configure and
maintain a hub. It runs on the same site as the public front end but under a
separate URL, uses a different template, and is closed to anyone without an
explicit permission to enter it.

## Reaching it

Add `/administrator` to the hub's address. If the hub is at
`https://example.com`, the back end is at:

```
https://example.com/administrator
```

That first path segment is what selects the administrator application; there
is no separate hostname or port. Everything after it is an ordinary
`index.php?option=com_…` request handled by the same components as the front
end, only with their `admin/` controllers and views instead of their `site/`
ones.

## Logging in

Every administrator URL is rewritten to the login component until you are both
logged in and authorised, so it does not matter which back-end page you ask
for first — you always land on the login screen:

<!--include: core/bootstrap/Administrator/routes.php:88-105-->

The check is the `core.login.admin` permission, not simply "is logged in". A
member who is signed in to the front end and browses to `/administrator` is
still sent to the login screen if their access groups do not carry that
permission. On a fresh install `core.login.admin` is granted to the
**Manager** group and inherited by **Administrator** beneath it; **Super
Users** hold `core.admin`, which grants everything. See
[Access groups](../users/accessgroups.md) for how to change that.

The login screen itself is `com_login`, rendered through the admin template's
`login.php` layout with the **Login Form** module (`mod_adminlogin`) in it.
What you see depends on which authentication plugins the hub has enabled with
their **Admin login** option turned on:

- With no such plugin, you get a plain form: **Username**, **Password**, and a
  **Log in** button.
- With one or more, you get a **Sign in with …** button for each, plus a link
  back to the plain form for hub-local accounts.

A successful login lands you on the **Control Panel**.

> **Note:** Failing to log in here is not always a wrong password. If the
> account is valid but lacks `core.login.admin`, the login is refused in the
> same way. Check the member's access groups before resetting anything.

## The layout

Every back-end page is built from the same pieces, in this order down the
page:

| Region | What is in it |
|---|---|
| Header | The hub's name, linking to the front end, and the **Log out** link on the right |
| Main navigation | The top-level menus, rendered by the admin menu module in the `menu` position |
| Toolbar box | The page title on the left and its action buttons — **New**, **Save**, **Save & Close**, **Close**, **Options**, **Help** — on the right |
| Sub-navigation | The current component's own screens, when it registers any |
| Content | The component itself |

The toolbar is where the verbs live. A list screen puts **New**, **Edit**,
**Delete** and the status buttons there; an edit screen replaces them with
**Save**, **Save & Close** and **Close**. A component's **Options** button
opens its configuration in a modal, and the **Permissions** tab inside that
modal is where you say which access groups may use it.

## The Control Panel

The Control Panel is the component `com_cpanel`, and it is the default screen
for any administrator URL that names no component. It has no content of its
own: it renders every administrator module published in the `cpanel` position,
one collapsible panel per module. What your dashboard shows therefore depends
entirely on which modules the hub publishes there. [Daily
maintenance](../maintenance/README.md) describes the panels a working hub
usually adds.

## The menus

The main navigation is assembled by the admin menu module. Most of it is
hardcoded, and each entry is shown only if you hold the permission it needs,
so a Manager sees fewer entries than a Super User.

| Menu | Contains |
|---|---|
| **Site** | **Control Panel**, **Global Configuration**, a **Maintenance** submenu (**Global Check-in**, **Clear Cache**, **Purge Expired Cache**, **LDAP**, **Geo DB**, **APC**, **Routes**), **System Information**, and **Logout** |
| **Users** | **Members**, **Groups**, **Access Groups**, **Access Levels**, **User Notes** and their categories, and **Mass Mail Users** |
| **Menus** | **Menu Manager**, then one entry per menu defined on the hub |
| **Content** | **Article Manager**, **Category Manager**, and **Media Manager** |
| **Components** | One entry per installed, enabled component, with a submenu where the component defines one |
| **Extensions** | **Extension Manager**, **Module Manager**, **Plug-in Manager**, **Template Manager**, **Language Manager** |
| **Help** | **Help Articles** and links out to hubzero.org; the menu module's **Help Menu** option, on by default, hides it |

The **Components** menu is the one that grows. Unlike the rest, it is built
from the database — the administrator menu rows installed with each component
— rather than from the module's own code, which is why installing a component
adds it to that menu without any further step. Members, Groups and System are
deliberately left out of it because they already appear under **Site** and
**Users**.

> **Note:** **Global Configuration** and **System Information** appear only
> for accounts holding `core.admin`. The **Maintenance** submenu needs
> `core.admin` on the check-in component or `core.manage` on the cache
> component; without either, the whole submenu is left out.

## Logging out

Click **Log out** in the top-right corner of the header, or use **Site →
Logout**. Either returns you to the login screen.

While you have an item open for editing, the whole main navigation and the
**Log out** link are disabled: the record is checked out to you, and the
interface hides the links so you cannot navigate away and leave it locked.
Finish with **Save & Close** or **Close** to release it, and the links come
back.

> **Tip:** If a record stays locked — a browser crash, a lost session — clear
> it with **Site → Maintenance → Global Check-in**, which releases every
> record checked out across the hub.

## Where to go next

- [Advanced setup](../advancedsetup.md) — the site template, menus and
  modules.
- [Configuring](../configuring/README.md) — the global configuration and the
  per-extension options.
- [Users](../users/README.md) — members, access groups, and access levels.
- [Daily maintenance](../maintenance/README.md) — the work a running hub
  needs every day.
