<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
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

## Does your hub need a new one

Probably not. The three levels a hub ships with — Public, Registered and
Special — cover the three audiences most hubs actually have: everyone, people
who have logged in, and staff. A hub that only needs to keep something behind
the login already has what it needs.

You need a fourth when a set of content must be visible to *some* logged-in
members and not to others: material for one partner institution, a policy page
for site staff that ordinary managers should not see, a set of pages for a
funded project. That is a level, built from an [access
group](06-accessgroups.md) holding exactly those accounts.

What it is not:

- **Not a permission.** A level decides who sees an item, not who may edit,
  publish or delete it. Someone excluded from a level can still be allowed to
  administer the component that holds the content.
- **Not a per-item setting.** You cannot name people on an item. You name a
  level on the item and put the people in a group the level lists.
- **Not security for a file.** A level hides an item from the site's own
  listings and views. It is not a guard on a URL that some other part of the
  hub serves. Do not use one to protect anything you would mind being fetched
  directly.

## What a change here does, and when

A level is resolved from the database on every request, so a change to it
lands on the next page load for everybody, logged in or not. Nobody is warned
and nothing is logged. Two consequences are worth having in front of you
before you edit one:

- **Editing a level changes every item already tagged with it, at once.** If
  forty articles carry **Registered** and you untick a group on that level,
  forty articles disappear for those people immediately. There is no preview
  and no list of what is affected. Take a note of the ticked groups before you
  change them; that note is the only undo you have.
- **Being a Super User does not help.** A user sees a level only if they
  belong to one of the access groups it lists, or to one of that group's
  ancestors. Super Users are not exempt. Build a level that names only your new
  group and your own account will not see the content either, which is
  disconcerting the first time and easy to misread as a broken page.

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

These three are sensible defaults and are worth leaving alone. Nearly every
content item on a new hub is tagged **Public**, so anything you do to that
level reaches the whole site.

A level names its access groups literally, but membership is resolved up the
tree: an account is treated as belonging to its groups and to all of their
ancestors. So the **Registered** level, which names the Registered group,
also admits Authors, Editors and Publishers, since Registered is their
ancestor. It does not work downwards — a new access group you add sees
nothing extra until you tick it on the levels it should reach.

> **Note:** The level with ID 1 is a special case. Every visitor is authorised
> for it whatever its rules say, so editing the groups on the shipped
> **Public** level has no effect: untick everything and it is still public. To
> take something off the open web, retag the item with a different level; do
> not try to narrow level 1.

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

Creating a level is safe. Until an item is tagged with it, a new level changes
nothing at all, so build it, look at the site through an ordinary account, and
delete it again if it is wrong.

**Save as Copy** duplicates the level you are editing, which is the quick way
to make a variant of an existing audience.

### Worked example: a viewing level for the workshop

Continuing the scenario from the [section
introduction](README.md#a-worked-example), the `Workshop 2026` access group
already exists but grants nothing. This gives it something to grant.

1. Go to **Users** → **Access Levels** and select **New**.
2. Enter a **Level Title** of `Workshop 2026`.
3. Under **Access Groups Having Viewing Access**, tick `Workshop 2026`. Tick
   **Super Users** as well, so that you and the other administrators can see
   the material you are about to hide — you are not admitted automatically.
4. Select **Save & Close**.
5. Open each article, page or menu item that belongs to the workshop and set
   its **Access** to `Workshop 2026`. Each one becomes invisible to everyone
   else the moment it is saved.
6. Check it. Log into the site with an ordinary member account, in another
   browser, and confirm the material is gone. Then put one test account into
   the `Workshop 2026` access group and confirm it comes back.

Step 6 is the whole point of the exercise. A Super User cannot tell by looking
whether the material is hidden, and a level that hides nothing looks exactly
like one that works.

When the workshop is over, take the accounts out of the access group. The
content stays tagged, the level stays in place, and the material simply stops
being reachable — which is reversible, unlike deleting either.

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

That check is the safety rail on this screen, and it is a good one: it means a
level you are still using cannot be deleted by accident. It also means the
usual failure is the opposite one — you are told you cannot delete a level and
cannot see what is holding it. Set the list filter on each content manager to
that level in turn to find the items, or leave the level alone; an unused level
costs nothing but a line on this list.

If a level has simply outlived its purpose, renaming it is usually better than
deleting it. Level titles are unique, so a rename also frees the name for reuse,
and it leaves the number in place for anything outside the interface that
refers to it.
