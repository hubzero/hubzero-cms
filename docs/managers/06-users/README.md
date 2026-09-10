<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/users
source-id: 3354
modified: 2014-10-10
-->
# Users

Everything about the people on your hub: their accounts, the hub groups they
form, the access control that decides what each of them may see and do, and
the notes administrators keep about them.

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
