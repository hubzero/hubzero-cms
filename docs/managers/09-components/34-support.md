<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/managers/components/support
-->
# Support

Support is the hub's ticket tracking system. Members report a problem at
`/support`, the ticket lands in a queue, and support staff answer it,
reassign it, tag it, and close it — with every change recorded in the
ticket's log. The component also collects the abuse reports that members
file against comments and other content elsewhere on the hub. This chapter
covers the administrator's side; the [Hub users](../../users/12-support.md)
book covers submitting and tracking a ticket.

Every hub needs this one. It is the route members take when something is
wrong, and unlike the rest of the components in this book you do not choose
whether to run it — you only choose whether anyone is watching it. A member
whose tool session dies at midnight files a ticket whether or not you have
configured a single thing, and the ticket sits in the queue until someone
opens it. So the question this chapter answers is not "should I turn this
on" but "what has to be in place before the first ticket arrives, and how do
I work the queue once they do".

> **Note:** This is not the member messaging system. A reply on a ticket is
> *delivered* through that system, but tickets, statuses, assignment and the
> ACL described here have nothing to do with the **Messages** component or
> the **Messages** tab on a member's profile. See
> [Messages](22-messages.md) if that is what you are looking for.

Open it under **Components > Support**. Eight sub-menu links run across
the top: **Tickets**, **Categories**, **Queries**, **Messages**,
**Statuses**, **Abuse**, **Stats**, and **ACL**.

## Before the first ticket arrives

A new hub ships with the ticket system working and almost nothing in it.
`#__support_statuses`, `#__support_categories` and the three ACL tables are
all empty on a fresh install, which means tickets arrive, but nobody except
a super administrator can see them and the only statuses are the built-in
**New** and **Closed**. Half an hour spent here saves a year of confusion.

