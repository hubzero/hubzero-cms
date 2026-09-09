<!--
status: rewritten
reviewed-against: 2.4-main @ ddeb90135f
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/users/forum
-->
# Forum

The forum is the hub's message board, at `/forum`. Discussions are grouped
into **sections**, each section holds **categories**, and each category holds
**discussions** — a thread made up of an opening post and the replies to it.
Groups have their own forums, described in
[Group forum](groups/groupforum.md).

You can read the forum without an account if the hub allows it. Posting
always requires you to log in.

## Finding a discussion

The forum's front page lists every section with the categories inside it. Each
category row shows its description, how many discussions it holds, and how
many posts. A padlock in place of the folder icon means the category is
locked: you can read it, but you cannot start a new discussion there.

The sidebar carries a **Statistics** panel counting the categories,
discussions, and posts on the hub, and a **Last Post** panel linking to the
most recent post anyone has made.

The search box at the top matches your words against post titles and post
text. Press **Search** to run it; results list the matching posts with the
section and category each one lives in.

Click a category to see its discussions. Every discussion shows its title, who
started it and when, how many posts it holds, and who posted last. Sort the
list with the **Created**, **Activity**, **# Posts**, and **Title** links —
clicking the active one again reverses the order. Two icons mark special
discussions: an asterisk for a sticky discussion, which is pinned to the top
of the list regardless of age, and a padlock for a closed one, which no longer
accepts posts. **All categories** takes you back to the front page.

Every discussion has an address you can share:
`https://<your hub>/forum/<section>/<category>/<id>`.

## Reading a discussion

Posts run oldest to newest, each with the author's name and picture, the time
it was posted, and an **Edited** stamp if it has been changed since. The
**@**/**on** timestamp beside a post is a permalink to that post. Where the
hub has turned on nested threads, replies are indented under the post they
answer.

The sidebar of a discussion lists **Tags On This Discussion**, **In This
Discussion** — everyone who has posted, with anonymous posters shown once as
Anonymous — and **Attachments**, every file attached anywhere in the thread.

If you are logged in, a heart under each post lets you like it. The count
beside it opens the list of everyone who has liked that post, with links to
their profiles. Click the heart again to take your like back.

## Posting a reply

The **Add Post** form sits at the foot of every open discussion. Log in,
write your post in the **Comments** editor, and press **Submit**. Guests see
"You must be logged in to comment." in place of the form, and a closed
discussion has no form at all.

- Type an `@` in the editor to mention another member. The name becomes a link
  to their profile, and they are emailed a copy of your post.
- Add **Your tags** to connect the discussion to other tagged content on the
  hub.
- Attach one file with **File**, optionally naming it in **Description**.
- Check **Post Anonymously** to keep your name off the post, if the hub allows
  it. Hub managers can still see who posted.

Where nested threads are enabled, each post also carries a **Reply** link that
opens a small form directly under it, so your answer is attached to that post
rather than to the end of the discussion. Nesting stops at the depth the hub
has set.

## Starting a discussion

Open the category you want to post in and press **New Discussion** in the
sidebar. The button is absent when the category is locked; you are sent to the
log-in page if you are not signed in.

| Field | Notes |
|---|---|
| Who can read this thread? | The access level needed to read it — Public, Registered, or another level the hub defines. |
| Category | Required. The menu lists every category you can post in, grouped by section. |
| Title | The subject line of the discussion. |
| Comments | Required. The opening post, with the same `@` mentions as a reply. |
| Your tags | Optional keywords. |
| File, Description | One attachment, with an optional description. |
| Post Anonymously | Hides your name, if the hub allows anonymous posts. |

Forum moderators also get **Make discussion sticky** and **Closed thread (no
new posts)** on this form. Press **Submit** to post; the hub confirms with
"You have successfully created a new discussion topic." and drops you into the
new discussion.

## Editing and deleting

Your own discussions and posts carry **Edit** and **Delete** links. Editing
reopens the same form; the post is stamped as edited afterwards. Deleting asks
you to confirm, then hides the post and everything under it — replies to a
deleted opening post go with it.

> **Note:** Deleting only hides the post from the site. A hub administrator
> can still see it, and can restore it.

## Reporting a post

Every post has a **Report abuse** link. It opens the hub's abuse form, and
once a post is reported its text is replaced on the site by "This comment has
been reported as abusive and/or containing inappropriate content." until the
hub's support staff review it.

## Managing categories and sections

Some hubs let members organise the forum as well as post in it. When yours
does, the front page grows a **New Section** form in the sidebar and a **New
Category** button under each section, and sections and categories gain
**Edit** and **Delete** icons.

Creating a category asks for a title, a description, who can see it, and the
section it belongs to. **Lock Category (no new posts)** closes it to new
discussions while leaving what is already there readable.

> **Warning:** Deleting a category hides every discussion inside it. Move
> anything you want to keep to another category first.

If the forum is completely empty and you may create sections, the front page
offers to generate an example section and category for you to start from.

> **Note:** Older hub documentation pointed at `/forum/latest.rss` for a feed
> of new discussions. That feed is no longer served, and the address returns
> an error.

## Elsewhere on the hub

A group's forum works the same way but is reached from the group's **Forum**
tab, is limited to the group's members, and can email you new posts —
individually or as a daily, weekly, or monthly digest. See
[Group forum](groups/groupforum.md). For questions that want a single answer
rather than a discussion, use [Questions and answers](questions.md); to reach
the hub's staff, use [Support](support.md).
