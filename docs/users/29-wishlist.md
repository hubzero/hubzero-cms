<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/users/wishlist
-->
# Wish list

The wish list is where you ask for something that does not exist yet: a
feature, a tool, a change to how something works. An instructor marking
forty submissions from a hub tool wants a button that exports the whole
cohort's results as one file, because doing it one student at a time is
the reason the assignment takes her a weekend. There is nothing broken
to report and nobody can answer the question today — what she wants is
for somebody to build it.

That is what separates a wish from everything else you can write on the
hub. A wish is a request for work. It goes on a list, other members vote
it up or down and comment on it, the people who own the list rank it
against everything else on the list, and it ends up **Accepted**,
**Granted**, **Rejected** or **Withdrawn**. It is slow on purpose: the
voting is the point, because it is how a list's owners find out which of
forty requests matter to more than one person.

The hub's main list is at `/wishlist`, usually linked from the support or
help menu. Groups and resources can have lists of their own, shown on
their **Wishlist** tab — so the export button above belongs on the
tool's own list, where the tool's authors will see it, rather than on
the hub-wide one.

## Which one do I want?

| If you want to | Use |
|---|---|
| Ask for a feature, tool, or change to be built | The wish list — this page |
| Report something broken, or ask the hub's staff for help | [Support](12-support.md) |
| Get one answer to a specific question, from anyone on the hub | [Questions and answers](19-questions.md) |
| Discuss something open-ended, where several answers are defensible | [Forum](09-forum.md) |

The line that matters is between a wish and a ticket. If the software is
doing something it should not, that is a fault and belongs in
[Support](12-support.md), where somebody is expected to answer. If the
software is working as built and you want it built differently, that is
a wish. Filing a fault here can leave it sitting for months waiting for
votes; a list owner can move a wish onto a support ticket, but nobody
will do it for you if you never say the thing is broken.

## The list

The list page shows the wishes on one list, open ones first and the newest
of those at the top, with an **Add a Wish** button at the top right. Each
row gives the wish's title, its
number, who proposed it and when, and how many comments it has, plus the
like and dislike counts. On the right of the row you see the wish's state
— **Accepted**, **Granted**, **Rejected** or **Withdrawn** — or nothing at
all while it is still pending.

Above the table is a search box; type a few words and press **Search** to
match wish titles and descriptions. Two menus sit beside it:

- **Sort by** — **Date** (newest first), **Feedback** (most liked first),
  or **Submitter**. **Bonus** appears when the hub awards points, and
  **Ranking** only for list owners.
- **Filter by** — **All**, **Active**, **Accepted**, **Rejected**,
  **Granted**, and, once you are logged in, **Submitted by me**. List
  owners also get **Public** and **Private**.

> **Warning:** There is a sixth filter, **Assigned to me**, which does
> not work. The link is shown only to someone holding an access flag
> that no code in the hub ever sets, so nobody ever sees it; and if you
> build its address by hand, the controller discards the value and shows
> you every wish instead. To find the wishes assigned to you, open the
> list and read the **Implementation Plan** on each one.

The sidebar carries a short description of the list, and on the main list
a cloud of popular tags. Click a tag to narrow the list to wishes carrying
it; click the small **x** on an applied tag to remove it again.

> **Note:** Wishes a list owner has marked private do not appear at all
> unless you own the list. A whole list can be private too, in which case
> you have to log in and be one of its owners to see anything.

## Posting a wish

Take the instructor's export button through. She opens the tool's page,
selects its **Wishlist** tab, and presses **Add a Wish** at the top
right. You have to be logged in; if you are not, the hub sends you to
the login page and back again. The form asks for:

| Field | Notes |
|---|---|
| Post anonymously | Check it and your name is replaced by "Anonymous" everywhere the wish appears. |
| Summary of your wish | Required. One sentence, up to 200 characters. |
| Explain in more detail | The body of the wish, in the editor. |
| Tag this wish | Tags connect your wish to hub-wide search and to other tagged content. |
| Assign a point reward | Only when the hub runs a points economy. The amount you offer is put on hold against your account until the wish is granted or rejected. |

