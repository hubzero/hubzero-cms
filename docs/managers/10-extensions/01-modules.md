<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
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

A position is just a name. The template decides where on the page a position
appears, and which positions exist at all; a position no template renders
produces nothing. The positions a template offers are declared in its
`templateDetails.xml`.

**kimera**, the site template, declares:

`footer`, `banner`, `welcome`, `left`, `right`, `helppane`, `user3`,
`introblock`, `notices`, `search`

Its `index.php` also renders `breadcrumbs` and `endpage`, which are not in
the declared list, and does not render `banner` or `introblock`, which are.
So the picker is a guide, not a guarantee — check the template you are
actually using.

**kameleon**, the administrator template, declares:

`menu`, `submenu`, `toolbar`, `title`, `status`, `icon`, `cpanel`, `debug`

To see the positions of the live template laid out on the page, set
**Preview Module Positions** to Enabled in the Template Manager's **Options**,
then append `?tp=1` to any site URL. Each position is drawn as a labelled
outline.

You can also type a position no template defines and pull the module into
article text with the Content - Load Module plugin, which expands
`{loadposition myposition}` and `{loadmodule mod_login}` wherever they appear
in content. The plugin must be enabled for either to work.

## Putting a notice on every page

`mod_notices` renders a coloured banner across the site, for maintenance
windows and hub-wide announcements.

1. Go to **Extensions** → **Module Manager**.
2. Select **New** and choose **Site Notices** (`mod_notices`).
3. Fill in the details:
   - **Title** — for example, *Upgrade notice*. Set **Show Title** to Hide
     unless you want it printed above the message.
   - **Position** — `notices`.
   - **Status** — Published.
   - **Access** — the level that should see the announcement.
   - **Start Publishing** and **Finish Publishing** — the window the notice
     should appear in. A notice with a finish date takes itself down.
4. Set the module's own parameters:
   - **Alert level** — Low, Medium or High. This picks the colour.
   - **Message** — the text of the notice.
   - **Module ID** — an optional CSS id for styling this notice alone.
   - **Allow closing** — lets the reader dismiss the notice.
   - **Autolink message** — turns URLs and email addresses in the message into
     links. On by default.
5. Under **Menu Assignment**, choose **On all pages**.
6. Select **Save & Close**.

> **Note:** The `notices` position exists in **kimera** and **lucent**. A
> template that does not declare it will not render the notice.

## Permissions

`com_modules` has no configuration options of its own. Its **Options** button
opens a single **Permissions** tab, which sets who may configure, access,
create, delete, edit and change the state of modules.
