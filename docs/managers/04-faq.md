<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/fqas
source-id: 3345
modified: 2014-11-07
imported: 2026-09-09
-->
# Frequently asked questions

Short answers to things hub managers ask, each checked against Hubzero 2.4,
and ordered roughly by how often the question comes up rather than by
component. Every answer points at the chapter that covers the screen
properly.

## I made a menu and it does not appear on the site. Why?

Because a menu and the thing that displays it are two separate objects, and
creating one does not create the other. A menu is a list of links with no
position and no presence on any page; a **Menu** module is what puts it
somewhere.

Go to **Menus → Menu Manager** and look at your menu's row. If the **Modules
Linked to the Menu** cell offers **Add a module for this menu type**, there is
no module — follow that link and make one. If a module is named there, open
it and check two things: its **Status** is Published, and its **Position** is
one the current template actually declares. The **Modules Linked to the Menu**
column lists modules whether or not they are published, so seeing one there
proves nothing on its own.

A plain install is the common case of the second problem: the shipped **Main
Menu** module sits in `position-7`, which neither site template in this tree
declares, so it renders nowhere. See [Menus](07-menus.md) and
[Modules](10-extensions/01-modules.md).

## How do we email everybody in a group?

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

## How do I point one URL at another?

Use the **Redirect Manager** at **Site → Maintenance → Routes**: a **Source
URL**, a **Destination URL**, and a response code. It also records every 404
the site serves, so it doubles as a list of the addresses people are asking
for and not finding — which is usually where you find out a link is broken in
the first place.

A menu item of type **External URL** does the same job for a path that
belongs to a component rather than to an article — redirecting
`/groups/mainclass` to `/groups/spring2016class`, say. Both are covered in
[URLs](08-content/urls.md#redirects).

## How do I set an option for one article rather than the whole site?

Open the article at **Content → Article Manager** and use the **Article
Options** panel down the right-hand side. It repeats every option from the
component's **Articles** settings — **Show Title**, **Show Author**, **Show
Hits**, and the rest — and every one of them defaults to **Use Global**.

**Use Global** means "look one level up". On a single-article page the order
is article, then menu item, then the component's global setting. Blog and
featured layouts reverse the first two: the menu item wins, and the article's
value is used only where the menu item's setting is **Use Article**.

See [Article Manager](08-content/articlemanager.md#the-editor).

## Why does the **Featured** switch do nothing?

Because it writes to a column the site does not read. The site's **Featured
Articles** page reads a separate table that nothing in the administrator
interface writes to, and the administrator's own Featured Articles screen is
unreachable. This is a defect and is recorded with the project.

It matters more than it sounds, because the home page a plain install ships
is a Featured Articles item. A hub left on it has an empty front page and no
way to fill it from any screen. Point the home item at a **Single Article**
instead — [Menus](07-menus.md#example-navigation-for-a-new-hub) walks through
it.

## Can one article be in two categories?

No. An article belongs to exactly one category. If a page has to appear in
two places, point two menu items at the same article rather than making a
second copy of it. See
[Categories](08-content/categories.md).

## A member says their account is "temporarily disabled". What do I do?

Nothing, usually. That message means the account has failed to log in too
many times inside the window set at **Users → Members → Options → Login
Settings** — ten attempts in an hour on the shipped settings. The block is
not stored on the account: it is a count of recent rows in the authentication
log, so it lifts itself as the window slides forward. Tell the member to wait
and try again, and to use **Forgot your password** rather than guessing.

If it is happening to many accounts at once, or repeatedly to one, read
[Security considerations](security.md#cms-controlled-fail2ban-jail), which
explains what is being counted and what the three thresholds do.

## A member is stuck on a "spam detected" page. How do I release them?

Their lifetime spam counter is over the limit. Go to **Users → Members**,
open the member, and on the **Account** tab select **Reset** beside
**Lifetime Spam Incidents**. That field only appears when **System -
Spamjail** is enabled, which is also the only reason the page exists. The
whole procedure, including the per-session counter that clears itself, is in
[Spam](11-spam.md#releasing-a-member).

## How do I allow another attachment type on support tickets?

1. Go to **Components → Support**.
2. On **Support: Tickets**, select **Options**.
3. Open the **Files** tab.
4. Add the extension to **Extensions**.
5. **Save & Close**.

Think about what you are allowing. Every attachment is scanned by the
command in the `virus_scanner` configuration key before it is accepted, but a
scanner is not a substitute for keeping executables and archives off the
list. See [Security considerations](security.md#does-the-cms-scan-uploads-for-viruses).

## How does a project connect to Google Drive?

A project manager creates the connection and authorizes it with their own
Google account; everyone else in the project works through it.

1. Open the project and select the **Files** tab.
2. Choose **Google Drive** from the **New Connection** drop-down, give the
   connection a **Name**, and save.
3. Authorize it. Sign in to Google and accept the permissions the hub asks
   for.
4. Google returns you to the project, which now shows the connection beside
   the project's own file repository.

The same tab carries **Edit**, **Delete**, **Refresh Connection Credentials**
and **Refresh Connection Path**. If a connection stops working for the whole
project, **Refresh Connection Credentials** is usually the fix: the
authorization belongs to one person's account, and it fails when that person
revokes it or leaves.

The provider does not work until an administrator has enabled the plugin and
supplied credentials — see
[Integrations](02-advancedsetup.md#external-file-storage-in-projects). Some
hubs still run the older project-wide Google connection instead, which puts a
**Connect** link on the same tab; that one is covered there too.

## How do I change the status of a wish?

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

## How do I see the outline a student sees?

Enrol in your own course as a student, from a test account. An instructor or
course manager sees the whole outline, published and unpublished, and sees it
regardless of prerequisites. A student sees only what the prerequisites and
the availability dates have released to them, so the instructor's view is not
a preview of the student's.

## What was removed from this page

The imported version of this page had one further answer, a second
step-by-step for redirecting `/groups/mainclass` to another group, duplicating
the one above it. Both have been replaced by the single entry under
[How do I point one URL at another?](#how-do-i-point-one-url-at-another).
Nothing else was dropped; the remaining answers were corrected rather than
removed — most visibly the group message answer, which described an email
icon on the **Members** tab as the only way in, and the wishlist answer, which
described a drop-down where the interface has radio buttons.
