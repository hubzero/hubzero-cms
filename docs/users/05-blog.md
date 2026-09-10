<!--
status: rewritten
reviewed-against: 2.4-main @ ddeb90135f
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/users/blog
source-id: 3295
modified: 2014-11-17
-->
# Blog

Your blog is a dated journal on your member profile: notes on what you are
working on, results, opinions, anything you want to publish under your own
name. Other members can comment on your entries, and you decide for each
entry whether anyone, only logged-in members, or only you can read it.

There are three blogs on a hub, all built from the same component:

- **Your blog**, at `https://<your hub>/members/<your ID>/blog`, reached
  from the **Blog** tab on your profile.
- A **group blog**, at `https://<your hub>/groups/<group>/blog`, reached
  from the **Blog** tab inside a group.
- The **site blog**, at `https://<your hub>/blog`, written by hub staff.

Depending on how your hub configures the site blog, it may show only its
own entries or pull in member and group entries as well.

## Your blog page

The Blog tab lists your entries newest first. Each one shows its title,
its entry number, the date and time it was published, its author, and
either the number of comments on it or **Comments off**. Below that comes
an excerpt — the first 300 characters or so of the entry. On your own
entries you also see the privacy level and **Edit** and **Delete** links.

A search box at the top finds entries by title and text. The sidebar has
**Entries By Year**, which drills down to a year and then a month, and
**Popular Entries**, the five entries with the most views. When feeds are
turned on, an **RSS Feed** link beside the heading gives you the feed for
whatever you are looking at, including a single month.

If your blog is empty, you see a short introduction instead of the list,
explaining what a blog is and inviting you to press **New entry**.

Two buttons sit at the top of your own blog: **New entry** and
**Settings**. They do not appear on anyone else's blog.

## Writing an entry

Press **New entry**. The form asks for:

| Field | Notes |
|---|---|
| Title | Required. |
| Entry | Required. The body of the post, in the editor. |
| Uploaded files | An uploader for images and attachments. Choose a file and press **Upload**; the list above shows what you have already uploaded, each with a delete link. Reference the files from the entry text with the hub's content macros. |
| Tags | Optional, comma-separated — for example `negf theory, ion transport`. Tags connect your entry to everything else on the hub with the same tag. |
| Allow comments | Checked by default. Uncheck it to close the entry to comments. |
| Privacy | **Public (anyone can see)**, **Registered members**, or **Private (only I can see)**. |
| Start publishing | When the entry becomes visible. Leave blank to publish now. |
| End publishing | When the entry stops being visible. Leave blank for no end. Format both as `YYYY-MM-DD hh:mm:ss`. |

Press **Save**. You land on the finished entry, at a permanent address
built from its publication date and title, such as
`/members/1042/blog/2026/09/my-first-results`.

> **Note:** You can only write and edit entries on your own blog. Opening
> someone else's blog with an edit link returns "You do not have permission
> to perform that action."

To change an entry later, open it or find it in the list and press
**Edit**. The same form comes back with everything filled in.

## Deleting an entry

Press **Delete** on an entry. A confirmation page warns that the entry and
all its comments are about to go, with a **Yes, I want to delete this
entry** checkbox. Tick it and press **Delete**.

> **Note:** Deleting marks the entry as trashed rather than erasing it. It
> disappears from your blog, from listings, and from feeds immediately, but
> a hub administrator can still see it in the Blog Manager.

## Settings

**Settings** on your own blog controls your feeds:

- **RSS Feed of entries** — enabled or disabled.
- **The length of RSS feed entries** — **Full** or **Partial**, a
  300-character excerpt.

Privacy is not a setting here; it belongs to each entry.

> **Note:** Feeds only ever carry entries marked public. There is no way to
> produce a private feed, so entries limited to registered members or to
> yourself never appear in one, whatever the setting.

## Reading and commenting

An entry page shows the title, the entry number, the date and time, the
text, and the entry's tags. Click a tag to find related content.

Where comments are allowed, they follow the entry as a threaded
conversation. Each comment shows the commenter's picture and name — or
**Anonymous** — with a permalink carrying the time and date, marked
**Edited** if it has been changed since.

To add one, log in, write it in the form at the bottom, and press
**Submit**. Tick **Post anonymously** to hide your name and picture from
other readers. Guests see a note saying they must log in first.

On each existing comment you can:

- **Reply** — opens a form under that comment. Replies nest up to three
  levels deep; below that the Reply link stops appearing.
- **Edit** — on your own comments, and on any comment on your own blog.
- **Delete** — on comments on your own blog.
- **Report abuse** — files a support ticket about the comment. A reported
  comment stays in place but its text is replaced by a notice that it has
  been reported, until the hub's support staff deal with it.

> **Note:** Member blogs have a feed of entries but not of comments. Only
> the site blog offers a per-entry comment feed, at the entry's address
> followed by `/comments.rss`.

## Group blogs

A group's blog works the same way, but the group decides who may write in
it. Group managers set that from the **Settings** button inside the
group's Blog tab, choosing whether **all members** or **managers only** may
post, along with the group blog's feed options. Whether the Blog tab shows
at all, and who can read it, are group plugin settings a hub administrator
controls.

Entries in a group blog belong to the group rather than to you, and the
group's managers can edit and remove them.

## The site blog

The site blog at `/blog` is the hub's own. You can read it, search it,
subscribe to its feed, and comment on entries that allow comments, exactly
as on a member blog. Writing to it needs a permission that ordinary
members do not have; if your account has it, a **New entry** button appears
there and the form works the same way.

See the [Blogs](../managers/09-components/04-blogs.md) chapter in the
managers book for how a hub administrator configures all three.
