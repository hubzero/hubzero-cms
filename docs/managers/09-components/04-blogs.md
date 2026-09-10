<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/managers/components/blogs
source-id: 3373
modified: 2016-07-12
-->
# Blogs

A hub has three kinds of blog, and all three store their posts in the same
place. The **site blog** lives at `/blog` and is written by hub staff. A
**member blog** is the Blog tab on a member's profile, at
`/members/<id>/blog`. A **group blog** is the Blog tab inside a group, at
`/groups/<cn>/blog`. Every post is a row with a *scope* (`site`, `member`,
or `group`) and a *scope ID* (0 for the site, the member's or group's ID
otherwise), so the Blog Manager in the administrator interface reaches all
of them. The [Hub users](../../users/05-blog.md) book covers writing posts.

## Whether your hub needs it

A blog post is dated, signed, and finished. It says what happened, on a day,
and then it stops being current — a release note, a downtime notice, a write-up
of a workshop. That is the whole of what this component is good at, and it is
genuinely useful for it: a hub with no other way of saying "we upgraded the
solver on Tuesday" ends up saying it in an email nobody keeps.

The typical case: you are taking the hub down for four hours on Saturday to
move it to new hardware. Write it as a site blog post, publish it, and every
member and every group has one address to point at.

Against its siblings, the line is time. A blog post is right when the date
is part of the meaning. When it is not — when a reader a year from now still
needs the information to be true — the [Knowledge base](19-kb.md) or the
[Wiki](39-wiki.md) is the right place, because a blog post nobody has
revisited is indistinguishable from one that is still accurate. Blogs do not
compete with [Answers](02-answers.md) or the [Forum](17-forum.md) at all: a
post has one author and comments underneath, not a conversation.

**What it is not:** it is not the article manager. Articles in
[Content](../08-content/README.md) build the hub's static pages — About, Policies,
Contact. Blog posts are a dated stream with their own listing at `/blog` and
their own feed. Managers reach for the wrong one about half the time.

Most of a hub's blogging happens without a manager touching this screen at
all. Member and group blogs are written by their owners; the Blog Manager
exists mainly so you can find a post someone else wrote, unpublish something
that should not be up, or delete a spam post from a member who has since been
blocked.

Open it under **Components > Blog**. Two sub-menu links sit at the top
left: **Entries** and **Comments**.

## Entries

The Entries screen lists every post on the hub, whatever its scope. The
columns are **ID**, **Title**, **Author**, **State**, **Created**,
**Comments**, and **Scope**. Click a column heading to sort by it; click
again to reverse the order. The list is sorted by title ascending until you
change it.

The **Comments** column is two cells. The first is an on/off switch that
toggles whether the post accepts comments; clicking it saves immediately.
The second reads "*n* comment(s)" and opens the Comments screen filtered to
that post. The **Scope** column prints the raw scope and scope ID, as in
`member (1042)`.

The filter bar above the list offers:

- **Search** — matches the post title only, not its content. Type a term
  and press **Go**, or press **Clear** to reset.
- **Scope** — Site, Member, or Group. The choices are read from the
  adapter files in the component, so they follow whatever scopes the code
  supports.
- **State** — All States, Unpublished, Published, or Trashed.
- **Access** — one of the hub's viewing levels.

The toolbar offers:

- **Options** — the component's configuration; see [Options](#options).
- **Publish** and **Unpublish** — set the state of the checked posts.
- **Delete** — remove the checked posts after a confirmation.
- **Edit** — open the checked post. Clicking a title does the same.
- **New** — create a post.
- **Help** — the built-in help screen.

> **Warning:** **Delete** destroys the rows outright. It is not the same as
> setting a post's state to Trashed, and there is no trash to recover from.

> **Note:** The filters remember themselves. If a scope or state filter is
> still set from a previous visit, the list looks empty even though posts
> exist; press **Clear** and reset the drop-downs.

## Creating or editing an entry

The edit screen is in two halves. Details on the left:

| Field | Notes |
|---|---|
| Scope | Site, Member, or Group. Editable while the post is new; afterwards only an administrator with the Admin permission can change it, and the form shows a warning that changing scope may break references to uploaded files. |
| Scope ID | The member ID or group ID the post belongs to. Leave 0 for the site blog. Follows the same rule as Scope. |
| Title | Required. Up to 250 characters. |
| Alias | Optional. The last segment of the post's URL. Left blank, it is generated from the title: lowercased, trimmed to 100 characters, punctuation removed, spaces replaced by hyphens. |
| Content | Required. The post body, in the editor. |
| Tags | Optional, comma-separated. |

The panel on the right shows the post's **ID**, **Creator**, **Created**
date, and **Hits**, none of which you can edit, followed by the publishing
options:

| Field | Notes |
|---|---|
| Allow comments | Whether members may comment on the post. |
| Access | Which viewing level a visitor needs: Public, Registered, or another level defined on the hub. |
| State | Unpublished, Published, or Trashed. |
| Publish up | When the post starts being visible. Set automatically to now on a new post. |
| Publish down | When the post stops being visible. Leave blank for no end. |

Press **Save** to save and stay on the form, **Save & Close** to return to
the list, or **Cancel** to discard changes.

