<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/users/groups/groupforum
source-id: 3308
modified: 2015-12-04
-->
# Group forum

A group's **Forum** tab is where its members talk. It is organised in three
levels: **sections** hold **categories**, and categories hold
**discussions**. A discussion is a first post plus its replies.

Who can *read* the forum is set by the group's access permission for the
**Forum** tab, which is **Group Members Only** unless a manager changes it.
Only group members can post, whatever that setting says.

The forum is empty when a group is created, and says so: *This forum is
currently empty and requires some set-up by the managers before it can be
used.* A manager has to make the first section before anyone can use it.

## Reading and posting

The tab opens on the list of sections, each with its categories and a count
of the discussions and posts in each. **Statistics** and **Last Post**
summarise the forum in the sidebar. The search box searches posts, across the
whole forum or within one category.

To start a discussion, open a category and select **New Discussion**.
**Category**, **Title** and **Comments** — the text of the first post — are
required. **Your tags** and an **Attachment** are optional, and so is
**Post Anonymously** where the manager has allowed it.

A manager's form has two more boxes, **Make discussion sticky** and **Closed
thread (no new posts)**.

**Who can read this thread?** — **Anyone**, **Logged-in users** or group
members — appears only when the group has opened the **Forum** tab beyond its
own members. The group's access setting still overrides whatever you choose,
and the form says so.

To reply, use the form at the foot of the discussion. When the forum is set
to nested threading, each post also carries its own **Reply** link, so you
can answer one post rather than the discussion as a whole. Sort a category's
discussions by creation date, activity, number of posts or title.

**Edit** and **Delete** appear on your own posts. A manager can delete
anyone's reply, and edit or delete the discussion itself.

A closed discussion, or a category marked **No new posts**, takes no
replies.

## What a manager can do

A group manager can:

- Create, rename, reorder and delete **sections**.
- Create, edit and delete **categories** within a section.
- Lock a category, or close it to new posts.
- Edit and delete any discussion or post.
- Change the forum's settings.

> **Warning:** Deleting a section or a category takes its discussions with
> it. You get one browser confirmation, *Are you sure you wish to delete this
> item?*, and no undo.

**Settings**, at the top of the tab, offers:

| Setting | What it does |
|---|---|
| **Comment threading** | **Flat, one level threads (traditional)** or **Nested comments (tree)** |
| **Nested depth limit** | How many levels a nested thread may go. Applies to nested threads only. Default 3 |
| **Default thread sorting** | **Most recent activity**, **Creation date**, **Number of posts** or **Title** |
| **Allow anonymous posts?** | Whether members may hide their name on a post |

An empty forum offers a manager a shortcut: **generate** example content, to
get a section and some categories in place quickly.

## Email settings

Members can have the forum email them. The **Email Settings** box sits in the
sidebar of the **Forum** tab.

> **Note:** The box only appears to group members, and only when the hub's
> administrators have turned **Forum Outgoing Email** on for groups. Without
> that there is nothing to subscribe to, and no box.

What the box offers depends on which of the hub's two extra options is on.

### The digest

When the hub allows a forum digest, the box shows your current preference and
a **Change your settings** link.

1. Open the group and select the **Forum** tab.
2. Find **Email Settings** in the sidebar and select **Change your
   settings**.
3. Tick **Email me about new posts in this group**.
4. Choose how they arrive:
   - **individually as new posts are made**, or
   - **as part of a** **Daily**, **Weekly** or **Monthly** **digest email** —
     pick the frequency from the menu.
5. Select **Save**.

The line above the link then reads back what you chose: *not receive email
notifications*, *receive immediate notification*, or *receive a daily*,
*weekly* or *monthly digest*.

> **Note:** A digest is sent by a scheduled job on the hub. If the hub's
> administrators have allowed digests but not scheduled that job, nothing
> arrives however you set this. See
> [Groups](../../managers/06-users/05-groups.md#configuring-groups) in the
> managers book.

### Per-category subscriptions

When the hub allows per-category subscriptions instead, the box lists the
forum's categories under **Posts to:**. Tick the ones you want mail from and
select **Save**.

### The simple form

With neither option on, the box is a single checkbox, **Email forum post
notifications**, and a **Save** button.

## Being subscribed automatically

A group manager can set the group so that new members start subscribed to its
forum email: the **Auto subscribe new group users to discussion email**
checkbox under **Group Email Settings** in
[the group's settings](01-createdeleteagroup.md#group-email-settings).
Everyone can change their own setting afterwards.

Every notification the forum sends also carries an **Unsubscribe** link.
