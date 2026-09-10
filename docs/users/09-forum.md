<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/users/forum
-->
# Forum

The forum is the hub's message board: the place for a conversation that
has no single right answer, held in public, where whoever arrives next
can read it. A materials group argues out which exchange-correlation
functional to trust for a family of compounds, three people weigh in
over a fortnight, and the thread stays on the hub as the record of why
the group settled where it did. Nothing about that fits a question with
one accepted answer or a ticket that gets closed.

If you only want to know whether the hub has a forum and where it is: it
is at `/forum`, most hubs link it from the community or support menu,
and you can read it without an account if the hub allows it. Posting
always requires you to log in.

Discussions are grouped into **sections**, each section holds
**categories**, and each category holds **discussions** — a thread made
up of an opening post and the replies to it. Groups have their own
forums, described in [Group forum](11-groups/05-groupforum.md).

## Which one do I want?

Four parts of the hub take a written message from a member, and it is
easy to file in the wrong one. The forum is the one that never closes.

| If you want to | Use |
|---|---|
| Discuss something open-ended, where several answers are defensible | The forum — this page |
| Get one answer to a specific question, and mark it as the answer | [Questions and answers](19-questions.md) |
| Report something broken, or ask the hub's staff for help | [Support](12-support.md) |
| Ask for a feature, tool, or change to be built | [Wish list](29-wishlist.md) |

A forum thread has no accepted answer, no assignee, and no state that
says it is finished. That is a feature when the subject is a method, a
convention, or an argument, and a nuisance when you have a broken tool
and need someone to fix it. If your post ends in "is this a bug?", file
a support ticket instead: nobody is on duty to read the forum.

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

Say you want the functional argument on the record. You open `/forum`,
find the section your hub uses for simulation topics, click into the
category inside it that covers methods, and press **New Discussion** in
the sidebar. Posting into the category you are already reading is the
whole trick — the form starts with that category selected.

1. Press **New Discussion**. The button is absent when the category is
   locked, and you are sent to the log-in page if you are not signed in.
2. Fill in the form below. **Category** and **Comments** are required;
   everything else is optional.
3. Press **Submit**.

The hub confirms with "You have successfully created a new discussion
topic." and drops you into the new discussion, where you can copy its
address out of the browser and paste it into your group's chat.

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
new posts)** on this form.

If a discussion lands in the wrong category, press **Edit** on the
opening post: the **Category** menu comes back and choosing another one
moves the whole thread. The menu on a reply is hidden, because a reply
always follows its discussion.

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
[Group forum](11-groups/05-groupforum.md). A lab that wants its method
arguments kept among its own people wants that one rather than this one:
the hub forum has no way to restrict a discussion to a named set of
readers beyond the access levels the hub defines.

For a question that wants a single answer rather than a discussion, use
[Questions and answers](19-questions.md). To reach the hub's staff about
something broken, use [Support](12-support.md). To ask for something to
be built, use the [wish list](29-wishlist.md).