> **Note:** Both timestamps are read and written in the hub's configured
> time zone offset, not UTC, and a post whose publish-down date has passed
> still shows in this list marked as expired.

## Comments

The Comments screen lists comments and replies as an indented tree, oldest
first. The columns are **ID**, **Comment** (the first 90 characters of the
text, stripped of markup), **Author**, **Anonymous**, and **Created**. A
search box filters on the comment text.

Reaching the screen from the sub-menu lists comments from every blog post
on the hub. Reaching it by clicking a post's comment count limits the list
to that post, and the post's scope and title appear in a header row above
the columns.

The toolbar offers **Delete**, **Edit**, **New**, and **Help**. There are
no publish buttons here; a comment's state is set on its edit form.

> **Note:** The **Anonymous** column renders as an on/off switch, but the
> link behind it points at a task the comments controller does not
> implement. Clicking it produces an error. Change the flag on the
> comment's edit form instead.

A comment's edit form has an **Anonymous** checkbox — which hides the
commenter's name and picture from other members, though the record still
carries who wrote it — and a required **Content** field. The panel beside
it shows the comment's ID, creator, creation date, and the entry number it
belongs to, and lets you set the **State**: Unpublished, Published,
Trashed, or **Flagged**. A flagged comment stays on the page but its text
is replaced with a notice that it has been reported. Saving returns you to
the comment list for that entry.

## Unpublishing a post you did not write

The job that brings a manager here: a member's blog post is spam, or a group
post names someone it should not, and you need it off the site now.

1. Go to **Components > Blog**. Press **Clear** first — the filters remember
   themselves from your last visit and will hide the post you are looking for.
2. Set **Scope** to Member or Group, whichever it is, and search the title.
   The **Scope** column prints the scope and its ID, so `member (1042)` tells
   you which member's blog it is on.
3. Check the row and press **Unpublish**.

The post is off the site immediately — its URL now refuses anyone without
manage rights — and the row is untouched — the author's text, comments, and uploaded files all
remain. Pressing **Publish** puts it back. That reversibility is why
unpublishing is the right first move even when you are fairly sure the post
should go.

> **Warning:** Do not reach for **Delete** at step 3. It destroys the rows,
> there is no trash to recover from, and if you have misjudged the post you
> cannot restore it or show the author what you removed. Unpublish now,
> decide later.

If the member's account is the problem rather than the post, blocking the
account handles the blog too: `plg_members_blog` trashes a member's posts
when the account is blocked without approval, and deletes them when the
account is deleted.

## Options

The **Options** button opens the component's configuration, grouped into
four tabs. **Basic** sets the blog's title and the upload path for files
attached to site-blog posts (`/site/blog` by default). **Archive** chooses
which posts the site blog at `/blog` pulls in — site only, member only,
group only, or all three — and how long an intro each entry gets in a
listing and whether its markup is stripped. **Entry** sets the defaults
for showing authors, allowing comments, and which date to print.
**Feeds** enables the RSS feeds and chooses whether feed items carry the
full post or a 300-character excerpt. Every option is listed with its
values in the
[configuration reference](../../reference/configuration/components/blog.md).

The one setting worth a decision is **Archive**: which posts the site blog
at `/blog` pulls in. Pulling in member and group posts fills a quiet hub's
front-page feed, but it also puts a member's personal writing on the hub's
own blog, which they may not have expected. Site only is the default, the safer
setting, and the one to keep unless you have told members otherwise. Changing it takes
effect on the next page load and is undone by changing it back.

The **Permissions** tab controls who may administer the component, manage
it, and create, delete, edit, change the state of, or edit their own
entries and comments. The permission set is defined separately for the
component as a whole, for entries, and for comments.

> **Note:** The administrator screens ask for permissions on the component
> as a whole, so the per-entry and per-comment permission rows do not
> change what the Blog Manager lets you do. They apply on the site side.

## Member and group blogs

Member and group blogs are rendered by plugins, and each is configured in
its own place:

- The member blog is `plg_members_blog`, under **Extensions > Plugins >
  Members - Blog**. Its parameters set whether the Blog tab appears on
  profiles, the upload path (`/site/members/{{uid}}/blog`), intro length
  and whether intros are stripped of markup, and the feed defaults. A
  member overrides the feed settings for their own blog from the
  **Settings** button on their blog page.
- The group blog is `plg_groups_blog`, under **Extensions > Plugins >
  Groups - Blog**. Alongside the same intro and feed parameters it sets
  the default plugin access (any visitor, registered users, group members
  only, or off), whether the Blog tab shows in the group menu, and whether
  all members or only managers may post. A group manager overrides posting
  and feed settings from the **Settings** button inside the group's blog.

The member plugin also trashes a member's posts automatically when the
account is blocked without being approved or its address is prefixed
`SPAM_`, and deletes them when the account is deleted. The group plugin
deletes a group's posts when the group is deleted.

## API

The component exposes `GET /api/blog/list`, `POST /api/blog`,
`GET /api/blog/{id}`, `PUT /api/blog/{id}`, and `DELETE /api/blog/{id}`.
See the [API reference](../../reference/api/blog.md).
