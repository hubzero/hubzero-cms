<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/menus
source-id: 3366
modified: 2014-10-10
imported: 2026-09-09
-->
# Menus

A menu is a named list of links. It does two jobs on a hub: it gives visitors
a way to move around, and it gives the pages it points at their URLs. A page
no menu item points at is reachable but hard to find, and often has an
address nobody would guess.

Menus are managed at **Menus → Menu Manager**. The **Menus** menu also lists
every menu defined on the hub, with **Add New Menu Item** under each.

Two things are easy to confuse:

- A **menu** is the container. It has a system name (its *menu type*), a
  title, and a description.
- A **menu item** is one link inside it.

A menu is not displayed anywhere until a **Menu** module is pointed at it and
given a template position. Creating a menu and creating the module that shows
it are two separate steps.

## Menu Manager

**Menus → Menu Manager** opens **Menu Manager: Menus**. Each row shows the
menu's title, its menu type beneath, the number of published, unpublished and
trashed items in it, and the modules linked to it.

- The **title** opens the menu's items.
- The **menu type** opens the menu itself for editing.
- Under **Modules Linked to the Menu**, each module is a link to its
  settings. Where there is no module, the cell offers **Add a module for this
  menu type**.

The toolbar is **New**, **Edit**, **Delete**, **Rebuild**, **Options**, and
**Help**. **Delete** asks for confirmation first, and it means it: deleting a
menu deletes all of its menu items and the menu modules attached to it.
**Rebuild** repairs the nested-set bookkeeping behind the menu tree, which is
worth doing if items start appearing at the wrong depth.

### Creating a menu

1. **Menus → Menu Manager**, then **New**.
2. Fill in **Menu type**, the system name. It is what modules and menu items
   refer to, it must be unique, and it cannot sensibly be changed later.
   Whatever you type is lowercased and stripped of anything that is not a
   letter, a digit, or a hyphen, up to 24 characters; leave it blank and it
   is derived from the title.
3. Fill in **Title**, which is what the administrator interface shows, and
   optionally a **Description**.
4. **Save & Close**.
5. Back on the list, follow **Add a module for this menu type** to create the
   module that displays it. See [Modules](10-extensions/01-modules.md).

## Menu items

**Menu Manager: Menu Items** lists the items of one menu — pick the menu from
the first drop-down above the list, or open it from the Menu Manager. The
other filters are **Select Max Levels**, status, access level, and language,
and the search box matches the title or the alias.

The columns are **Title**, **Status**, **Ordering**, **Access**, **Menu Item
Type**, **Home**, **Language**, and **ID**. Sort by **Ordering** ascending to
reorder items; the list is a tree, so an item's indent is its depth.

The toolbar is **New**, **Edit**, **Publish**, **Unpublish**, **Check In**
(administrators only), **Trash**, **Home**, **Rebuild** (administrators
only), and **Help**. Filtering to **Trashed** replaces **Trash** with **Empty
trash**, which deletes for good. **Home** makes the selected item the site's
default page; there must always be exactly one.

### Adding a menu item

1. Open the menu and select **New**.
2. Next to **Menu Item Type**, select **Select**. A picker opens listing
   every type available on this hub, grouped by the extension that provides
   it, plus a **System Links** group at the end.
3. Choose a type. The form reloads with the fields that type needs.
4. Fill in **Menu Title**. Everything else has a usable default.
5. **Save & Close**.

The details are:

| Field | Notes |
|---|---|
| **Menu Item Type** | Set through the picker. Changing it changes the rest of the form. |
| **Menu Title** | Required. The text shown in the menu. |
| **Link** | Read-only for a component item — the picker fills it in. Editable for an **External URL** item. |
| **Alias** | This item's own path segment. Together with its parents' aliases it is the page's URL. Leave it blank and it is generated from the title. |
| **Note** | A private note, shown only in the administrator interface. |
| **Access** | Which viewing level sees the item. |
| **Status** | Published, Unpublished, or Trashed. |
| **Menu Location** | Which menu the item belongs to. Change it to move the item between menus. |
| **Parent Item** | **Menu Item Root**, or another item in the same menu. This is what nests items, and what builds the URL. |
| **Ordering** | Position among its siblings. Available once the item has been saved. |
| **Target Window** | **Parent**, **New Window With Navigation**, or **New Without Navigation**. |
| **Default Page** | Component items only. Makes this the site's home page. |
| **Language** | **All**, or one content language. |
| **Template Style** | A specific template style for this page, or the site default. |

