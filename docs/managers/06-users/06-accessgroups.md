<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/users/accessgroups
source-id: 3361
modified: 2016-07-12
-->
# Access Groups

An access group is a permission bucket. Every account belongs to one or more
of them, and permissions throughout the hub — who may manage a component,
who may edit an article, who counts as a Super User — are granted to access
groups rather than to individual accounts. Access groups form a tree, and a
child inherits everything its parent is allowed.

> **Note:** An access group is not a hub group. It has no pages, no forum and
> no address on the site, and users cannot join one. Hub groups are covered
> in [Groups](05-groups.md).

Access groups are administered by `com_members`, at **Users** → **Access
Groups**, or directly at
`/administrator/index.php?option=com_members&controller=accessgroups`. The
screen itself is titled **Members: Access Groups**, and it shares a
sub-navigation bar with [Access Levels](07-accesslevels.md).

The **Access Groups** and **Access Levels** entries on the Users menu appear
only for a Super User; both are gated on `core.admin` for `com_members`.

> **Note:** That gate is on the menu, not on the screen. Reaching the screen
> itself needs only `core.manage` on `com_members`, and the **Access** entry
> in the component's own sub-navigation is not gated at all. A manager who
> knows the URL, or who is already inside Members, can see the access group
> list. The toolbar buttons are still checked individually, and a non-Super
> User cannot edit or delete a group that holds `core.admin`.

## The groups shipped with a hub

A new hub is installed with eight access groups, nested like this:

| Group | Parent |
|---|---|
| **Public** | — |
| **Manager** | Public |
| **Administrator** | Manager |
| **Registered** | Public |
| **Author** | Registered |
| **Editor** | Author |
| **Publisher** | Editor |
| **Super Users** | Public |

The names carry no meaning of their own. What each group may do is decided
entirely by the permission rules set against it, in the global configuration
and on each component's **Options** screen. The exception is **Super Users**,
which holds `core.admin` on the root asset and so is allowed everything.

New accounts are placed in the group named by the **New User Registration
Group** option of `com_members`, which is Registered by default. Visitors who
are not logged in count as members of the **Guest Access Group**, Public by
default. Both are on the component's **Options** screen and are listed in the
[Members configuration reference](../../reference/configuration/components/members.md).

## The list

Each row shows the group's **ID**, its **Group Title** indented by depth in
the tree, and **Users in group**. Search by title with the filter bar.

The toolbar carries **New**, **Edit**, **Delete** and **Options**.

> **Note:** Open a group by selecting its title, not by ticking it and
> selecting **Edit**. The **Edit** button looks for the selection under a
> request name the list does not use, so it opens a blank new-group form
> instead of the group you ticked. **Delete** is unaffected.

When the site is in debug mode each row grows a **Debug Group** button, which
opens a screen listing every asset on the hub and what that group is
permitted to do with it. That is the quickest way to answer "why can this
person do that".

## Creating an access group

1. Go to **Users** → **Access Groups**.
2. Select **New**.
3. Enter a **Group Title** and choose a **Group Parent**. Both are required.
4. Select **Save & Close**.

**Save as Copy**, on an existing group, duplicates it under a new title.

The new group starts with no permissions of its own and inherits whatever its
parent is allowed. Grant it something by editing the permission rules in the
global configuration, or under **Options** on the component it should reach.

## Assigning an account to a group

Access group membership is set on the member record, not here.

1. Go to **Users** → **Members**.
2. Open the account.
3. On the **Account** tab, tick the groups under **Assigned Access Groups**.
4. Select **Save & Close**.

## Deleting an access group

1. Go to **Users** → **Access Groups**.
2. Tick the box beside one or more groups.
3. Select **Delete**.

Three rules are enforced, and a group that trips one is skipped with a
warning rather than failing the whole batch:

- You cannot delete a group you yourself belong to.
- You cannot delete a group holding `core.admin` unless you are a Super User.
- You need `core.edit.state` on `com_members`.

> **Warning:** Nothing stops you deleting Public or Registered. Both are
> referenced by the default access levels and by the registration options, so
> deleting either breaks the site. Leave the eight shipped groups alone and
> add your own alongside them.
