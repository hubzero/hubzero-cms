<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/users/groups
source-id: 3360
modified: 2016-07-12
-->
# Groups

Hub groups are communities that users build inside the hub. A group has its
own address at `/groups/<alias>`, its own pages, and its own forum, wiki,
blog, calendar, file space and member list. Users create and run groups
themselves; from the administrator interface you approve them, configure how
they behave, correct their membership and delete the ones that should not
exist.

> **Note:** A hub group is not an access group. Access groups are the
> permission buckets described in [Access Groups](06-accessgroups.md), and
> the two systems are unrelated. See the
> [section introduction](README.md#two-different-things-are-called-a-group).

Reach the screen at **Users** → **Groups**, or directly at
`/administrator/index.php?option=com_groups`. Within the component the
sub-navigation offers:

| Entry | What it manages |
|---|---|
| **Groups** | The group list, the default screen |
| **Import** | CSV import of groups and members, with import hooks. Super User only |
| **Custom Fields** | Extra fields shown on the group form. Super User only |

## The group list

The list shows **Group ID**, **Name**, **Alias**, **Type**, **Published**,
**Approved**, a member count and a page count. The alias is the segment used
in the group's URL. Selecting the name or the alias opens the group for
editing; the member count opens the membership screen for that group; the
page count opens its page list.

Filter with the search box and the **Type**, **Discoverability**, **Join
Policy** and **State** menus.

The toolbar carries **Publish**, **Unpublish**, **Archive**, **New**,
**Edit**, **Delete** and **Options**. **Update Groups Code** appears as well
when GitLab repository management is switched on. Each button is gated on the
matching permission from
[`config/access.xml`](../../../core/components/com_groups/config/access.xml),
so a manager without `core.delete` does not see **Delete**.

**Published** and **Approved** are also togglable in place: select the icon
in either column to flip that group's state.

### Group types

The **Type** column and the type menu on the group form use these values,
set in
[`admin/views/manage/tmpl/edit.php`](../../../core/components/com_groups/admin/views/manage/tmpl/edit.php):

| Type | Meaning |
|---|---|
| **Hub** | An ordinary group, the kind users create for themselves |
| **Super** | A group with its own web space, template and code. See [Super Groups](08-supergroups.md) |
| **System** | Created and owned by the software. Offered only to a Super User |
| **Project** | Backs a project in `com_projects` |
| **Course** | Backs a course in `com_courses` |

Only Hub and Super groups render on the site. `com_groups` returns a 404 for
any other type.

## Creating a group

1. Go to **Users** → **Groups**.
2. Select **New**.
3. Fill in the form. **Type** and **Alias** are required, and the alias may
   contain only lowercase letters, digits and underscores. The alias cannot
   be changed once the group is saved.
4. Select **Save & Close**.

The form has two tabs. **Details** holds everything about the group;
**Files** is a file browser into the group's own directory and is empty
until the group has been saved once.

The **Details** tab is divided into these areas:

| Area | Contents |
|---|---|
| **Details** | Type, Published, Approve, Alias, Title, Group Logo, and any custom fields |
| **Page Settings** | Trusted content, page comments, page author display, and a template override |
| **Membership** | Membership Control, Join Policy, and the credentials text shown to applicants |
| **Access** | Discoverability, Plugin Access, and whether system users appear in the member list |
| **Email Settings** | Discussion email auto-subscription. Shown only when the component's email option **Forum Outgoing Email** is on |

**Join Policy** is the group's own setting, not a site setting, and its four
values are:

| Policy | Effect |
|---|---|
| **Public** | Any site member can join |
| **Restricted** | The user must request membership, and a manager approves it |
| **Invite Only** | The user must be invited by an existing group member |
| **Closed** | Membership cannot be changed from the site |

**Discoverability** is either **Visible** — the group is listed and
searchable — or **Hidden**, in which case only members find it.

> **Note:** New groups created here are **not** approved automatically. The
> **Approve** menu on the form defaults to *Unapproved*; set it yourself, or
> use the Approved column in the list afterwards. Automatic approval, the
> `auto_approve` option, governs groups created by users on the site, not
> groups created here.

## Editing a group

1. Go to **Users** → **Groups**.
2. Select the group's name or alias.
3. Change what you need and select **Save & Close**.

The alias field is read-only for an existing group.

## Publishing, unpublishing and archiving

1. Go to **Users** → **Groups**.
2. Tick the box beside one or more groups.
3. Select **Publish**, **Unpublish** or **Archive**.

Or select the icon in the group's **Published** column to toggle it directly.
An archived group stays readable but its toolbar drops the management
actions.

## Approving a group

When automatic approval is off, a group a user creates on the site is saved
unapproved and an email goes to the site administrator plus everyone listed
in the **Group reviewers** option. To approve it, select the icon in the
group's **Approved** column. Selecting it again unapproves the group.

## Deleting a group

1. Go to **Users** → **Groups**.
2. Tick the box beside one or more groups.
3. Select **Delete** and confirm.

> **Warning:** Deleting a group fires `groups.onGroupDelete` on every group
> plugin, which removes the group's blog entries, forum threads, wiki pages,
> announcements, collections, wish list and the rest along with it. There is
> no undo. The group's upload directory on disk is left behind, so for a
> super group you must clean up the files yourself.

## Membership

Select a group's member count in the list to open its membership screen. The
list can be filtered by status — Manager, Applicant, Invitee — and, when
membership expiration is available, by term.

What the toolbar offers depends on the filter:

- **Managers and members:** **Promote**, **Demote**, **Membership** (set or
  clear an end date) and **Delete**.
- **Applicants:** **Approve** and **Deny**.
- **Invitees:** **Uninvite**.

**New** adds a member, and **Assign role** attaches a role to the selected
members.

Membership end dates are enforced by a cron job, not by the page. The screen
prints a banner naming the last run, and an error banner if the job has not
run in the last 24 hours; until it runs, an expired membership keeps its
access.

## Roles

A role is a named set of extra permissions inside one group. A role carries
a name and any of three permissions:

- **Invite New Members**
- **Edit Group Settings**
- **Create/Edit Group Pages & Categories**

Members holding a role can do those things without being made a manager.

## Configuring groups

1. Go to **Users** → **Groups**.
2. Select **Options**.
3. Change what you need and select **Save & Close**.

The dialog has these sections:

| Section | What it sets |
|---|---|
| **General** | Whether groups users create are approved automatically, and the reviewer addresses notified when they are not |
| **Intro Page** | Which lists the group landing page shows: my groups, interesting groups, popular groups, featured groups |
| **Membership & Access** | The default join policy and discoverability for new groups, system user display, the invitation message, and membership expiration |
| **Email** | Incoming comment processing, forum outgoing email, auto-subscription, and the forum digest |
| **Upload** | The group file path and whether uploads are virus scanned |
| **Pages** | Page approvers, page depth, comments, author display, and whether groups may use modules |
| **Super Groups** | Super group components, the filesystem owner for group assets, and GitLab repository management |
| **Permissions** | The `com_groups` access rules for each access group |

Every parameter, its type and its default are listed in the generated
[Groups configuration reference](../../reference/configuration/components/groups.md).

> **Note:** **Allow forum digest?** only permits the digest. What sends it is
> the `emailGroupForumDigest` job in the **Cron - Forum** plugin, which you
> schedule under **Components** → **Cron**. Turning the option on without
> that job sends nothing. Schedule it no more than once a day; it has no
> queue, so a second run resends the same digest.
