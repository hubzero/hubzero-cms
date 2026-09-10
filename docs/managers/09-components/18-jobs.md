<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
-->
# Jobs

The Jobs component is a job board. Employers post openings, members apply to
them with a résumé held on their hub profile, and an administrator approves,
unpublishes and deletes the postings. It is installed and enabled on every
hub, but nothing links to it: there is no menu item, no module and no button
anywhere on the front end that reaches `/jobs`. A hub that has never been told
to run a job board is running one that nobody can find.

So the first decision is whether you want it at all. If you do, add a menu
item; if you do not, leave it alone — it costs nothing and creates nothing on
its own. The one setting that has an effect either way is **Enable this
component?**, which controls whether members see a **Résumé** tab on their
profile.

The realistic case for turning it on is a hub whose community is also a
labour market — a domain hub where the groups posting research are also the
groups hiring postdocs. A hub of a hundred members whose institution already
has a jobs page does not need a second one.

Whichever way you go, decide it in one sitting, because the board comes in
two quite different shapes and the choice pulls in another component:

- **A noticeboard.** Leave **Allow user subscriptions?** off. You and the
  **Admin group** post the openings; no member becomes an employer, no money
  changes hands, and [Services](32-services.md) stays empty. This is the
  simple version and most hubs that run a board at all want this one.
