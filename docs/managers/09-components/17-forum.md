<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/managers/components/forum
-->
# Forum

The forum is the hub's message board. Discussions are organised into
**sections**, which hold **categories**, which hold **threads**; a thread is
an opening post and the replies to it. The site-wide forum lives at `/forum`.
This chapter covers the administrator's side; the
[Hub users](../../users/09-forum.md) book covers reading and posting.

## Whether your hub needs it

The forum is for the conversation that has no single right answer: a
proposal being argued over, a shared problem nobody has solved yet, the
running chatter of a working group. It is the only one of the hub's
discussion components with a real hierarchy, so it is the one that scales
past a few dozen threads without becoming a heap.

The typical case: two labs on the hub are agreeing on a file format. That is
not a question with an answer — it is three weeks of back-and-forth that
needs to stay in one place and be readable afterwards by someone who joins
in week two.

Against its siblings:

- Better than [Answers](02-answers.md) at anything ongoing, because threads
  have depth, categories, attachments, and per-section permissions. Worse at
  questions: no reply is ever marked as the answer, so a reader has to read
  the whole thread to find out how it came out, and a question asked in a
  busy category slides off the front page unanswered.
- Better than the [Wiki](39-wiki.md) at the argument; worse at the
  conclusion, because nothing here produces a single current version of
  anything. Pairs well with the wiki inside a group: argue in the forum,
  record the outcome on a wiki page.
- Where a hub's real unit of organisation is the group, the site-wide forum
  at `/forum` is usually the one that stays empty while the per-group forums
  fill up. Look at your groups before you spend an afternoon building
  sections nobody will read.

**What it is not:** it is not a mailing list, and it does not chase anyone.
The site forum has no notifications of its own; per-member email
notifications exist only in group forums, through the Groups - Forum plugin.
A site-wide forum on a hub whose members do not visit daily will be posted
to and never replied to.

Before you build anything here, decide whether you want a site forum at all.
If your hub already runs [Answers](02-answers.md) for questions and groups
for team talk, a third place to post is a third place to go unread.

Open it in the administrator interface under **Components > Forum**. Three
sub-menu links sit at the top left — **Sections**, **Categories**, and
**Threads** — one for each level of the hierarchy.

## Scope

Every section, category, and post carries a **Scope** and a **Scope ID**.
Together they say which forum the record belongs to: `site` with an ID of `0`
is the site-wide forum, `group` with a group's ID number is that group's
forum, `course` with a course ID is a course's forum. All of them share the
same tables and the same three administrator screens, so the lists you see
here contain every forum on the hub, not only the site one. The **Scope**
filter on each screen narrows the list to one forum; the site forum is listed
as **(none)**.

> **Note:** Scope and Scope ID are free-text fields on the edit forms.
> Changing them moves a record into a different forum. A category is forced to
> take its section's scope when you save it, and a post is forced to take its
> category's scope, so in practice you only set the scope on a section.

## Sections

The Sections screen lists **ID**, **Title**, **State**, **Access**,
**Scope**, and the number of **Categories** in the section. Click a column
heading to sort by it, click again to reverse the order. Click the category
count to jump to that section's categories.

Filter the list with the search box (matched against titles; press **Go**, or
**Clear** to reset), the **Scope**, **State**, and **Access** menus.

The toolbar offers **Options**, **Publish**, **Unpublish**, **New**,
**Delete**, and **Help**.

## Categories

The Categories screen lists **ID**, **Title**, **State**, **Access**,
**Scope**, **Threads**, and **Posts**. The thread and post counts link to the
Threads screen filtered to that category.

Filter by **Scope**; once a scope is chosen a **Section** menu appears, and
**State** and **Access** menus are always present. This screen has no search
box. The toolbar is the same as the Sections screen.

## Threads

The Threads screen lists thread-starting posts: **ID**, **Title**, **State**,
**Sticky**, **Access**, **Scope**, **Creator**, and **Created**. Click a
title to open the thread's posts. Click the **Sticky** icon to pin or unpin a
thread — sticky threads sort to the top of a category regardless of age.

Filter by **Scope**, then **Section**, then **Category**, plus **State** and
**Access**.

> **Note:** The **Creator** column shows the author's numeric user ID rather
> than a name, and **Created** shows the raw database timestamp.

Opening a thread shows the posts it contains — **ID**, **Title**, **State**,
**Scope**, **Creator**, **Created** — with a **Category** filter and the same
State and Access filters. A post saved without a title is given one built
from the first 70 characters of its text.

## Setting up the site forum

Building the two labs of the example above a place to work, end to end. This
is the only setup task the component asks of a manager; everything after it
is members posting.

1. Go to **Components > Forum > Sections** and press **New**. Give it a
   **Title** — one section is enough to start with; sections are the top
   level and a hub with three threads does not need three of them. Leave
   **Scope** at `site` and **Scope ID** at `0`. Set **State** to Published
   and choose an **Access** level. **Save & Close**.
2. Go to **Categories** and press **New**. Pick your section from the
   **Section** menu, give the category a **Title** and a **Description** —
   the description is the only place you get to say what belongs in here, and
   it is shown under the title on the site, so use it. **Save & Close**.
3. Go back to **Sections** and click the section's category count to check
   the category landed in the right place.
4. Open **Options** and set **Threading** to **Nested threads** unless you
   want flat threads. See the warning below: the shipped value is neither
   choice, and the effect is flat.
