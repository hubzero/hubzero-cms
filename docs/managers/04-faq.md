<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/fqas
source-id: 3345
modified: 2014-11-07
imported: 2026-09-09
-->
# Frequently asked questions

Short answers to things hub managers ask, each checked against Hubzero 2.4.
Every answer points at the chapter that covers the screen properly.

## Articles

### How do I set an option for one article rather than the whole site?

Open the article at **Content → Article Manager** and use the **Article
Options** panel down the right-hand side. It repeats every option from the
component's **Articles** settings — **Show Title**, **Show Author**, **Show
Hits**, and the rest — and every one of them defaults to **Use Global**.

**Use Global** means "look one level up". On a single-article page the order
is article, then menu item, then the component's global setting. Blog and
featured layouts reverse the first two: the menu item wins, and the article's
value is used only where the menu item's setting is **Use Article**.

See [Article Manager](08-content/articlemanager.md#the-editor).

### Can one article be in two categories?

No. An article belongs to exactly one category. If a page has to appear in
two places, point two menu items at the same article rather than making a
second copy of it. See
[Categories](08-content/articlemanager.md#categories).

### Why does the **Featured** switch do nothing?

Because it writes to a column the site does not read. The site's **Featured
Articles** page reads a separate table that nothing in the administrator
interface writes to, and the administrator's own Featured Articles screen is
unreachable. This is a defect and is recorded with the project.

## URLs and menus

### How do I point one URL at another?

Use the **Redirect Manager** at **Site → Maintenance → Routes**: a **Source
URL**, a **Destination URL**, and a response code. It also records every 404
the site serves, so it doubles as a list of the addresses people are asking
for and not finding.

A menu item of type **External URL** does the same job for a path that
belongs to a component rather than to an article — redirecting
`/groups/mainclass` to `/groups/spring2016class`, say. Both are covered in
[URLs](08-content/urls.md#redirects).

## Groups

### How do we email everybody in a group?

Through the group's **Messages** tab, which is the **Groups - Messages**
plugin. Only a group manager or a site administrator can compose one.

1. Open the group and select **Messages**, then **Send New Message**.
2. Pick the recipients: **All Group Members**, **All Group Managers**, **All
   Group Invitees**, **All Group Applicants**, one of the group's member
   roles, or one named member.
3. Fill in the subject and the message, and select **Send**.

The group's **Members** tab has a **Message** link beside each member and
each role that opens the same form with the recipient already chosen. Sent
messages stay on the **Messages** tab.

## Projects

### How does a project connect to Google Drive?

The person who created the project has to connect their own Google account
before anyone else can connect theirs.

1. Open the project and select the **Files** tab.
2. Select **Connect** against Google, then **Connect** again.
3. Sign in to Google and accept the permissions the hub asks for.
4. Google returns you to the project, which now shows the connection and how
   many of the team have connected.

The same tab disconnects and re-authorizes a connection. The service does not
appear at all until an administrator has turned it on and supplied
credentials — see [Integrations](02-advancedsetup.md#google-drive-in-projects).

## Support

### How do I allow another attachment type on support tickets?

1. Go to **Components → Support**.
2. On **Support: Tickets**, select **Options**.
3. Open the **Files** tab.
4. Add the extension to **Extensions**.
5. **Save & Close**.

## Wishlist

### How do I change the status of a wish?

Open the wish on the site and select **Change status** beneath it. The
statuses are radio buttons, not a drop-down:

| Status | Meaning |
|---|---|
| **Pending** | Awaiting a decision from the list owners. |
| **Accepted** | The list owners have agreed to implement it. |
| **Rejected** | The list owners have declined it. |
| **Granted** | It is done and available on the hub. |

Choose one and select **change status**. The member who made the wish is
told. A wish on a resource wish list can only be marked **Granted** by the
person it is assigned to.

## Courses

### How do I see the outline a student sees?

Enrol in your own course as a student, from a test account. An instructor or
course manager sees the whole outline, published and unpublished, and sees it
regardless of prerequisites. A student sees only what the prerequisites and
the availability dates have released to them, so the instructor's view is not
a preview of the student's.

## What was removed from this page

The imported version of this page had one further answer, a second
step-by-step for redirecting `/groups/mainclass` to another group, duplicating
the one above it. Both have been replaced by the single entry under
[URLs and menus](#urls-and-menus). Nothing else was dropped; the remaining
answers were corrected rather than removed — most visibly the group message
answer, which described an email icon on the **Members** tab as the only way
in, and the wishlist answer, which described a drop-down where the interface
has radio buttons.
