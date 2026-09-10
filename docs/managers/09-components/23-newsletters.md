<!--
status: rewritten
reviewed-against: 2.4-main @ d48e29db14
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/managers/components/newsletters
-->
# Newsletters

The newsletter component composes HTML or plain-text newsletters, mails them
to a list of addresses, and tracks what recipients do with them. Newsletters
you mark public are also readable on the site at `/newsletter`. This chapter
covers the administrator's side; the
[Hub users](../../users/17-newsletters.md) book covers reading and subscribing.

Open it under **Components > Newsletters**. Six sub-menu links run across the
top: **Newsletters**, **Mailings**, **Lists**, **Templates**, **Tools**, and
**Campaigns**.

> **Note:** Mail is not sent by the browser request that starts it. The
> component queues recipients and the `newsletter` cron plugin's
> **processMailings** job sends them in batches. If that job is missing or
> disabled, the Newsletters screen shows a warning and a link to the Cron
> component; nothing you send will ever leave the hub.

## Newsletters

The list shows **Name**, **Format**, **Template**, **Public**, **Sent**, and
**Tracking**. Click a heading to sort. Filter by a search term matched
against the name, and by format with the **- Select Type -** menu (HTML or
Plain). The **Public** cell is a toggle: click it to publish or unpublish.

The toolbar offers **New**, **Copy** (duplicates the checked newsletters and
their stories, appending "(copy)" to the name), **Edit**, **Delete**,
**Publish**, **Unpublish**, **Preview** (renders the newsletter in a
lightbox), **Send Test**, **Send**, and **Options**.

Deleting is a soft delete: the record is flagged and disappears from every
screen, but is not removed from the database.

## Creating or editing a newsletter

