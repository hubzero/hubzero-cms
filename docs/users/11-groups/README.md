<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/users/groups
source-id: 3303
modified: 2015-09-22
-->
# Groups

A group is a community you build inside the hub: a place to share content and
conversation, privately or with the world. Every group has an address of its
own at `/groups/<group ID>`, a member list, and a set of tabs — a forum, a
wiki, a blog, a calendar, files, and more — that the group's managers switch
on or off.

People make groups when a body of work outlasts a mailing list. A soil carbon
project spread over four institutions puts its sampling protocols on a group
page, argues about the analysis pipeline in a forum thread that is still
findable next year, and keeps the papers it produced on a **Citations** tab
it can hand to the funder. None of that needs an administrator: any member
can start a group, and the tabs are switches the group's own managers turn
on. This chapter follows that group, `soilcarbon`, from creation onward.

## Three things called a group

Hubs have three arrangements that sound alike and do quite different jobs.

- A **group** is what this chapter is about: a space members create and run
  themselves, with its own pages, forum, files and membership. You join one,
  or are invited to it.
- A [**project**](../16-projects.md) is a workspace for a small team
  preparing files and data before any of it is released. Its owner picks the
  team and decides whether anyone else ever sees it. It is about work in
  progress rather than community: there is no forum and no wiki, and nobody
  asks to join.
- An [**access group**](../../managers/06-users/06-accessgroups.md) is one of
  the hub's permission buckets, set by administrators, which decides what you
  are allowed to do on the hub at all. You cannot see it, join it, or make
  one.

A group's roles and permissions stop at the group's edge, and no combination
of them makes you an administrator. Your access group can decide whether you
are allowed to create a group at all, but being in one puts you in no group,
and joining a group changes nothing about your access group.

Groups start at `/groups`. What you see there depends on whether you are
signed in:

| Section | What it lists |
|---|---|
| **Group Invites** | Groups that have invited you. Only when you have some |
| **Group Requests** | Membership requests of yours still awaiting approval |
| **My Groups** | Groups you belong to |
| **Groups Matching My Interests** | Groups whose tags match the interests on your profile |
| **Popular Groups** | The three groups with the most members |
| **Featured Groups** | Groups the hub's administrators picked out |

**Browse available groups** opens the full list at `/groups/browse`. Search it
by title, sort by **Group Title** or **Group Alias**, and filter by **State**
(Active or Archived) and **Policy** (Open, Restricted, Invite Only, Closed).
Hidden groups never appear, in the list or in search results.

## Joining a group

Joining is what gets you the tabs a group has kept to itself. Most groups
leave **Overview** readable by anyone and shut everything interesting —
files, forum, wiki — to members, so a group can look almost empty from
outside and be busy within.

Open a group and use the button at the top of its sidebar. What the button
offers depends on the group's join policy:

| Policy | What you can do |
|---|---|
| **Open/Anyone** | **Join Group** adds you immediately |
| **Restricted** | **Request Group Membership** opens a form. Explain who you are and why you should be admitted, then select **Send Request**. A manager approves or denies it, and the button then reads **Request Pending Approval** |
| **Invite Only** | Nothing. The sidebar says **Group is Invite Only** |
| **Closed** | Nothing. The sidebar says **Group Closed** |

A restricted group may show its own **Credentials** text on the request form,
telling you what the managers want to see — a project number, a principal
investigator, and so on.

If you have been invited, the sidebar offers **Accept Invitation** and
**Decline Invitation** instead.

Once you are a member the button becomes a **Group Member** menu, whose
**Cancel Group Membership** entry leaves the group. A manager who is the only
manager left cannot leave; promote someone else first.

## The group page

Every group page has the same frame:

- The group's **logo** at the top of the sidebar, linking back to the group's
  home page.
- The membership button or the **Group Member** / **Group Manager** menu.
- The **tab menu** — one entry per plugin the group has switched on, starting
  with **Overview**.
- The group's join policy, discoverability and creation date beneath the
  menu.

**Overview** is the group's home page. Out of the box it shows **About the
Group**, built from whatever fields the hub's administrators defined, and a
preview of **Group Members**. A manager can replace it with a page of their
own; see [Customization](03-groupcustom.md).

## The tabs

Each tab comes from a plugin in `core/plugins/groups`. These ship with
Hubzero 2.4:

| Tab | What it provides |
|---|---|
| **Activity** | A feed of what has happened in the group |
| **Announcements** | Notices from the managers, which can be made sticky and given a publish window |
| **Blog** | The group's blog, with posts, comments and an archive |
| **Calendar** | The group's events, with download and subscription links |
| **Citations** | Publications produced by the group or its members. See [Group citations](04-groupcitations.md) |
| **Collections** | Boards of collected posts — links, files, images and text |
| **Courses** | Courses the group runs |
| **Files** | The group's shared file space |
| **Forum** | Discussions, in sections and categories. See [Group forum](05-groupforum.md) |
| **Members** | The member directory, and where managers run membership. See [Member functions](02-groupmembers.md) |
| **Messages** | Messages sent to the group or to a subset of its members |
| **Projects** | Projects owned by the group |
| **Resources** | Resources contributed under the group's name |
| **Usage** | Counts of the group's pages, members, resources, discussions, wiki pages, blog posts and events |
| **Wiki** | The group's wiki |
| **Wish List** | Requests and suggestions members vote on |

Two more plugins ship without a tab of their own: **Member Options** stores
each member's per-group email preferences, and **Search** keeps the group's
content in the hub's search index.

A manager sets each tab to **Any HUB Visitor**, **Registered HUB Users**,
**Group Members Only** or **Disabled/Off**. A tab set to Disabled/Off
disappears from the menu. **Overview** cannot be turned off.

## In this chapter

- [Creating and deleting a group](01-createdeleteagroup.md)
- [Member functions](02-groupmembers.md) — inviting, roles, promoting and
  removing members
- [Customization](03-groupcustom.md) — group settings, group pages, page
  categories, and the group calendar
- [Group citations](04-groupcitations.md)
- [Group forum](05-groupforum.md)

Administrators run groups from a different screen; see
[Groups](../../managers/06-users/05-groups.md) in the managers book.
