<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/users
source-id: 3354
modified: 2014-10-10
-->
# Users

Everything about the people on your hub: their accounts, the hub groups they
form, the access control that decides what each of them may see and do, and
the notes administrators keep about them.

## Read this before you change anything here

This is the section where a mistake costs the most. The rest of the
administrator interface decides what the hub contains; this decides who may
reach it. An access group, a viewing level or a group membership you change
here applies to the whole hub on the next page request. Nobody is logged out,
nothing is cached, and there is no confirmation step beyond the one on the
button. The people affected are the ones already using the site.

None of it has an undo. Some of it is genuinely irreversible — deleting an
account, de-identifying one, deleting a hub group — and the rest is only
reversible in the sense that you can retype what you replaced, if you wrote it
down first.

Three habits cover most of the risk.

- **Add rather than edit.** A new access group or a new viewing level affects
  nobody until you attach it to something, so you can build it, look at it and
  throw it away. Editing one of the ones the hub shipped with changes every
  item already assigned to it, immediately.
- **Test with an ordinary account, not with yours.** Your account is almost
  certainly a Super User, which is allowed everything and therefore cannot
  show you what anyone else sees. Keep a plain member account, log into the
  site with it in another browser, and check there.
- **Know which of the two things called a group you are looking at.** They sit
  next to each other on the menu and have nothing to do with one another. See
  [below](#two-different-things-are-called-a-group).

Blocking an account, confirming an email address, approving an account and
publishing or unpublishing a hub group are the reversible operations in this
section. Prefer them.

## The Users menu

In the administrator interface, **Users** collects these screens. The menu is
built in
[`core/modules/mod_adminmenu/tmpl/default_enabled.php`](../../../core/modules/mod_adminmenu/tmpl/default_enabled.php),
and every entry is served by `com_members` except **Groups**, which is
`com_groups`.

| Menu entry | Component | Covered in |
|---|---|---|
| **Members** | `com_members` | [Members](01-members.md) |
| **Groups** | `com_groups` | [Groups](05-groups.md), [Super Groups](08-supergroups.md) |
| **Access Groups** | `com_members` | [Access Groups](06-accessgroups.md) |
| **Access Levels** | `com_members` | [Access Levels](07-accesslevels.md) |
| **User Notes** | `com_members` | [User notes](user-notes.md) |
| **User Note Categories** | `com_categories` | [User notes](user-notes.md) |
| **Mass Mail Users** | `com_members` | — |
| **Import** | `com_members` | [Importing members](03-memberimport.md), [Import archive](04-memberimportarchive.md) |

**Access Groups** and **Access Levels** appear only for a Super User; the
menu gates them on `core.admin` for `com_members`. **Groups** appears only if
you hold `core.manage` on `com_groups`.

> **Note:** There is no User Manager. `com_users` in this release has a site
> half (login, logout, password reset) and an API half, and no administrator
> screens at all. Edit accounts through **Members**.

## Two different things are called a group

The word *group* means two unrelated things in a hub, and the screens for
them sit next to each other on the same menu. Keeping them apart is the
single most useful thing to know about this section.

A **hub group** is a community. Members join it, or ask to join it, or are
invited to it. It has its own pages, forum, wiki, blog, calendar, file
space and member roles, and its own address at `/groups/<alias>`. Hub groups
live in `com_groups`, in the `#__xgroups` tables, and are administered under
**Users** → **Groups**. Users create and run them themselves; an
administrator's job is approval, configuration and cleanup. See
[Groups](05-groups.md).

An **access group** is a permission bucket. It carries no content and no
pages, users do not join it, and it is invisible on the site. It exists so
that permissions can be granted to a set of accounts at once: Public,
Registered, Author, Editor, Publisher, Manager, Administrator, Super Users.
Access groups live in `com_members`, in the `#__usergroups` table, and are
administered under **Users** → **Access Groups**. See
[Access Groups](06-accessgroups.md).

The two systems do not talk to each other. Joining a hub group grants no
access group, and being placed in an access group joins no hub group. An
account's access groups are set on the **Account** tab of its member record;
its hub group memberships are set on the **Groups** tab of the same record.

Access groups combine into **access levels** — named viewing levels such as
Public, Registered and Special that content items are tagged with. See
[Access Levels](07-accesslevels.md).

## Registration

By default visitors may register themselves. Users register through the
**Register** link on the site and log in at `/login`, which also carries the
links for a forgotten username or password. Registration can be narrowed or
switched off entirely.

> **Tip:** See [Configuring Registration](../05-configuring/02-registration.md)
> for the registration settings, and the generated
> [Members configuration reference](../../reference/configuration/components/members.md)
> for every parameter on the component's **Options** screen.

[Registration](02-registration.md) covers the screen itself: the table of
account states, what each column controls, and the confirmation mail.

Accounts can also arrive in bulk rather than one at a time. See
[Importing members](03-memberimport.md) for the file formats and the run,
and [Import archive](04-memberimportarchive.md) for what the archive screen
keeps afterwards.

## A worked example

Several pages in this section follow the same scenario, so that the pieces fit
together rather than each being demonstrated on its own.

A hub run by a research group agrees to host a two-week workshop for a partner
institution. Forty people from that institution need accounts before the
workshop opens, a place to work together while it runs, and access to material
that is not public. Nobody outside the workshop should see that material, and
when the workshop ends the accounts stay but the access does not.

That one job touches nearly everything here: accounts arrive through
[Member import](03-memberimport.md), the cohort meets in a
[hub group](05-groups.md), the private material is tagged with a viewing level
built in [Access Levels](07-accesslevels.md) out of an access group created in
[Access Groups](06-accessgroups.md), and registration is opened or left shut
in [Registration](02-registration.md). Each page picks the scenario up where
it left off.
