<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/managers/components/wishlist
-->
# Wish lists

A wish list collects feature requests and suggestions from the hub's
members. Members post wishes, vote them up or down, and comment on them;
list owners rank each wish by importance and effort, accept or reject it,
and write an implementation plan. The hub has one general list at
`/wishlist`, and a resource, a group, or a member profile can each have a
list of its own. This chapter covers the administrator interface; the
[Hub users](../../users/29-wishlist.md) book covers posting and voting.

The reason to run one is that members will ask for things whether or not you
give them somewhere to ask. A wish list turns "can you add a bigger memory
option to the solver tool" — which otherwise arrives as a support ticket, an
email, or nothing at all — into a public item other members can vote on, so
you can see which requests have twenty people behind them and which have one.
A hub that already knows what it is building next does not need one.

> **Note:** This is not [Support](34-support.md). A wish is something that
> does not exist yet and might never; a ticket is something that is broken
> and someone is responsible for. They are separate queues with separate
> permissions, and the confusion runs both ways — members file feature
> requests as tickets and bug reports as wishes. The **Support - Wishlist**
> plugin exists precisely so a wish can be moved to the ticket queue when it
> turns out to be the other kind.

Most of the day-to-day work happens on the site, not here: list owners rank,
accept and plan wishes from the list's own pages. These administrator screens
are for the things the site cannot do — creating a list in the first place,
moving a wish between lists, and clearing up.

Open it under **Components > Wishlists**. Three sub-menu links sit at the
top left: **Lists**, **Wishes**, and **Comments**. You need the
**Manage** permission on the component to open any of them.

## Lists

The Lists screen shows every wish list with its **Title**, **State**,
**Access**, **Category** and the reference ID beside it, and a **Wishes**
count. Click a column heading to sort by it; click again to reverse the
order. Two filters sit above the table: a **Search** box, matched against
the title and description, and a **Category** menu whose entries are
**- Category -**, *general*, *group* and *resource*. Press **Go** to apply
the search or **Clear** to drop it.

The toolbar offers:

- **Options** — the component's configuration; see [Options](#options).
  This button only appears if you have the Configure permission.
- **Publish** and **Unpublish** — change the state of the checked lists.
- **New** — create a list.
- **Edit** — open the checked list. Clicking a title does the same.
- **Delete** — remove the checked lists after a confirmation.
- **Help** — the built-in help screen.

The **State** and **Access** cells are links. Clicking one toggles that
list between Published and Unpublished, or between Public and Private,
without opening the edit form.

> **Warning:** Deleting a list is permanent and cascades. Every wish on the
> list goes with it, and with each wish its comments, rankings, votes and
> attachments. There is no trash to recover from.

The **Wishes** cell links to the Wishes screen filtered to that one list.

## Creating or editing a list

| Field | Notes |
|---|---|
| Category | What kind of thing the list belongs to: *general*, *group* or *resource*. Together with the reference ID this decides where the list appears on the site. |
| Reference ID | The ID of the item the list is attached to — a group ID for *group*, a resource ID for *resource*. The main site-wide list is category *general* with reference ID 1. |
| Title | Required. |
| Description | A short description, shown in the sidebar of the list on the site. Plain text, up to 255 characters. |
| State | Unpublished, Published or Trashed. |
| Privacy | Public or Private. A private list is only reachable by its owners; everyone else gets a "not authorized" notice. |

The panel beside the form shows the list's **ID**, when it was
**Created**, and its **Creator**. Press **Save & Close** to store the list
and return, or **Cancel** to discard.

> **Note:** The **Category** menu offers only *general*, *group* and
> *resource*, even though the component's `categories` option also ships
> with *user*. Member profile lists are created by the site when a member
> first uses one, not from this form.

In practice you will rarely create a list here. Nothing is seeded at install,
but the first visit to `/wishlist` creates the site-wide list itself, public,
with the title `general #1`; a group's or a resource's list is created the
same way the first time its tab is opened. So the usual first job on this
screen is not **New** — it is opening that auto-created list and giving it a
title and description that read like something a member would want to post
to.

## Wishes

The Wishes screen lists wishes across every list, or the wishes of one
list when you arrive from a list's Wishes count. Columns are **ID**,
**Title**, **List ID** (hidden when you are inside a single list),
**Submitter**, **Submitted**, **Status**, **Access** and **Comments**, all
sortable. Filters are a **Search** box, matched against the subject and
description, and a **Filter status** menu whose entries are
**- Status -**, *Granted*, *Open*, *Accepted*, *Pending*, *Rejected*,
*Withdrawn*, *Deleted*, *User accepted*, *Private*, *Public* and
*Assigned*. **- Status -** is the default and shows everything except
deleted wishes; only *Deleted* brings those back.

The toolbar offers **Granted** and **Pending**, which set the status of
the checked wishes; **New**, **Edit**, **Delete** and **Help**. As on the
Lists screen, the **Status** and **Access** cells are links that toggle a
single row. The **Comments** count opens the Comments screen for that
wish.

## Creating or editing a wish

The form has three parts: Details, Plan and Parameters.

