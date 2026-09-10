<!--
status: rewritten
reviewed-against: 2.4-main @ ddeb90135f
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/managers/components/forum
-->
# Forum

The forum is the hub's message board. Discussions are organised into
**sections**, which hold **categories**, which hold **threads**; a thread is
an opening post and the replies to it. The site-wide forum lives at `/forum`.
This chapter covers the administrator's side; the
[Hub users](../../users/09-forum.md) book covers reading and posting.

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