5. Visit `/forum` as a member would and confirm the section and category are
   visible at the access level you set.

Everything in steps 1 to 4 is reversible: unpublish a section and its
categories vanish from the site with their content intact. What is not
reversible is **Delete**, which is why the warning below matters more than
anything else on this page.

Add categories as topics actually appear, not in advance. Empty categories
make a forum look abandoned, and a member faced with eight of them posts in
the wrong one.

## Editing a section

| Field | Notes |
|---|---|
| Scope | Which kind of forum the section belongs to: `site`, `group`, or `course`. New sections default to `site`. |
| Scope ID | The ID of the group or course. `0` for the site forum. |
| Title | Required. |
| Alias | The URL segment. Left blank, it is generated from the title in lowercase with hyphens for spaces. |
| State | Unpublished, Published, or Trashed. Only published sections appear on the site. |
| Access | The viewing level a visitor needs. |

The panel beside the form shows the **Creator** and **Created** date. If you
have the component's Admin permission, a **Rules** fieldset at the foot of the
form sets permissions for this one section.

Press **Save** to stay on the form, **Save & Close** to return to the list, or
**Cancel**.

## Editing a category

| Field | Notes |
|---|---|
| Scope, Scope ID | As above, but overwritten with the parent section's values on save. |
| Section | Required. Sections are grouped by scope in the menu, with the site forum shown as `[ site ]`. Saving with no valid section fails with "Section was not specified or does not exist." |
| Title | Required. |
| Alias | As above. |
| Description | A line or two shown under the category title on the site. |
| Lock Category (no new posts) | Locked categories accept no new threads. Existing content stays readable. |
| State | Unpublished, Published, or Trashed. |
| Access | The viewing level a visitor needs. |

The side panel shows the creator and creation date, and the modifier and
modification date once the category has been edited. This screen's toolbar
offers **Save & Close** and **Cancel** only; unlike the section and post
forms it has no **Save** button that keeps you on the form.

## Editing a post

The same form edits a thread-starting post and a reply; which fields appear
depends on which it is.

| Field | Notes |
|---|---|
| Scope, Scope ID | Overwritten with the parent category's values on save. |
| Object ID | An optional ID linking the post to another item on the hub. |
| Category | Thread starters only. Required; the menu nests categories under their sections. |
| Parent | Replies only. Which post in the thread this one answers. |
| Title | Optional. Left blank, it is built from the first 70 characters of the post. |
| Comments | Required. The body of the post. |
| Your tags | Thread starters only. |
| File, Description | One attachment per post. Uploading a new file replaces the current one. Existing attachments are listed above with a delete link. |
| Post Anonymously | Hides the author's name on the site. |
| Make discussion sticky | Thread starters only. |
| State | Unpublished, Published, or Trashed. A fourth state, reported, is set when a member reports the post as abusive; the post then shows a warning in place of its text on the site. |
| Access | The viewing level a visitor needs. |

The toolbar has **Save**, **Save & Close**, **Cancel**, and **Help**.

> **Warning:** **Delete** on any of these screens removes the record
> permanently and takes its children with it — deleting a section deletes its
> categories, their threads, every reply, and every attached file. There is no
> trash to recover from. Setting the state to Trashed instead hides the record
> while keeping it.

## Options

The **Options** button opens the component-wide settings: where uploaded
files are stored, which forums feed the site's discussion listings, whether
members may post anonymously, whether threads are flat or nested, and how
deep nesting may go. Every option is listed with its values in the
[configuration reference](../../reference/configuration/components/forum.md).

> **Note:** The **Threading** option ships with a stored default of `both`,
> which is not one of its two choices. Until you set it to **Nested threads**
> the forum renders flat, one-level threads.

Of the rest, the one to think about is whether members may post anonymously,
which ships **enabled**. Anonymous posting hides the name on the site but not
in the database — you can still see the author on the post's edit form — and
on a small hub where everyone knows everyone it mostly invites the kind of
post you will later have to unpublish. Turning it off is worth considering
unless you have a reason to keep it, such as a forum for reporting problems
with a colleague's tool. Changing the setting does not retrospectively reveal
anyone: posts already made anonymously stay anonymous.

## Permissions

The **Permissions** tab of Options controls who may access, administer, and
manage the component, and who may create, delete, edit, change the state of,
and edit their own entries. Alongside the general actions, the component
defines separate create, delete, edit, edit-state, and edit-own actions for
each level — Section, Category, Thread, and Comment — so a group can be
allowed to start threads without being allowed to add categories. Permissions
can also be set on an individual section, category, or thread through the
**Rules** fieldset on its edit form, and the site-facing forum honours those:
a member with the section create permission gets a **New Section** form on the
forum's home page.

## Group and course forums

A group's forum is provided by the **Groups - Forum** plugin, which reuses
these models and tables with a scope of `group`. Its plugin settings set the
default access level for the tab and the display limit; a group's own managers
set threading, default sort order, and anonymous posting from the forum's
**Settings** page inside the group. Group forums also offer per-member email
notifications, described in
[Group forum](../../users/11-groups/05-groupforum.md).

## API

The forum exposes endpoints for listing sections, categories, and threads,
reading a single thread, creating threads and posts, and managing a member's
category subscriptions. See the
[API reference](../../reference/api/forum.md).
