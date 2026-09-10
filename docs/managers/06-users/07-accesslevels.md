<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/users/accesslevels
source-id: 3362
modified: 2016-07-12
-->
# Access Levels

An access level is a named audience. Content items — articles, menu items,
modules, categories — each carry an **Access** setting, and its value is one
of these levels. A level is defined as a list of
[access groups](06-accessgroups.md); a user sees the item if they belong to
any group on that list.

Levels answer "who may see this". They say nothing about who may change it;
that is the permission rules on access groups.

Administer them at **Users** → **Access Levels**, or directly at
`/administrator/index.php?option=com_members&controller=accesslevels`.

> **Note:** The menu calls them **Access Levels**; the screen itself is
> titled **Members: Viewing Levels**, and the field on the edit form is
> **Access Groups Having Viewing Access**. Both names mean the same thing.
> The screen shares a sub-navigation bar with
> [Access Groups](06-accessgroups.md), and the same access rules apply to
> both.

## The levels shipped with a hub

A new hub is installed with three:

| Level | Access groups that see it |
|---|---|
| **Public** | Public — that is, everyone, including visitors who are not logged in |
| **Registered** | Manager, Registered, Super Users |
| **Special** | Manager, Author, Super Users |

A level names its access groups literally, but membership is resolved up the
tree: an account is treated as belonging to its groups and to all of their
ancestors. So the **Registered** level, which names the Registered group,
also admits Authors, Editors and Publishers, since Registered is their
ancestor. It does not work downwards — a new access group you add sees
nothing extra until you tick it on the levels it should reach.

## The list

Each row shows the level's **ID**, its **Level Name** and its **Ordering**.
Sort by name or ordering, and search by name with the filter bar. The
toolbar carries **New**, **Edit**, **Delete** and **Options**.

> **Note:** Open a level by selecting its name. As on the access group list,
> the toolbar's **Edit** button looks for the selection under a request name
> the list does not use and opens a blank new-level form instead.

> **Note:** The ordering column is display only in 2.4. The reorder arrows
> never appear and the ordering boxes are always disabled, because the
> screen tests the sort against a column name the list never uses and the
> controller has no task to save a new order. Ordering only affects the
> sequence levels appear in on an item's **Access** menu, so this is
> cosmetic.

## Creating an access level

1. Go to **Users** → **Access Levels**.
2. Select **New**.
3. Enter a **Level Title**.
4. Under **Access Groups Having Viewing Access**, tick every access group
   that should see content assigned to this level.
5. Select **Save & Close**.

The level is then offered in the **Access** menu of every content item.

**Save as Copy** duplicates the level you are editing, which is the quick way
to make a variant of an existing audience.

## Editing an access level

1. Go to **Users** → **Access Levels**.
2. Select the level's name.
3. Change the title or the ticked groups, and select **Save & Close**.

A change takes effect at once, on every item already assigned to the level.

## Deleting an access level

1. Go to **Users** → **Access Levels**.
2. Tick the box beside one or more levels.
3. Select **Delete**.

A level that is in use is refused, with the message *You cannot delete the
view access level '…' because it is being used by content*. The check scans
every table in the database that has an `access` column and collects the
values in use, so it catches components the screen knows nothing about. Move
the content to another level first, then delete.
