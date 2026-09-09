<!--
status: rewritten
reviewed-against: 2.4-main @ d48e29db14
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/users/support
-->
# Support

Support is where you tell the hub's staff that something is wrong. You
file a ticket, the support team answers it, and you follow the
conversation until the problem is fixed. It lives at `/support`, and most
hubs also put a **?** tab or a help link on every page that opens the same
form in place.

## The Support Center

`/support` is the Support Center. It points you at the other places an
answer might already be waiting — the [knowledge base](knowledgebase.md),
[questions and answers](questions.md), the [wiki](wiki.md),
[resources](resources.md), [tags](tags.md), and [search](search.md) — and
at the two things you can do here: **Report Problems** and
**Track Tickets**. A **Quick Links** panel repeats those, plus the
**Support FAQ's** popup.

## Submitting a ticket

There are two ways in.

**From any page on the hub.** Select the **?** tab. The report form drops
down over the page you were on, already carrying the address you came
from.

![The report form opened from the ? tab](media/support-support-newticket1.png)

**From the Support Center.** Go to `/support/ticket/new`, or follow
**Report Problems** from `/support`.

![The full new ticket form](media/support-support-newticket2.png)

The form asks for one thing: a **Detailed Description** of what went
wrong. Say what you were doing, what you expected, and what happened
instead. If you are not logged in, you also have to give your **Name** and
**Email** — both required — and you may add your **Username**. Logged in,
those are filled in from your account and hidden.

Below the description is an **Attachments** box. Drag a file onto it or
click to browse; the allowed file types are listed underneath. A
screenshot helps enormously, and a screenshot that includes the address
bar helps more.

If you are not logged in, or your e-mail address has never been confirmed,
a **Human Check** appears — a field you must leave blank, and on many hubs
a simple sum to answer. It is there to keep robots from filling the queue
with spam.

Press **Submit**. The next page gives you your ticket number and a link to
the ticket, thanks you, and reminds you that tickets are generally
answered during business hours. A **New report** button starts another
one.

> **Note:** If you submit the same text twice within a few seconds — a
> double-clicked button, usually — the second submission is refused and the
> form comes back so you can check it.

The addresses the hub has set for new-ticket notifications are e-mailed
straight away, and the ticket turns up in your own list under
**Reported by me**.

## Tracking your tickets

`/support/tickets` lists tickets. You have to be logged in; the page sends
you to the login form and back again.

The left pane holds saved queries, grouped into folders — **Open
tickets**, **New tickets**, **Closed tickets**, **Reported by me**,
**Assigned to me**, and so on — each with a count. Pick one to fill the
list beside it. If you are not on the hub's support staff, the queries are
filtered so you only ever see tickets you submitted, tickets assigned to
you, and tickets belonging to a group you are in.

Sort the list with the **Sort results** bar: **Age**, **Status**,
**Severity**, **Summary**, **Group**, or **Assignee**. The **Find** box
searches within the query you have selected. Each row shows the ticket
number, its status, when it was opened, when it was last commented on, the
summary, and the severity.

Support staff get more here: a **Watch list** with counts of the open and
closed tickets they are watching, a **Stats** button, and controls for
adding query folders and building their own queries.

## Reading a ticket

`/support/ticket/123` — or clicking the summary in the list — opens one
ticket. You can open a ticket you submitted, one assigned to you, one
belonging to a group you are in, or one you were copied on in the latest
comment; anything else returns a "not authorized" page.

The ticket shows your original report, anything you attached, and the
technical details collected when you filed it: the browser and operating
system, the address you were on, and whether cookies were enabled. Under
it runs the conversation — every comment, with its author and time — and
the log of what changed, so you can see when the ticket was reassigned, or
its severity or status altered. Comments marked private by staff are not
shown to you.

Beside the ticket, its status says **open ticket** or **closed ticket**.

## Adding a comment

The comment form sits at the bottom of the ticket. Write your reply and
press **Submit**. Attach files to a comment the same way you attach them
to a new ticket.

Because the ticket is yours, you also get a single checkbox:

- **Close ticket (issue resolved or no longer relevant)** while it is open.
- **Re-open ticket (issue not resolved?)** once it has been closed.

You do not have to tick it to reopen a closed ticket, though — posting any
comment reopens it, and a closed ticket carries a note saying so.

## Watching a ticket

**Watch ticket** in the sidebar adds the ticket to your watch list. While
you are watching, you are e-mailed about every comment and every change.
**Stop watching** ends it. Watchers are notified anonymously — nobody else
sees who is watching.

## Terse comment e-mails

Hub administrators and support staff see a **Terse** checkbox under the
comment box.

![The Terse checkbox on the comment form](media/support-support-terse.png)

Left clear, the e-mail sent to the ticket's recipients contains the whole
comment. Checked, they get only the ticket number and a line saying the
ticket has been updated, with a link to come and read it. Hubs that must
keep sensitive material out of e-mail — those working to HIPAA or FISMA
rules — turn this on hub-wide, and then the box is checked for every
comment; staff can still clear it one comment at a time. See
[Support](../managers/09-components/support.md) in the managers book.

## Reporting abuse

Comments, reviews, and other member-written content around the hub carry a
**Report abuse** link. It opens a short form at `/support/reportabuse`
showing the content you are reporting and asking for a **Reason** —
*Offensive content*, *Stupid*, *Spam*, or *Other* — with a box for
anything you want to add. You have to be logged in.

Submitting gives you a report number. A member of staff reviews the
report and either releases the content or takes it down; if it is taken
down, the person who posted it is e-mailed. Where the hub runs points, you
may be credited some for a report that turns out to be valid.