| Field | Notes |
|---|---|
| Category | Required. The wish list the wish belongs to, chosen by title. |
| Title | Required. Up to 150 characters. |
| Description | The body of the wish, in the editor. |
| Tags | Comma-separated tags, which connect the wish to hub-wide search. |
| New revision | Only shown when a plan already exists. Checked, saving stores the plan text as a new revision instead of overwriting the current one. |
| Due | *never*, or a date. The site marks a wish overdue once the date passes. |
| Assigned to | One of the list's owners, or *Unassigned*. The menu is rebuilt for the list you choose. |
| Description (Plan) | The implementation plan text, in the editor. |
| Anonymous | Hide the submitter's name on the site. |
| Private | Only list owners can see the wish. |
| Accepted | Marks the wish as accepted for work. |
| Points | Bonus points awarded when the wish is granted. Only meaningful when banking is on. |
| Status | Pending, Granted, Deleted, Rejected or Withdrawn. |

The panel beside the form shows the wish's **ID**, when it was
**Created**, its **Creator**, and its computed **Ranking**. Press **Save**
to save and stay on the form, **Save & Close** to return to the list, or
**Cancel**.

### Dealing with one wish

A member asks for a bigger memory option on the solver tool. Twelve people
have voted it up and it has been sitting **Pending** for a month. What you do
with it from here:

1. **Components > Wishlists > Wishes**. Leave **Filter status** at
   **- Status -** and search for the wish, or arrive through the
   **Wishes** count on its list.
2. Open it. The panel beside the form shows the computed **Ranking** — the
   number the site sorts by, made up of the votes and the owners' importance
   and effort rankings. That is the evidence for whether this is worth
   doing.
3. If you are going to do it, tick **Accepted**, set **Assigned to** to one
   of the list's owners, and give **Due** a date. Write what you intend to
   do into the plan's **Description**.
4. If you are not going to do it, set **Status** to **Rejected** and say why
   in the plan. Rejecting is not deleting: the wish stays visible with its
   votes, which is the point — the next person to ask can see it was
   considered.
5. When the work ships, set **Status** to **Granted**.

> **Note:** Editing a plan that already exists overwrites it unless you tick
> **New revision**, which keeps the old text as a revision instead. On a wish
> several people have worked on, tick it.

Do not use **Delete** for a wish you have turned down. It is permanent, it
takes the votes and comments with it, and it makes the list look as though
nobody ever asked.

## Comments

The Comments screen lists the comments on one wish, replies indented
beneath the comment they answer. Columns are **ID**, **Comment** (the
first 50 characters), **Added by**, **Added**, **State** and
**Anonymous**. A search box filters the list, and the toolbar offers
**Publish**, **Unpublish**, **New**, **Edit**, **Delete** and **Help**.
Both the **State** and **Anonymous** cells toggle when clicked.

Editing a comment gives you the comment text in an editor, an
**Anonymous** checkbox, and a **Status** of Unpublished, Published or
Trashed. The panel beside it shows the wish ID the comment belongs to, its
type, its own ID, and when and by whom it was created.

> **Note:** The **Comments** sub-menu link opens the screen unfiltered, so
> it lists the comments on every wish on the hub. Arriving through a
> wish's **Comments** count narrows it to that wish and puts the list and
> wish titles in a header row above the columns.

## Where wish lists appear on the site

The **Category** and **Reference ID** of a list decide where it shows up:

- *general* with reference ID 1 is the site-wide list at `/wishlist`.
- *group* lists appear on a group's **Wishlist** tab, supplied by the
  **Groups - Wishlist** plugin, whose parameters set the default access
  level, whether the tab shows in the group menu, and how many items to
  display.
- *resource* lists appear on a resource's **Wishlist** tab, supplied by
  the **Resource - Wishlist** plugin, for resource types that enable it.
  There is an equivalent **Publication - Wishlist** plugin.
- *user* lists belong to a member profile.

The **Support - Wishlist** plugin lets wishes and wish comments be
reported as abusive and transferred to a support ticket, and the
**Wishlists** search plugin indexes wishes for site search. Three modules
ship with the component: **My Wishes**, which shows a member their open
and assigned wishes; **Wish Voters**, shown beside a busy general list;
and an administrator **Wishlist** module.

## Options

The **Options** button opens the component-wide settings: the categories
lists may use, the admin group whose members own the general list, how
many popular tags to show, whether banking (points and bonuses) is on,
whether to show the percentage of wishes granted, whether lists may have
an advisory committee and how heavily its votes count, and where
attachments are stored. Every option is listed with its values in the
[configuration reference](../../reference/configuration/components/wishlist.md).

## Permissions

The **Permissions** tab sets who may act on the component as a whole:
Configure, Access Administration Interface, Create, Delete, Edit, Edit
State, and Edit Own. Manage opens the administrator screens; Create,
Edit, Delete and Edit State gate the corresponding toolbar buttons and
row toggles on all three screens; Configure shows the Options button.
On the site, a list's own owners are granted the equivalent rights on
that list regardless of these settings, and a member may always edit a
wish they submitted.