1. **Decide who answers tickets, and give them access in the
   [ACL](#acl).** Until you do, the only people who can work the queue are
   super administrators, because the ACL check passes anyone holding
   `core.admin` and refuses everyone else. Adding a hub *group* rather than
   naming individuals is worth the extra minute: staff turnover then means
   changing the group, not revisiting this screen.
2. **Set the address that hears about new tickets.** **Options > Notify
   when ticket created** ships as `{config.mailfrom}`, the hub's own From
   address, so notifications go somewhere real but probably to a mailbox
   nobody reads as a queue. Point it at whatever your staff actually watch.
   Leave it blank and no one is told a ticket exists.
3. **Add the [statuses](#statuses) you will actually use.** With none, every
   ticket is either New or Closed, and you cannot tell "waiting on the
   member" from "waiting on us".
4. **Add a few [categories](#categories)**, if you want tickets sorted by
   subject at all. This is optional and easy to add later.
5. **Check the attachment settings** under **Options**, so that a member
   sending you a screenshot is not turned away.

Do all five before you advertise the support link, not after. Changing the
ACL and the statuses later is safe — existing tickets keep whatever status
they had, and a status you remove simply stops being offered — but until
they exist, tickets accumulate that nobody has triaged.

## Tickets

The Tickets screen is the queue, and it is where you will spend your time.
It is split into three panes. The left pane is your list of saved queries;
the middle pane holds the tickets the selected query returns; the right pane
shows a ticket you pick from the list.

The important idea is that the queue is not one list — it is whatever the
selected query returns. A query is a saved filter, and the stock ones
(*Open tickets*, *New tickets*, *Assigned to me*) are how most people work:
open *New tickets*, triage what is there, assign it, and it leaves that list
for someone else's.

Above the queries sits **Watch list**, with **Open tickets** and
**Closed tickets** counts for every ticket you are watching. Below that
are your query folders. Each folder and each query carries **Edit** and
**Delete** links, and the two controls at the bottom, **Add custom query**
and **Add folder**, create new ones. Folders and queries can be dragged
into a new order, which is saved as you drop them.

> **Note:** Watching and owning are different. **Watch ticket** puts a
> ticket on *your* watch list and tells you about changes; **Assigned to**
> says who is responsible for it. Watching someone else's ticket does not
> take it off them.

The ticket list has a **Sort results** bar — **Age**, **Status**,
**Severity**, **Summary**, **Group**, **Assignee** — and a **Find** box
that searches within the current query. Each row shows the ticket number,
its status (coloured with the status's own colour), the time of the last
comment, any target date, the submitter, the summary, tags, group,
assignee, and severity.

The toolbar offers **Options**, **New**, **Delete**, and **Help**.

> **Warning:** **Delete** removes the ticket and everything attached to
> it — its comments, its log and its files — and there is no trash to
> recover from. To take a ticket out of the queue without destroying it,
> set it to a closed status instead. Reserve **Delete** for spam.

> **Warning:** There is no way to act on many tickets at once. A batch
> screen exists in the code, but the link that reached it is commented out
> of the ticket list template, so nothing in the interface renders it, and
> the screen behind it has been partly disabled — it changes the fields and
> tells nobody, and the comment it writes to the ticket's log is empty.
> Treat it as gone. If a spam wave puts two hundred junk tickets in the
> queue, you close or delete them one at a time. Recorded in
> It is recorded with the project.
## Reading and answering a ticket

Clicking a ticket's summary opens it. The top of the screen shows the
original report — which cannot be edited — with the submitter's name,
**Submitter e-mail**, **User type**, **OS / Browser**, **IP and Hostname**,
**Referrer URL**, **Instances**, the user agent string, and any files the
submitter attached. Beside it are the ticket's status, **Severity**,
**Owner**, **Last Activity**, and a **Watch ticket** / **Stop watching**
button.

Below the existing comments is the response form.

| Field | Notes |
|---|---|
| (message template) | Pick a saved response from the list to fill the comment box; **[custom]** leaves it empty. See [Messages](#messages). |
| Comments | The reply. Leave it blank to record only a change of status, owner, or tags. |
| Private | Marks the comment visible to staff only. It overrides every e-mail setting — a private comment is never sent to the submitter. |
| Attachments | Drag files in or click to browse. Size and type come from the component's Options, or the Media Manager's if those are blank. |
| Send message to | Usernames, user IDs, or e-mail addresses to copy, comma-separated. The value is remembered and pre-filled on the next comment. |
| Ticket submitter / Ticket owner | Checked by default; clear either to leave that person out of the notification. |
| Tags | Tags for the ticket, comma-separated. |
| Group | A group the ticket belongs to. With no owner set, the group's managers are notified instead. |
| Assigned to | The person who owns the ticket now. The list is drawn from the users and groups in the [ACL](#acl), or from the group named in the **Group** option. |
| Category | One of the [categories](#categories) you have defined. |
| Target date for completion | Optional deadline, shown on the ticket in the list. |
| Severity | Critical, High, Normal, or Low. |
| Status | Grouped under **Open** and **Closed**. The open group lists your open statuses; the closed group starts with plain **Closed** and then your closed statuses. |

Press **Save** or **Save & Close**. Everything that changed is written to
the ticket's log, along with who was notified.

Two things about that form are easy to get wrong, and both are visible to
the member if you do. **Private** is the only thing that keeps a comment off
the submitter's screen and out of their mail — a status change or an
internal note typed into a public comment goes straight to them. And a
comment with the **Ticket submitter** box left ticked mails them, so a
one-word note to a colleague becomes a support reply. Neither can be
recalled once saved.

> **Note:** Posting a comment reopens a closed ticket. This is deliberate,
> so that a reply is never lost on a ticket nobody is watching.

> **Note:** If someone else commented while you had the form open, the save
> is refused and your text is handed back so you can review it first.

### Working one ticket end to end

A member reports that a simulation tool will not start. The ticket arrives
as **New**, unassigned. This is the sequence, and the rest of the chapter
refers back to it.

1. In **Components > Support > Tickets**, select the **New tickets**
   query in the left pane. The ticket is in the middle pane; click its
   summary.
2. Read the report. The **OS / Browser**, **Instances** and user agent
   lines at the top were captured when it was submitted — you do not have
   to ask for them.
3. Set **Severity**. This is the field the queue sorts by when several
   things are wrong at once, so it is worth being honest: **Critical** for
   something that stops everyone, **Normal** for one member blocked.
4. Set **Assigned to** to the person who will look at it. Only people the
   [ACL](#acl) knows about appear in that list; if the right person is not
   there, that is the screen to fix, not this one.
5. Set **Status** to one of your open statuses — the one that means "we
   have this" rather than leaving it **New**, so that the next person
   opening the *New tickets* query does not triage it again.
6. Type the reply to the member in **Comments**, leaving **Private**
   clear, and press **Save**. They get it by mail, and the ticket's log
   records the status, the assignment and who was notified.
7. When the tool is fixed, reply again and set **Status** to a closed
   status, or to plain **Closed**. The closing time is recorded.

If the member answers afterwards, their comment reopens the ticket and it
comes back into the open queries — which is the behaviour you want, and the
reason not to delete a ticket you think is finished.

**New** on the Tickets toolbar opens a shorter form for entering a ticket
on someone's behalf: **Login**, **Name**, **Email**, **Description**,
tags, group, assignee, severity, status, category, and CC. Use it when a
problem reaches you by mail or in a corridor and you want it in the queue
where the rest of the record is.

## Queries

The Queries screen manages the default queries every user starts with. It is
worth visiting once, when you first set the hub up, and then rarely: what
you change here shapes the queue every new staff member inherits.

Each has a **Type**:

- **Common (in ACL)** — a common query such as *Open tickets* or
  *New tickets*, given to users who hold support permissions.
- **Common (not in ACL)** — the same idea, but filtered so that a user only
  sees tickets they submitted, own, or that belong to one of their groups.
- **Mine** — queries about the logged-in user: *Reported by me*,
  *Assigned to me*, *Assigned to me (closed)*.
- **Custom** — a query a user built for themselves.

Editing one opens the **Query builder**: a title, a set of rules matching
**any** or **all** of the conditions, optional nested groups of rules, a
sort field, and a direction. Conditions can test the owner, group,
submitter, ticket ID, report text, open/closed state, status, created and
closed dates, tag, type, severity, and category.

The toolbar adds **Reset to defaults**.

> **Warning:** **Reset to defaults** deletes every query and folder on the
> hub — everyone's, not just yours, including custom queries people built
> for themselves — and rebuilds the stock set. It cannot be undone and
> there is no confirmation of what will be lost.

> **Note:** Changes here only reach *new* users, the first time they open
> the support component. Existing users keep the copies they already have.
> That is why this screen is for the hub's first week: after that, a change
> reaches nobody who is already working the queue, and you have to tell them
> to build the query themselves.

## Categories

Categories organise tickets by subject — "tool sessions", "accounts",
"storage" — so that a query can pick out one kind of problem and the stats
screen can count them. They are optional; a hub that answers thirty tickets
a month manages fine without any.

The list shows **ID**, **Title**, and **Alias**; an entry has only those
fields, with the alias generated from the title when you leave it blank.
Nothing seeds the list, so it is empty until you add some.

## Statuses

Statuses are the labels a ticket can carry, and they are what makes a queue
readable. The two that matter are the ones that distinguish *we are working
on this* from *we are waiting for the member to answer* — without them, a
ticket blocked on somebody else looks exactly like a ticket nobody has
touched.

Filter the list by **Status for** — *all statuses*, *open*, or *closed*.

| Field | Notes |
|---|---|
| Title | Required. What the status is called on the ticket. |
| Alias | Generated from the title if blank. |
| For | **Open tickets** or **Closed tickets**. Setting a ticket to a closed status closes it and records the closing time. |
| Color | A colour swatch, picked from the colour widget, used to stripe the ticket in the list. |

> **Note:** A new hub starts with no statuses at all. Until you add some,
> the only choices on a ticket are the built-in **New** and **Closed**.

Adding statuses is safe at any time and takes effect immediately. Deleting
one is safe too, in the sense that no ticket is lost — but a ticket already
carrying it keeps pointing at a status that is no longer offered, so retire
a status by editing tickets off it first if the queue is large.

## Messages

Messages are canned responses that appear in the drop-down above the
comment box. They are the cheapest thing on this screen: three or four saved
replies for the questions you answer every week — how to reset a password,
why a tool session was reclaimed, where the storage quota is — turn a
five-minute reply into a ten-second one, and make the answers consistent
between staff.

Each has a **Summary** — the name shown in that list — and the **Message**
text. Four placeholders are substituted when the message is chosen:
`{ticket#}` and `#XXX` become the ticket number, `{sitename}` the site name,
and `{siteemail}` the site's from address.

A saved message fills the comment box; it does not send anything on its own.
You can edit the text before saving, which is the point.

## Abuse

Abuse reports are filed by members against comments and other content
across the hub — a spam post in a forum, an offensive comment on a
resource. They arrive here rather than in the ticket queue, and they need
watching for a different reason: a report sitting unread means content the
community has already flagged is still on the site.

The list shows **ID**, **Status**, **Reported Item**, **Reason**, **By**,
and **Date**, filtered by **Show**: *Outstanding*, *Released*, or
*Deleted*. Opening a report shows the reported content and who reported it,
with four choices:

- **Release item** — return the content to its normal state.
- **Remove as Spam** — take the content down and train the enabled
  antispam plugins on it.
- **Delete item** — take the content down, with an optional note.
- **Decide later** — leave the report alone.

Releasing or removing marks the report reviewed. Removal e-mails the
content's author, and — where banking is enabled — credits points to the
member who filed the report.

Prefer **Remove as Spam** to **Delete item** when the content really is
spam: it is the only one of the two that teaches the antispam plugins, so it
makes the next wave easier to catch. Use **Delete item** for content that is
unwanted but not spam, where training the filter on it would do harm.

The **Spam Check** link beside the list runs a block of sample text past
every enabled antispam plugin and reports what each one made of it. It
changes nothing, so it is safe to run on a live hub; use it when spam is
getting through and you want to know whether a filter is answering at all.

## Stats

Stats charts tickets opened against tickets closed by month, then breaks
the totals down: opened and closed all time, average ticket lifetime,
unassigned tickets, tickets by severity, and tickets by resolution. Below
that is a card for each person tickets are assigned to, ranked by how many
they have closed, with their own chart and average lifetime. **Show for
group** narrows everything to one group's tickets.

The number to watch is **unassigned tickets**. Opened-against-closed
drifting apart tells you the queue is growing; a rising unassigned count
tells you why.

## ACL

This screen decides who can work the queue, and on a new hub it is empty —
which is why nobody but a super administrator can see a ticket until you
fill it in. It is the first screen to visit and the one to come back to
whenever staff change.

The ACL grants support permissions to individual users and to groups.
Each row is a user or group with **Read**, **Update**, and **Delete** on
tickets, **Create** and **Read** on comments, and **Create** and **Read**
on private comments; click a cell to toggle it. To add a row, type a
username, user ID, or group name into **Alias or ID** at the foot of the
table, choose *user* or *group*, tick the permissions, and press **Add**.

Group permissions are inherited by every member, and where a user belongs
to several groups the highest permission wins. A permission set on the
user directly overrides everything the groups give them. Super
administrators pass every check.

The one to think about is **Read** on private comments. Private is where
staff write what they would not say to the member — a guess about the
cause, a note that this is the third time this person has broken the same
thing. Anyone who can read those can read all of them, on every ticket.

> **Note:** Whatever the ACL says, a member always has read access to a
> ticket they submitted, own, or were copied on in the most recent comment.

Changes here take effect on the next page load and are entirely reversible:
untick a cell and that person loses the permission. Nothing is deleted.

## Options

**Options** on the Tickets toolbar opens the component-wide settings:
the group to draw assignees from, whether replies can arrive by e-mail,
the addresses notified of a new ticket, whether e-mails carry ticket
details or only a bare notification, abuse-report notifications, the
attachment upload path, size limit and allowed extensions, and the IP
blacklist and bad-word list used to screen submissions. Every option is
listed with its values in the
[configuration reference](../../reference/configuration/components/support.md).

Four are worth knowing about before you touch them.

**Notify when ticket created** defaults to `{config.mailfrom}` — the hub's
own From address. That is a sensible fallback rather than a sensible
setting; change it to the mailbox your staff actually watch, and remember
that emptying it means nobody is told at all.

**Enable email interface** lets a member reply to the notification mail and
have the reply land on the ticket. The manifest's default is *No*, but the
row written at install time sets it to *Yes*, and the stored row is what the
hub runs with — so on a stock hub it is on. It is safe either way: the
feature needs `/etc/hubmail_gw.conf` on the server, and when that file is
absent the component quietly falls back to ordinary notifications. Setting
**Email Content** to *Terse* also switches it off, because a terse mail
carries no token to reply against.

**Email Content** set to *Terse* strips ticket detail out of every
notification, which is what a hub bound by FISMA or HIPAA rules wants.
When it is on, the **Terse** checkbox on the comment form is pre-checked;
staff can clear it for a single comment.

![The privacy setting on the Options screen](../media/support-supportprivacyemails.png)

**Max upload** and **Extensions** are blank in the manifest but are given
real values — 40 MB, and a long list of image, document, video and archive
types — by the row written at install time. Blank is not "no limit": with
the stored values cleared, the component falls back to the Media Manager's,
which are tighter — 10 MB, and a list with no archives and almost no video.
That is the setting to check when a member says their screenshot or log
bundle was refused.

> **Note:** The stored install row also sets a **severities** value of
> `critical, major, normal, minor, trivial`. Nothing reads it. The list of
> severities is fixed in code at Critical, High, Normal and Low, and the
> setting is passed to a function that ignores it, so *trivial* never
> appears on a ticket.

The **Permissions** tab carries the actions from `access.xml`: the
component-wide *Configure*, *Access Administration Interface*, *Create*,
*Delete*, *Edit*, *Edit State*, and *Edit Own*, plus a per-object set for
tickets (view, create, delete, edit, edit state, edit own) and for ticket
comments and private ticket comments (view and create). These are the
platform-wide access rules; the day-to-day permissions that decide who
answers tickets live in the [ACL](#acl) screen instead. If someone cannot
see the queue, check the ACL first — that is nearly always the reason.

## API

Support exposes a full REST API — tickets, comments, categories,
statuses, messages, stats, and a report of tickets that breach a
service-level criterion. See the
[API reference](../../reference/api/support.md).