Down the right-hand side are collapsible panels: the request fields the
chosen type declares (for **Single Article**, a **Select Article** picker),
**Link Type Options** (link title attribute, link CSS class, link image, and
whether to show the title next to the image), any layout options the type
declares, and **Module Assignment for this Menu Item**, which lists the
modules that will and will not show on this page.

> **Note:** A menu item's options are the ones its layout declares, in the
> component's `site/views/<view>/tmpl/<layout>.xml`. The component's own
> global options are *not* merged into the menu item form: the code that
> would load them is disabled behind an always-false condition, with a `TODO`
> saying that fixing it breaks the form. Recorded in
> It is recorded with the project.
### Where the types come from

The picker is built from every enabled component. For each one the hub looks
for a `<menu>` block in the component's `site/metadata.xml`. If there is
none — as for Articles — it scans `site/views/`, taking each view that is not
prefixed with an underscore and each layout XML file inside it, and uses the
title declared there. That is why Articles offers **Single Article**,
**Category Blog**, **Category List**, **List All Categories**, **Featured
Articles**, **Archived Articles**, and **Create Article**: one entry per
layout under `core/components/com_content/site/views`.

The **System Links** group at the end is fixed, and holds three types that
belong to no component:

| Type | What it does |
|---|---|
| **External URL** | Links to any address, on this hub or elsewhere. Also acts as a redirect — see [URLs](08-content/urls.md#redirecting-with-a-menu-item). |
| **Menu Item Alias** | Points at another menu item, so one page can appear in two menus. Leave the **Alias** field empty when the two items share a parent. |
| **Text Separator** | A label with no link, for breaking a long menu into groups. |

## Grouping articles under a menu

To give a set of articles a section of their own — an About section, say —
build the menu first and then show it:

1. Create the articles. See [Article Manager](08-content/articlemanager.md).
2. **Menus → Menu Manager → New**. Give the menu a **Menu type** of `about`
   and a **Title** of `About`. **Save & Close**.
3. Open the new menu and add one item per article, each of type **Articles →
   Single Article**, choosing the article in the **Select Article** field.
4. Give the section a parent: add one more item, set the other items'
   **Parent Item** to it, and their URLs become `/about/<alias>`.
5. Back on the Menu Manager, follow **Add a module for this menu type**.
6. In the module, set a **Title**, set **Status** to **Published**, choose a
   **Position** — `left` and `footer` exist in the templates that ship — and
   under **Select Menu** choose the menu you just made.
7. Under **Menu Assignment**, set **Module Assignment** to **Only on the
   pages selected** and tick, in **Menu Selection**, the menu items the
   module should appear on.
8. **Save & Close**.

The module's other options are **Start Level**, **End Level**, **Show
Sub-menu Items**, **Show as Disclosure Menu**, and **Show Top Level Items as
Links**; the advanced tab adds a menu tag ID, a menu class suffix, a layout,
and caching. There is no "Menu Style" option. Older documentation describes
one; it was removed years ago.

## Redirecting a URL

A menu item of type **External URL** whose route matches the requested path
redirects to its link, which is how one hub address is pointed at another
without moving any content. The Redirect Manager is usually the better tool;
both are covered in [URLs](08-content/urls.md#redirects).

## Options

**Options** on the Menu Manager sets the **Page Display Options** every menu
item inherits — browser page title, whether to show the page heading, the
page heading text, and a page class suffix — and the component permissions.
They are listed in the
[Menus configuration reference](../reference/configuration/components/menus.md).
