<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/extensions/modules
source-id: 3406
modified: 2009-10-01
imported: 2026-09-09
-->
# Module Manager

Modules are the small blocks of output that sit around the component: a login
box, a breadcrumb trail, a search field, a site-wide notice. The Module
Manager creates them, places them in a template position, decides which pages
they appear on, and orders them within a position.

Select **Extensions** → **Module Manager** in the administrator interface, or
go to `/administrator/index.php?option=com_modules`.

Every hub uses this screen, whether or not anyone has opened it, because the
menu down the side of the site and the login box in the corner are modules.
You come here to add a block, to move one, to stop one appearing on a
particular page, or — most often — to find out why one is not appearing at
all. That last case is nearly always
[a position problem](#positions).

This chapter covers creating modules, positions and page assignment. The
parameters inside a module — how many items it lists, which feed it reads —
are covered in [Modules](../05-configuring/04-modules.md).

## What the Module Manager is not

It is not where modules are installed. The hundred module types that ship
with the platform arrive with the code; a hub's own arrive through the
[Extension Manager](04-extension-manager.md). This screen only creates
*instances* of types that already exist.

It is not the menu editor either. `mod_menu` renders a menu, but the menu's
items are built in [Menus](../07-menus.md); the module only decides where the
result appears and on which pages.

## Modules and module instances

The code for a module lives in [`core/modules/`](../../../core/modules), one
directory per module, named `mod_something`. A hub's own modules go in
`app/modules/` and take precedence over a core module of the same name. One
hundred modules ship with the platform: 78 for the site and 22 for the
administrator interface.

Each directory holds `mod_name.php`, a `mod_name.xml` manifest that declares
the module's parameters, and a `tmpl/` directory of layouts. All but one also
carry a `helper.php` holding the class that does the work, which
`mod_name.php` requires and runs:

<!--include: core/modules/mod_login/mod_login.php-->

The Module Manager does not list those directories. It lists *instances*.
Creating a module makes a row in `#__modules` that points at one of the
installed module types and carries its own title, position, access level,
parameters and page assignment. Ten instances of `mod_custom` are ten
different blocks of HTML sharing one piece of code.

> **Note:** A module type must be enabled in the
> [Extension Manager](04-extension-manager.md) before any of its instances
> render. The site query joins `#__extensions` and skips modules whose
> extension row is disabled, whatever the instance's own status says.

## The list

The list shows the modules for one client at a time — **Site** or
**Administrator** — chosen from the **Client** filter. Columns:

| Column | Meaning |
|---|---|
| Checkbox | Selects rows for the toolbar buttons. The header checkbox selects all. A module whose files are missing has no checkbox and cannot be selected. |
| **Title** | The instance title. Click it to edit. A module note, if set, appears beneath. |
| **Status** | Published, unpublished or trashed. Click the icon to toggle. |
| **Position** | The template position the module renders in, or `:: None ::`. |
| **Ordering** | Order within the position. Editable only while the list is sorted by this column. |
| **Module** | The module type, for example `mod_login`. |
| **Pages** | Page assignment: **All**, **None**, **Selected only** or **All except selected**. |
| **Access** | The [access level](../06-users/07-accesslevels.md) required to see the module. |
| **Language** | The content language the module is limited to, or **All**. |
| **ID** | The row's primary key. Not editable. |

If a module's files have been removed from disk, the row loses its checkbox
and its title link, carries the message *Module file(s) not found!*, and is
shown as unpublished.

## Filters

Above the list, a search box matches the title, and six drop-downs narrow the
list further. They combine.

| Filter | Values |
|---|---|
| **Client** | Site, Administrator |
| **Status** | Published, Unpublished, Trashed |
| **Position** | Every position currently in use by a module of this client |
| **Module** | Every installed module type for this client |
| **Access** | Any defined access level |
| **Language** | Any installed content language |

## Toolbar

| Button | Effect |
|---|---|
| **New** | Opens the module-type chooser in a pop-up. Pick a type to create an instance of it. |
| **Edit** | Opens the selected module. |
| **Duplicate** | Copies the selected modules. The copies are created unpublished. |
| **Publish** / **Unpublish** | Changes the state of the selected modules. |
| **Check In** | Releases modules left checked out by an interrupted edit. |
| **Trash** | Moves the selected modules to the trash. |
| **Empty trash** | Replaces **Trash** when the Status filter is set to Trashed. Deletes permanently. |
| **Options** | Component permissions; there are no other settings. |
| **Help** | Opens the built-in help screen. |

A batch panel below the list applies an access level, a language or a position
to every selected module at once. It is hidden unless you hold create, edit
and edit-state permission on `com_modules`.

## Creating and editing a module

**New** opens a pop-up listing every *enabled* module type for the current
client, by translated name and by directory name, with the manifest
description as a tooltip. A module type disabled in the Extension Manager does
not appear here. Choosing a type opens the edit form; the type is fixed once
the instance exists.

The left column holds the details every module shares:

| Field | Notes |
|---|---|
| **Title** | Required, up to 100 characters. |
| **Show Title** | Show or Hide. Whether the template prints the title above the module. |
| **Position** | The template position. Type one, or use **Select position** for a picker listing every position declared by an installed template plus every position already in use. |
| **Ordering** | Position within the chosen position. |
| **Status** | Published, Unpublished or Trashed. |
| **Access** | The access level required to see the module. |
| **Start Publishing** / **Finish Publishing** | Optional dates that bracket when the module renders. |
| **Language** | Limits the module to one content language, or **All**. |
| **Note** | A private label shown under the title in the list. |
| **ID** | Read-only. |

The right column shows the module type, the client, and the description from
the module's XML manifest, none of which can be edited. Below them, the
module's own parameters appear in collapsible panels named by the manifest.
For a module with no parameters the panel is empty.

An instance of `mod_custom` — or a module with no XML manifest — also gets a
**Custom output** editor for arbitrary HTML.

### Menu assignment

Site modules get a **Menu Assignment** section. **Module Assignment** offers
four choices:

- **On all pages**
- **No pages**
- **Only on the pages selected**
- **On all pages except those selected**

The last two reveal a tabbed tree of every menu item, one tab per menu, with
**All**, **None** and **Invert** buttons. Administrator modules have no menu
assignment.

## Positions

Positions are what managers get wrong, and the reason is that getting them
wrong produces no error of any kind. A module in a position the live template
does not draw is published, correct, present in the list — and invisible.
Nothing on the Module Manager screen tells you which positions those are.

A position is just a name. The template decides where on the page a position
appears, and which positions exist at all; a position no template renders
produces nothing. The positions a template offers are declared in its
`templateDetails.xml`.

**kimera** and **lucent**, the older site templates, both declare:

`footer`, `banner`, `welcome`, `left`, `right`, `helppane`, `user3`,
`introblock`, `notices`, `search`

kimera's `index.php` also renders `breadcrumbs` and `endpage`, which are not
in the declared list, and does not render `banner` or `introblock`, which
are. So the picker is a guide, not a guarantee — check the template you are
actually using.

**hubzero**, the site template in `app/templates/`, declares:

`footer`, `banner`, `welcome`, `left`, `right`, `helppane`, `user3`,
`notices`, `search`, `breadcrumbs`, `endpage`

It has dropped `introblock` and declares the two kimera only rendered. A
module in `introblock` therefore renders under one of these site templates
and not the other, which is worth knowing before you change the site's
template style.

**kameleon**, the administrator template, declares:

`menu`, `submenu`, `toolbar`, `title`, `status`, `icon`, `cpanel`, `debug`

The **Select position** picker lists two things together: every position
declared by an installed template, and every position some module is already
sitting in. Its second column names the templates that declare each one. A
position with an empty second column is in use by a module and declared by
nothing — which is the picker's way of showing you a module that cannot
render, if you know to read it that way.

To see the positions of the live template laid out on the page, set
**Preview Module Positions** to Enabled in the Template Manager's **Options**,
then append `?tp=1` to any site URL. Each position is drawn as a labelled
outline, including the empty ones. This is the only reliable way to find out
what the template you are running actually draws, and it is worth doing once
before you place anything.

You can also type a position no template defines and pull the module into
article text with the Content - Load Module plugin, which expands
`{loadposition myposition}` and `{loadmodule mod_login}` wherever they appear
in content. The plugin must be enabled for either to work.

### Why a fresh hub looks empty

A newly installed hub shows no menu, no login box and no sidebar, and a
manager's first instinct is to go looking for the content. The content is
fine. Two separate things in the shipped install data are responsible, and
both live in this section of the book.

**The default site style draws no modules at all.** The install data ships
three template styles and makes **Welcome Template** the default for the
site. That template declares no positions and its `index.php` contains no
module include of any kind, so nothing you do in the Module Manager appears
on the front end while it is the default. It is a splash page for a hub that
has just been installed, not a template to run a hub on. The **Get started**
link on that splash page — the same as visiting `/?getstarted=1` — switches
the default site style to **kimera** and sends you to the getting started
page. Doing it from the [Template Manager](02-templates.md) has the same
effect.

**The main menu ships in a position no site template declares.** The install
data creates one site module, *Main Menu*, published, assigned to all pages,
and placed in a position called `position-7`. No site template in the tree
declares `position-7` — not kimera, not lucent, not hubzero; nothing draws
it. Only the optional sample data moves
it — it runs an update that sets the position to `user3` and hides the
title — so a hub installed without the sample data has a main menu that is
switched on and cannot be seen. The shipped *Login Form* module is in
`position-7` as well.

So the sequence on a new hub is: change the site's default style away from
Welcome, then move the Main Menu module into a position the new template
draws. Neither is obvious, and neither reports anything if you skip it.

## Making the main menu visible

This is the task above, done once, on a hub that has just been installed and
whose default style is already **kimera**.

1. Go to **Extensions** → **Module Manager**.
2. Leave the **Client** filter on **Site**.
3. Select **Main Menu** in the list. Its **Position** column reads
   `position-7`.
4. In the edit screen, select **Select position** beside **Position**.
5. Search the picker for `user3` and select it. The picker's second column
   shows which templates declare it — confirmation that something will draw
   it, which `position-7` does not have.
6. Check that **Status** is **Published** and that **Menu Assignment** is
   **On all pages**.
7. Select **Save & Close**.
8. Open the site in another tab. The menu is there.

If it is not, the position is drawn by a different template from the one the
site is running. Turn on **Preview Module Positions** and load the site with
`?tp=1` to see what the live template really offers.

> **Note:** This changes what every visitor sees, immediately. It is entirely
> reversible — set the position back — but there is no draft state and no
> preview of the change itself.

## Adding a site notice

The other module a manager creates by hand is `mod_notices`, the coloured
banner used for maintenance windows and hub-wide announcements. It follows
the same shape as the task above, with **New** in place of picking an
existing row: choose **Site Notices** from the type pop-up, fill in the
**Details**, set the position, choose **On all pages** under **Menu
Assignment**, then **Save & Close**.

Its position is `notices`, which kimera, lucent and hubzero all declare, and
it is worth setting **Finish Publishing** so the notice takes itself down.
Its own parameters — **Alert level**, **Message**, **Module ID**, **Allow
closing**, **Autolink message** — and the rest of the detail are in
[Site Notices](../03-maintenance/04-notices.md).

> **Note:** A template that does not declare `notices` will not render the
> notice, with the same silence as any other unrendered position.

## Permissions

`com_modules` has no configuration options of its own. Its **Options** button
opens a single **Permissions** tab, which sets who may configure, access,
create, delete, edit and change the state of modules.
