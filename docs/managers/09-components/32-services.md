<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
-->
# Services

The Services component exists to serve one other component. It holds the
paid subscriptions that let an employer use the hub's job board: what an
employer service costs, how long it runs for, and where each employer's
subscription stands. Nothing else on the hub reads it.

It has no site pages of its own. Employers buy and renew a subscription from
the job board's own screens; this component is where an administrator sees
the result, takes a payment that arrived some other way, and switches a
subscription on.

It is unrelated to [Storefront](33-storefront.md) and [Cart](06-cart.md),
which are the hub's actual shop. Services predates them and shares no code,
no tables, and no checkout. If a hub does not run a job board, this component
has nothing in it.

## The screens

**Components → Services**, gated on `core.manage` for `com_services`. Two
sub-navigation entries.

### Services

The catalogue of things an employer can subscribe to. The list shows the ID,
**Title**, **Category**, and **Status** — **Active** or **Inactive**.
Toolbar: **Options**, **New**, **Edit**, **Delete**.

The edit form:

| Field | Meaning |
|---|---|
| **Category** | Required. The job board asks for services in the `jobs` category, so anything else is invisible to it. |
| **Title** | Required. Shown to the employer. |
| **Alias** | Letters, numbers, dashes and underscores. Must be unique. The job board looks services up by alias. |
| **Description** | Shown to the employer. |
| **Currency** | A symbol, printed in front of prices. |
| **Price** | The price of one unit. |
| **Points** | The price of one unit in hub points, if the hub lets employers pay that way. |
| **Minimum # of units** and **Maximum # of units** | How many units an employer may buy at once. |
| **Size** and **Measure** | What a unit is — `1` and `month` in the services the job board creates. |
| **Parameters** | Newline-separated `key=value` pairs. The job board reads `maxads` (how many adverts the subscription allows), `promo` (promotional text), and `promomaxunits` (how many units the promotion covers). |
| **Restricted** | Marks the service as reserved for members of the job board's **Special services group**. See the warning below. |
| **Status** | **Published**, **Unpublished**, or **Trashed**. Only a published service is offered. |

> **Warning:** **Restricted** does not work as its description promises. A
> restricted service is hidden from everyone unless the job board's **Special
> services group** is set, and when it is set the service is offered to
> members who are *not* in that group rather than to members who are.
> Recorded with the project. Leave **Restricted** clear.

The first time an employer reaches the job board's subscription screen and
finds no `jobs` services at all, the job board creates two for you:
*Employer Service, Basic* (free, one advert, first three months promotional)
and *Employer Service, Premium* (unpublished, $500 a month, three adverts).
Edit those rather than starting from nothing.

### Subscriptions

One row per employer subscription. **Filter by** offers **Pending**,
**Active**, **Cancelled**, and **All**. The list shows the ID and code, the
status, the service, the pending payment and units, the member, and the
added, updated and expiry dates.

> **Note:** The **Active** filter does not filter. The controller understands
> **Pending** and **Cancelled**; anything else, **Active** included, falls
> through to showing everything. This is recorded in
> It is recorded with the project.
Opening a subscription shows the service and its price, the member's profile
and employer details, the amounts paid and pending, and an **Administrator
Notes** box. Below that, **Manage Subscription** offers one action at a time:

| Action | What it does |
|---|---|
| **No action / send message to user** | Records the note and mails the member. Shown as **Subscription on hold** for a subscription that is not cancelled. |
| **Activate / Extend this subscription** | Takes a **New payment received** amount and a number of units, adds the payment to the total paid, extends the expiry date by units × unit size months, and sets the status to active. |
| **Cancel this subscription** | Sets the status to cancelled and clears the pending amounts. See the warning below. |
| **Process refund / remove pending items** | Only offered on a cancelled subscription that still shows a pending payment. Records the refund as posted and clears the pending amounts. |

Anything typed into **Send user a message**, and the status change itself,
are appended to the notes with a timestamp and mailed to the member.

> **Warning:** **Cancel this subscription** never computes a refund. The
> calculation uses two variables that are never assigned, so the result is
> always zero: the subscription is cancelled showing nothing owed, and
> because the pending payment is zeroed at the same time, **Process refund**
> never appears afterwards. The job board's own cancel screen has the same
> fault. Work refunds out by hand. Recorded in
> It is recorded with the project.
## Options

One setting, plus the usual **Permissions** tab. Both are listed in the
[generated reference](../../reference/configuration/components/services.md).

**Auto-approve employer subscriptions** decides what happens when an employer
subscribes to a service that costs nothing — a free tier, or a paid one
inside its promotional period. With it on, the subscription activates
immediately. With it off, it is created as pending and waits for someone to
open it here and press **Activate / Extend**.

The setting is read by the job board, not by this component.