Press **Save**. Your wish goes onto the list as **Pending**, the list's
owners are e-mailed, and you land on the wish's own page.

Hers reads "Export all students' results as one CSV" in **Summary of
your wish**, with the detail explaining that she does this for forty
students each term and how long it takes. That second part is what earns
votes: a wish that says who wants it and how often is a wish other
people recognise and vote up, and the vote is what moves it up the
list.

## A wish's page

Every wish has a permanent address of the form
`https://<your hub>/wishlist/general/1/wish/<number>`, which you can share.
The page shows who proposed it and when, the summary, the detailed
description, and its tags.

Below the wish is the row of actions available to you:

- **edit** — change the wish. Available to you on your own wishes and to
  list owners.
- **Report abuse** — flag the wish for the hub's support staff. The wish
  is hidden behind a notice until they review it.
- **Withdraw my wish** — take back a wish you posted, while it is still
  open. You are asked to confirm; any points you put on hold come back.

List owners get more: **Change status**, **Move** (to another list, or to
a support ticket), and **Make it public** / **Make it private**.

## Voting and commenting

Anyone logged in can press the **Like** or **Dislike** button beside a
wish, on the list or on the wish's own page. You get one vote per wish and
you can change it later. You cannot vote on your own wish, and voting
closes once a wish is granted, rejected or withdrawn; the buttons say so
when you hover over them.

Comments run below the wish. Log in, write your comment in the box under
**Add a Comment**, and press **Post comment**. The same form takes an
**Attach file** and a **File description**; images are shown inline under
the comment, other files as download links. Check **Post comment
anonymously** to hide your name.

Each comment has a **Reply** link, which opens a smaller form under it —
the conversation nests three levels deep — and a **Report abuse** link.
Replies carry no attachment.

## How the consensus is worked out

This section is about what the list's owners do, not what you do. Read
it if you want to know why your wish is where it is in the list, or skip
it — you cannot rank a wish unless you own the list or sit on its
advisory committee.

The owners of a list — and, where the hub has enabled one, its advisory
committee — rank each wish under **My Opinion**. There are two
selections:

- **Importance**, from *rubbish* through *not important*, *to consider*,
  *nice to have*, *a must*, up to *crucial*.
- **Effort**, an estimate of how long it would take: *few hours*, *one
  day*, *few days*, *one week*, *few weeks*, *several months*, or *don't
  know*.

Press **Save** to record your ranking. The **Consensus** column averages
everyone's answers, and the **Community Vote** column shows the likes and
dislikes from everyone else. Those numbers combine into the wish's
priority, weighted by how many of the eligible voters have actually voted,
so a wish ranked by most of the owners counts for more than one ranked by
a single person. Where the hub gives the advisory committee extra weight,
their rankings count four times as much as an ordinary owner's.

> **Note:** Only a list's own owners and advisory committee can rank a
> wish. Being a site administrator is not enough on its own.

## The implementation plan

The plan is how an accepted wish turns into a commitment you can check
on. Once a wish is accepted, its owners can write an **Implementation
Plan** below the comments: who the wish is assigned to, when it is due, and a
description of the work. Each save can be kept as a new revision, so the
plan's history survives. The wish shows how long is left before the due
date, and turns overdue once it passes.

## Managing a list

If you own a list, the sidebar tells you so and links to **List
Settings**. There you set the list's title and description, make it public
or private, and choose who owns it: whole groups, named individuals, and —
if the hub has enabled it — an advisory committee. Adding or removing an
owner re-ranks every wish on the list, because the pool of eligible voters
has changed.

Some settings are fixed. The main site-wide list and resource lists cannot
be made private, and a list's built-in owner group cannot be removed.

## Related

- [Support](12-support.md) — report a problem rather than ask for a feature.
- [Questions and answers](19-questions.md) — ask the community.
- [Forum](09-forum.md) — talk it over before you file it.
- [Knowledge base](13-knowledgebase.md) — check whether it already exists.
