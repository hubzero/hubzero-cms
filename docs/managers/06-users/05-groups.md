<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
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
> Putting somebody in a hub group grants them nothing on the rest of the hub.
> It gives them the group's own pages, forum, wiki and files, and stops there.

## What you actually do on this screen

Groups largely run themselves. Users create them, users manage their own
membership, and a hub can go months with nobody opening this screen. You come
here for four things, in rough order of how often:

- **Approving a group** somebody created, when automatic approval is off.
- **Rescuing a group** whose only manager has left the hub, by promoting
  somebody else on its membership screen.
- **Deleting a group** that should not exist — a duplicate, an abandoned test,
  something a spammer created.
- **Creating a group that users cannot create for themselves**: a super group,
  a group on somebody's behalf, or a group that has to exist before its members
  do.

Everything else on the screen — the type, the join policy, the discoverability
— is a group's own setting that its managers control from the site. Changing
one from here is an intervention in somebody else's group, and it takes effect
without telling them.

## What a change here does, and who notices

- **Membership changes take effect immediately.** Someone removed from a group
  loses the group's pages, forum and files on their next page load. They are
  not told, and they will read it as the hub being broken.
- **Unpublishing is the reversible way to take a group out of circulation.** The
  group and everything in it survive; publish it again and it comes back. Use
  this while you work out whether a group should really go.
- **Deleting is not reversible and takes the content with it.** See [Deleting a
  group](#deleting-a-group) before you use it, and prefer unpublishing.
- **Approving and unapproving are both reversible** and change only whether the
  group is listed and usable.

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

Creating a group is safe. Until it has members and content it is an empty
shell, and unpublishing it puts it out of sight without losing anything.

### Worked example: a group for the workshop cohort

Continuing the scenario from the [section
introduction](README.md#a-worked-example), the forty visiting participants need
somewhere to work together for the two weeks of the workshop.

1. Go to **Users** → **Groups** and select **New**.
2. Set **Type** to **Hub**. A super group is only needed if the workshop wants
   its own web space and code; see [Super Groups](08-supergroups.md).
3. Enter an **Alias** of `workshop2026` and a matching **Title**. Get the alias
   right the first time — it becomes the group's address and cannot be changed
   afterwards.
4. Under **Membership**, set **Join Policy** to **Invite Only**, so that nobody
   outside the cohort can add themselves.
5. Under **Access**, set **Discoverability** to **Hidden**, so the group does
   not appear in the site's group listings.
6. Set **Approve** to *Approved* and **Published** to *Published*.
7. Select **Save & Close**.

The group now exists and is empty. The cohort's accounts are put into it by the
`groups` column of the [member import](03-memberimport.md), which is the one
place that column is genuinely the right tool — the accounts do not exist yet,
so there are no memberships for it to overwrite.

Note what this group does *not* do. It gives the cohort a shared space; it does
not give them access to restricted material elsewhere on the hub. That is a
[viewing level](07-accesslevels.md) built on an
[access group](06-accessgroups.md), and it is set up separately.

When the workshop ends, unpublish the group. Its discussions and files stay
readable to you and recoverable; deleting it destroys them.

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

Automatic approval is on by default, in the manifest and in the data a hub is
installed with, so out of the box a user's new group works the moment they
create it and nothing reaches you. That is the right default for a hub whose
groups are its own community, and the wrong one for a hub that gets
unsolicited signups: turn it off under **Options** → **General** and a group
waits for you instead. Turning it off does not affect groups already approved.

When automatic approval is off, a group a user creates on the site is saved
unapproved and an email goes to the site administrator plus everyone listed
in the **Group reviewers** option. To approve it, select the icon in the
group's **Approved** column. Selecting it again unapproves the group.

> **Warning:** Turning automatic approval off with nothing in **Group
> reviewers** means the notification goes only to the site administrator
> address. If nobody reads that mailbox, every group a user creates sits
> unapproved and unnoticed, and the users who created them have no way to
> chase it.

## Deleting a group

1. Go to **Users** → **Groups**.
2. Tick the box beside one or more groups.
3. Select **Delete** and confirm.

> **Warning:** Deleting a group fires `groups.onGroupDelete` on every group
> plugin, which removes the group's blog entries, forum threads, wiki pages,
> announcements, collections, wish list and the rest along with it. There is
> no undo. The group's upload directory on disk is left behind, so for a
> super group you must clean up the files yourself.

This is the most destructive button in this section, and there is nothing to
distinguish it from any other delete button in the interface. What goes is not
the group but years of somebody's discussion. If a group is dormant, unpublish
it; if it is a duplicate, check which of the pair holds the content first; if
somebody has asked you to delete their group, ask them whether they want the
forum archived somewhere first, because you cannot get it back for them
afterwards. Reserve **Delete** for groups that were never used — spam, tests,
mistakes made minutes ago.

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

Two of these actions are worth pausing over.

**Delete** here removes somebody from the group, not from the hub. The account
itself is untouched — this is the screen to use when a member has left a
project but stays on the hub.

The two ways of removing a manager do not behave alike. **Demote** refuses to
act on the last remaining manager: it answers *Cannot remove all managers. A
group must have at least one manager.* and does nothing. **Delete** carries no
such check, so ticking the last manager and pressing **Delete** succeeds and
leaves the group with nobody able to run it. The site gives a group's members
no way out of that; promoting somebody from this screen is then the only route
back. Check the **Managers** filter before you delete anybody from it.

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