| Field | Notes |
|---|---|
| Name | Required. Also used as the subject line of the mailing. |
| Alias | The last segment of the newsletter's URL, for example `january2013update`. |
| Issue | Free text, substituted into the template as `{{ISSUE}}`. |
| Format | **HTML** or **Plain Text**. An HTML newsletter is also sent with a generated plain-text part. |
| Template | A saved template, or **No Template (If HTML format, content includes template)** to write the whole message yourself. |
| Show Newsletter on HUB | **Show** or **Don't Show**. Hidden newsletters are not listed at `/newsletter`. |
| Email Tracking | Yes or No. See [Mailings](#mailings). |
| Scheduled Digest | Disabled, Daily, Weekly, or Monthly. Turns the newsletter into an auto-generated digest. |

Save once before adding content — until the newsletter has an ID, the form
says so and offers nothing else. After saving, a **Mailing Details** panel
appears with **From Name**, **From Email**, **Reply-To Name**, and
**Reply-To Email**. Each falls back to the component-wide option of the same
purpose, and that falls back to the site name and the request host.

### Stories

With a template selected, the lower half of the form holds
**Newsletter Primary Stories** and **Newsletter Secondary Stories**, which the
template drops in at `{{PRIMARY_STORIES}}` and `{{SECONDARY_STORIES}}`. Use
**Add Primary Story** or **Add Secondary Story**; each story has a **Title**,
a read-only **Order**, a **Story** body (line breaks become `<br />`), and a
**Read More Link** with its own link text and URL. Reorder a story with the
**Move Up** and **Move Down** links beside it.

With **Scheduled Digest** set to Daily, Weekly, or Monthly, the secondary
stories section disappears and the primary one becomes **Auto-Generated
Stories**. Such a story asks for a **Data Source**, an **Item Count**
(capped at 20), and a **Story Layout Template**, and is stored as a
placeholder that is expanded with current content each time the digest is
built. Sources come from enabled plugins in the `newsletter` group; the
shipped ones are `event`, `resource`, and `feedaggregator`.

Choosing **No Template** replaces both story sections with plain
**HTML Content** and **Plain Text Content** text areas.

## Sending

Check one newsletter and press **Send Test** to mail it to up to five
comma-separated addresses. Test messages go out immediately, with
`[SENDING TEST] - ` in front of the subject; anything past the fifth address
is reported as failed.

**Send** opens the distribution screen. It shows any previous mailings with
their state (Scheduled, In Progress, or Sent), a **Schedule** choice of
**Send Now** or **Send Later** with a date, hour, minute and AM/PM, and a
**Mailing List** menu. That menu lists your public and private lists plus
**Default List**, which means every hub member whose email preference is set,
who is not blocked, is approved and activated, together with the guest
sign-up list. The count of recipients appears under the menu as you choose.

Pressing **Send** in the toolbar creates the mailing, queues one recipient
row per address in batches of 10,000, and marks the newsletter as sent. The
cron job takes it from there.

## Mailings

The Mailings screen lists every mailing by newsletter name, **Date
Sent/Scheduled**, **% Complete** (sent versus queued), and **Re-occur**
(the newsletter's digest frequency, or N/A). Search matches the mailing
subject.

**Stop/Cancel** flags the checked mailing as deleted, which removes its
remaining queued messages from processing and cancels a scheduled send.

**Stats** opens the tracking report: **Open Rate**, **Bounce Rate**,
**Forwards**, **Prints**, **Opens By Location** with a table of top countries
and clickable world and US maps, and **Click Throughs** listing each URL and
its click count. Tracking works by embedding a one-pixel image and rewriting
links through the hub; if the newsletter had **Email Tracking** set to No,
the screen refuses to open and says so.

## Lists

Mailing lists hold addresses that need not belong to hub members. The list
shows **Name**, **Public/Private**, **Active Subscribers**, and **Total
Subscribers**. A list has only a **Name**, a **Public/Private** setting, and
a **Description**; public lists appear on the site for anyone to join,
private ones do not.

**Manage** opens one list's addresses, filtered by **Status**: All Statuses,
Active, Removed by Admin, Unsubscribed, or Inactive (Awaiting Confirmation).
Columns are **Email**, **Status**, **Confirmed?**, **Date Added**, and
**Date Confirmed**; an unconfirmed row carries a link that resends the
confirmation email, and an unsubscribed row shows the reason the person gave.
**Remove** marks the checked addresses as removed rather than deleting them.

**Add Emails** takes addresses three ways at once, and merges them:
an uploaded `txt`, `csv`, `xls` or `xlsx` file, a hub group whose members'
addresses are imported, and a text area you paste into. The parser accepts
one address per line, comma-separated, tab-separated, or
`"Name" <address>` form. Invalid and duplicate addresses are reported and
skipped. **Confirmation Email** decides whether each new address is asked to
confirm.

**Export List** downloads the active addresses of the checked list as a CSV
of email and status.

## Templates

A template is the HTML frame a newsletter is poured into. The list shows
template names; templates shipped with the hub are marked not editable and
not deletable, so **Copy** one to start your own.

| Field | Notes |
|---|---|
| Name | Required. |
| Primary Stories: Title Styles, Text Styles | Inline CSS applied to primary story titles and bodies, for example `font-family:arial;font-size:12px;color:#FF0000;` |
| Secondary Stories: Title Styles, Text Styles | The same for secondary stories. |
| Template | The HTML of the message. |

The placeholders the builder replaces are `{{LINK}}` (a link to the hub),
`{{ALIAS}}`, `{{TITLE}}`, `{{ISSUE}}`, `{{PRIMARY_STORIES}}`,
`{{SECONDARY_STORIES}}`, `{{COPYRIGHT}}` (the current year), and
`{{UNSUBSCRIBE_LINK}}`, which becomes a one-click unsubscribe URL unique to
each recipient and mailing list. Two links beside the editor point at
whatever guides and example galleries the Options name.

## Tools

The Tools screen holds one utility, **Mozify Image**: upload an image or give
its URL, pick a mosaic size, and get back an HTML table that approximates the
image. Mail clients that block images still show something.

## Campaigns

Campaigns generate and hold a 32-character secret used to build signed URLs
that let a person reach a hub feature from an email without logging in. They
live under Newsletters but are otherwise unrelated to newsletters and
mailings. A campaign has a **Campaign Name**, an **Expiration Date**
(90 days out by default), and a **Campaign Description**; the secret is never
displayed. Editing offers **Reset Campaign Secret**, which invalidates every
URL already built from that campaign. Deleting one does the same.

## Options and permissions

The **Options** button opens the component's settings: the default from and
reply-to names and addresses for mailings, and the URLs used for the email
tracking explanation and for the template guide links. Every option is listed
in the
[configuration reference](../../reference/configuration/components/newsletter.md).

The **Permissions** tab sets, per user group, who may configure the component
(`core.admin`), reach the administrator screens (`core.manage`), and create,
delete, edit, change the state of, or edit their own newsletters. There are
no per-list or per-template permissions.

## API

Three read-only endpoints return newsletters as JSON:
`GET /api/newsletters/current`, `/api/newsletters/list`, and
`/api/newsletters/archive`. See the
[API reference](../../reference/api/newsletter.md).
