<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
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

## Does your hub need this screen at all

Most hubs never touch it. The eight groups a hub ships with cover the ordinary
shape of a site — visitors, members, people who write content, people who
publish it, people who administer it — and the interesting work is done on the
**Permissions** tab of the global configuration and of each component, which
grants those groups their rights. That is where you go when the question is
*what may this person do*.

You need a new access group only when you have a set of accounts that must be
treated differently from every existing set, and permanently enough to be
worth naming. Two cases come up in practice: a group of people who need to
reach something ordinary members cannot see, and a group of people who need to
run one part of the administrator interface without being made administrators
of the whole hub.

If your question is instead "how do I let these forty people see this
material", you want a new access group *and* a viewing level built from it;
the level is the half that content is tagged with. See
[Access Levels](07-accesslevels.md).

## What a change here does, and when

Access group membership is read from the database on every request. Nothing is
cached between requests and nobody is logged out, so:

- Putting an account into a group, or taking it out, takes effect on that
  person's next page load, whether or not they are logged in at the time.
- Changing a group's place in the tree changes what everyone in it, and
  everyone in the groups beneath it, is allowed to do — also on the next
  request.
- Nobody is told. There is no notification and no entry in any log the
  administrator interface shows you. A member who loses access finds out by
  meeting a page that is no longer there.

This is why adding is safer than editing. A new group grants nothing until you
write a permission rule or a viewing level that names it, so you can create it,
look at it, and delete it again with no consequence at all.

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
Accounts created by an administrator and accounts created by
[import](03-memberimport.md) are placed in the same group; the importer falls
back to Registered by name if the option is unset.

Both defaults are sensible and there is rarely a reason to change either. In
particular, do not point **New User Registration Group** at a group with more
rights than Registered as a way of giving a batch of new people extra access —
it applies to *every* subsequent registration, including the ones you did not
expect. Put the batch in an extra group afterwards instead.

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

**Users in group** counts accounts assigned to that group directly. It does
not count the accounts that reach the group's rights by inheritance from a
child group, so a group showing zero users can still be granting permissions
to plenty of people.

## Creating an access group

1. Go to **Users** → **Access Groups**.
2. Select **New**.
3. Enter a **Group Title** and choose a **Group Parent**. Both are required.
4. Select **Save & Close**.

**Save as Copy**, on an existing group, duplicates it under a new title.

The new group starts with no permissions of its own and inherits whatever its
parent is allowed. Grant it something by editing the permission rules in the
global configuration, or under **Options** on the component it should reach.

Choose the parent carefully, because it is the whole of what the new group can
already do. A group under **Public** starts with the rights of a passing
visitor; a group under **Registered** starts with the rights of an ordinary
member. For a set of accounts who are members and also something more,
**Registered** is the parent you want. Never hang a new group under **Super
Users** unless you mean everyone in it to be allowed everything on the hub.

The parent can be changed later, and doing so takes effect immediately, so an
access group is one of the few things in this section you can genuinely
correct after the fact — provided you have not yet attached anything to it.

### Worked example: an access group for the workshop cohort

Continuing the scenario from the [section introduction](README.md#a-worked-example),
the forty visiting participants need to reach workshop material that ordinary
members cannot see.

1. Go to **Users** → **Access Groups** and select **New**.
2. Enter a **Group Title** of `Workshop 2026`.
3. Set **Group Parent** to **Registered**, so the cohort keeps every ordinary
   member right and gains the workshop's on top.
4. Select **Save & Close**.

At this point the group does nothing. Nobody is in it and nothing names it. The
next step is a viewing level built from it — see
[Worked example: a viewing level for the workshop](07-accesslevels.md#worked-example-a-viewing-level-for-the-workshop) —
and then the accounts, which are put into the group either one at a time on the
member record or in bulk by [import](03-memberimport.md).

When the workshop ends, empty the group rather than deleting it. Emptying it
removes the access and leaves the level, the tagged content and the group
itself intact, so the same arrangement works again next year.

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

Deleting a group of your own is not gentle either. The delete does more than
remove the row, and none of it can be undone from the interface:

- **Every group beneath it goes too.** The delete takes the whole subtree, not
  just the group you ticked, and the count in the confirmation does not say so.
- **Every account is removed from those groups**, losing whatever they granted,
  on that person's next page load.
- **The group is stripped out of every viewing level that named it.** A level
  built only on the group you deleted is left with an empty list, and content
  tagged with it becomes invisible to everyone.

The permission rules on components and on the global configuration are *not*
cleaned up; they go on naming an id that no longer exists, harmlessly, until
somebody reuses that id.

So if you only want to withdraw access, take the accounts out of the group and
leave the group in place. Delete a group only when you are sure nothing is
built on it, and check its viewing levels first.