- **An employer marketplace.** Turn **Allow user subscriptions?** on.
  Members can now become employers, which means taking out a paid
  subscription, which means you are also running
  [Services](32-services.md) — the component that holds those subscriptions
  and the only thing that reads it. It also opens résumé browsing and bulk
  résumé download to those employers. Read the warnings under
  [For an employer](#for-an-employer) and in
  [Services](32-services.md) before you choose this.

Neither shape has anything to do with [Storefront](33-storefront.md) or
[Cart](06-cart.md); the job board does not use the hub's store.

## The administrator screens

**Components → Jobs** opens the job board manager. Every screen is headed
**Jobs Manager**, and three tabs run along the top: **Jobs**, **Categories**
and **Types**. Reaching any of them needs `core.manage` on `com_jobs`.

### Jobs

The list of postings. Its only control is a **Search** box with a **Go**
button, matching words in the title and the description. The columns are
**Code**, **Title**, **Company & Location**, **Status**, **Owner**,
**Added**, **Expires** and **Applications**; all but Code, Expires and
Applications sort. Hovering a title shows the created date, the creator, the
category and the type.

**Code** is the eight-digit identifier the posting is addressed by on the
front end, at `/jobs/job/<code>`. It is generated on the first save and never
changes. **Owner** shows `(admin)` for a posting the hub itself made rather
than an employer.

**Status** is one of six:

| Status | Meaning |
|---|---|
| **Draft** | Saved but never submitted. New administrator postings start here |
| **Pending approval** | Submitted by an employer, waiting for you |
| **Active** | Published and visible |
| **Expired/Invalid Subscription** | Published, but past its expiry date or its employer's subscription has lapsed |
| **Inactive** | Unpublished |
| **Deleted** | Withdrawn. The row stays in the database |

The toolbar has **New**, **Edit**, **Delete** and **Options**.

> **Warning:** **Delete** asks nothing and deletes the rows from the database.
> It is not the same as the **Delete Ad** action on the posting form, which
> sets the status to **Deleted** and keeps the record and its applications.
> Use the form unless you mean to erase the posting.

### The posting form

**New** and **Edit** open the same three-part form.

**Company** — **Name** and **Location** are required; **URL** is not.
**Location** wants "City, State".

**Job** — **Category** and **Type** come from the two other screens.
**Country** is a fixed *United States* when **US jobs only?** is on, and a
country list when it is off. **Title** is required. **Description** opens in
the hub's configured editor and is marked *Wiki formatting is enabled*.
**Position Start Date**, **Applications Due** and **Posting
Expires** are dates. **External URL for a job application** sends applicants
off-site; **Allow internal application** lets them apply through the hub
instead. Set one, the other, or both.

**Contact Information** — name, email and telephone, all optional.

The right-hand column shows the record's history and, for a saved posting, a
**Manage this Job** panel:

- **Change Status / Take Action** — one of **No action / Send message to
  author**, **Publish Ad** or **Unpublish Ad**, and **Delete Ad**.
- **Message to author** — free text, sent to the member who created the
  posting.

Choosing any action, or typing a message with **No action** selected, sends
the author a hub message with the status change and a link to the posting.
Selecting nothing and saving changes the posting silently.

> **Note:** A new posting created here has no action panel. It is saved as a
> **Draft** first — the form says *This is a new job ad. Please save it as
> draft before admin option become available* — and you publish it on the
> second visit.

Publishing checks the employer's quota and refuses if they are over it, with
*Failed to publish this ad because user is over the limit according to the
terms of his/her subscription*. Postings the hub itself owns are allowed one
active advertisement.

### Approving a posting that is waiting

With **Auto approve job postings** off, an employer's posting sits at
**Pending approval** until you look at it. That is the routine task on this
component.

1. **Components → Jobs**. The list opens on **Jobs**; look down the
   **Status** column for **Pending approval**. There is no status filter, so
   on a busy board sort by **Status** to bring them together.
2. Click the title to open the posting. Read the description and check the
   dates — **Applications Due** and **Posting Expires** are what the front
   end enforces, and an expiry already in the past publishes to nothing.
3. If it needs changing, change it. You are editing the employer's advert,
   and they are not told what you altered.
4. In **Manage this Job**, set **Change Status / Take Action** to **Publish
   Ad**. Add a line in **Message to author** if you want them to know why —
   whatever you type is mailed to them along with the status change.
5. Save. If the employer is over their subscription quota the publish is
   refused with the message above; the fix is in
   [Services](32-services.md), on their subscription, not here.

To turn a posting down, use **Unpublish Ad** with a message, or **Delete
Ad** if it should never have been submitted. Both keep the record. Do not
use the toolbar's **Delete** for this — see the warning above.

### Categories

The category list, with **ID**, **Order** and **Title**. Each row's **Order**
is an editable box; **Save Order** writes them all. The form has **Title**
and **Description**. Nothing seeds this list, so it is empty on a new hub and
every posting is filed under *No specific category* until you add some.

> **Warning:** The form says it plainly — *changing the category title will
> affect all currently available job postings in this category*. Postings
> store the category by ID, so a rename retitles every posting already in it.

### Types

The same screen with one field. A type is a title and nothing else — the
list shows **ID** and **Title** only. This is where "Full-time",
"Part-time", "Contract", "Internship" and "Temporary" would go; nothing
creates them for you.

There is no screen for **employers**. An employer record is created on the
front end when a member subscribes, and the hub itself is employer number 1,
created automatically the first time an administrator opens the dashboard.

## Options

Select **Options** on any of the three screens. The full parameter list is in
the [generated reference](../../reference/configuration/components/jobs.md).

- **Enable this component?** — despite the description, this does not turn the
  job board off. `/jobs` and the administrator screens stay exactly as they
  are. What it controls is the **Résumé** tab on member profiles: with the
  setting off, the Members - Resume plugin replaces the tab's contents with a
  notice. Treat it as *offer résumés or not*.
- **Industry name** — appended to every page title and breadcrumb. Setting it
  to `Nanotechnology` makes the pages read *Jobs in Nanotechnology*.
- **Admin group** — members of this hub group get the same rights over
  postings and résumés as a site administrator. It is the way to give the
  board a moderator who is not an administrator.
- **Special services group** — members of this group are offered subscription
  services that are otherwise restricted.
- **Auto approve job postings** — with this on, a posting from an employer
  with a valid subscription goes straight to **Active**. With it off it lands
  in **Pending approval** and waits for you. Postings an administrator
  publishes are never held.
- **Limit jobs per page** — the default page size on the front end.
- **Allow user subscriptions?** — see below. This changes the shape of the
  whole front end.
- **US jobs only?** — replaces the country list on the posting form with a
  fixed *United States*.
- **Posting Expiration Date** — **On - 180 day expiration** puts a date 180
  days out in **Posting Expires** on a new posting, which the poster can
  change. **Disabled** removes the field, and postings never expire. Either
  way the form refuses an expiry more than a year away.

Three more settings have language strings but no field on the Options screen,
so they cannot be set from the interface: **Promo for employers**, **URL to
about page** and **URL to premium services page**. The per-subscription
advertisement limit, `maxads`, is likewise not on this screen — it is a
parameter on the service record in [Services](32-services.md), and falls back
to 3.

## The front end

`/jobs` is reachable as soon as the component is enabled, with or without a
menu item. **Allow user subscriptions?** decides what it is.

**With subscriptions on** (the default), `/jobs` is a landing page split
between **Employers** — browse résumés, post a job — and **Seekers** — browse
jobs, post a résumé — followed by the latest postings. The job list proper is
at `/jobs/browse`.

**With subscriptions off**, `/jobs` is the job list itself. No member can
become an employer, so the subscription flow, the employer dashboard and the
résumé browser are all closed to them, and the board becomes a noticeboard
that only administrators and the admin group post to. Those two still reach
the dashboard and the résumé browser.

### For a visitor

The job list has a keyword search and paging. Selecting a posting opens
`/jobs/job/<code>` with the full description, the company, the dates and, if
the poster allowed it, an **Apply** button. Applying needs an account.
An application is a covering note plus the résumé already on the member's
profile; a member can edit or withdraw it afterwards. Guests can read active
postings but see nothing else.

### For a member

A member's résumé is not part of this component's front end. It lives on the
**Résumé** tab of their profile, served by the Members - Resume plugin;
`/jobs/addresume` is only a redirect there. That tab is where they upload the
file, and it is what employers see and download.

### For an employer

Posting a job means becoming an employer, and becoming an employer means
taking out a subscription. The first visit to `/jobs/subscribe` creates two
subscription services if none exist — *Employer Service, Basic*, free, one
advertisement, and *Employer Service, Premium*, priced, three advertisements
and unpublished. You then edit those, activate the subscriptions and record
the payments, in [Services](32-services.md). Subscriptions can be paid with
the hub's points, so a member's balance is shown on the form.

Once subscribed, `/jobs/dashboard` gives an employer their postings, their
quota, their subscription, and the applications they have received.
`/jobs/resumes` lets them browse and shortlist members who have posted a
résumé, filtered by category and type, and download résumés singly or in a
batch. Administrators and the admin group reach both without a subscription.

> **Warning:** Résumé browsing hands an employer the files members uploaded to
> their profiles, and the batch download hands them a zip of many at once.
> Turning **Allow user subscriptions?** on is what opens that door. Decide
> deliberately who should be able to walk through it, and use **Admin group**
> rather than site administrator accounts for the people who moderate.

## The API

`GET /api/jobs/list` returns the job list with `limit`, `start`, `search`,
`sort` and `sort_Dir`. See the [API reference](../../reference/api/jobs.md).

## What does not work

- **The front-end search box disappears when the list is empty.** It is inside
  the block that renders only when there is at least one posting, so a search
  that matches nothing leaves no way to search again. Editing the URL or
  going back is the only route out.
- **There are no category or type filters on the job list.** The template
  builds the option lists for a sort menu and a status filter and then never
  renders either. The saved search preferences behind them work; only the
  controls are missing.
- **`/jobs/editresume/<id>` goes nowhere.** The router recognises the path but
  the controller has no such task, so it falls through to the job list.
- **The Creator and Modifier fields show a number.** The administrator list's
  tooltip and the posting form both print the raw user ID rather than a name.
- One view template, `introemp`, is a three-step explanation of how to become
  an employer. Nothing renders it.
