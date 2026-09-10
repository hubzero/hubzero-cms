<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/search/admin
source-id: 3392
modified: 2019-09-11
-->
# Administration

**Components > Search** has four tabs when the engine is Solr: **Overview**,
**Searchable Components**, **Index Blacklist**, and **Boosts**. Every tab
carries an **Options** button that opens the same component configuration.

You come here for one of three reasons: search is missing something and you
want to know whether it is in the index, a new content type needs indexing,
or the whole index needs rebuilding after a bulk import. Nothing on these
screens has to be visited on a routine. A Solr hub that is behaving needs no
attention here from one month to the next.

> **Note:** These screens are not the search settings. Everything that
> changes how a query behaves — the engine, the Solr connection, the field
> weights, the batch size, the commit window, tag search — is behind the
> **Options** button, listed in the
> [configuration reference](../../../reference/configuration/components/search.md).
> These four tabs only manage the contents of the index.

## Overview

*Solr Search: Overview* is the landing screen. It reports:

- **Last Document Insert** — how long ago Solr accepted a document, as a
  relative time.
- **Mechanism** — the engine the hub is configured to use.
- Whether the engine answered: *The search engine is responding*, or
  *The search engine is not responding* with the advice to check the
  component configuration and the `hubzero-solr` service.

The toolbar has one action beyond **Options**: **Optimize Index**, which asks
Solr to defragment its index and reports success or failure. It is safe to
run at any time and is not needed on a schedule.

**Last Document Insert** is the number to read. On a working hub it moves
whenever anyone saves anything. If it reads *3 months ago* on a busy hub, the
index has stopped being updated — go to the **Search - Solr** plugin first,
as described in [Maintaining the index](05-index.md).

> **Note:** The status panel calls Solr on every page load. If Solr is down,
> the screen still renders, with the failure message in place of the check.

## Searchable Components

*Solr Search: Components* lists every content type the hub knows how to
index, with its **ID**, **Title**, **Active?** state, and the number of
**Records** currently in Solr. The record count is a link into the document
listing for that type.

Toolbar actions:

| Button | What it does |
|---|---|
| **New** | Adds a component row by hand. Rarely needed; use **Discover** instead. |
| **Delete component(s) results** | Removes every document of the checked components from Solr and returns them to **Not Indexed**. The rows stay. |
| **Remove component(s)** | Trashes the checked component rows, so they no longer appear in this list or in the site's category sidebar. The documents stay in Solr. |
| **Discover Searchable Components** | Scans the component directories for models that implement `Hubzero\Search\Searchable` and adds a row for each one not already listed. |

In the list itself, the **Active?** icon toggles the component between
**Not Indexed** and **Indexed** — selecting it on a **Not Indexed** row starts
the batch index. An **Indexed** row also carries a **Rebuild Index** button
that runs the same batch pass again over existing documents.

Of the four toolbar buttons, only **Discover Searchable Components** is
harmless. **Delete component(s) results** and **Remove component(s)** both
take a content type out of site search the moment you press them, with no
confirmation, and both are recoverable only by indexing that type again from
scratch — which on a large hub is an hour's work, not a click. Read the two
rows above carefully before ticking anything; they sound alike and do
different things.

Selecting a component's title opens *Solr Search: Edit Searchable Component*,
which has a **Title** (the label used in the site's category list), a
**Custom Query** that replaces the default `hubtype:<name>` when this type's
documents need a different Solr query, and a **Filters** builder for the
facet controls shown beside that type's results.

## Index Blacklist

*Solr Search: Index Blacklist* lists the documents struck from the index,
who struck them, and when, each with a **Remove entry** button. It reads
*There are no entries on the blacklist* when empty. See
[Blacklist](07-blacklist.md).

## Boosts

*Solr Search: Boosts* lists the ranking adjustments in force. See
[Boosting](04-boosting.md).

## Restarting Solr

Solr runs as a system service outside the hub, so restarting it is a shell
task on the server, not something the administrator interface can do. On a
`hubzero-solr` install that is a `service hubzero-solr restart`. Nothing in
this repository starts, stops, or supervises Solr, so the exact command
depends on your platform packaging.

The hub notices a restart on its own — the next request either reaches Solr
or reports it as not responding. No hub-side action is needed afterwards.

## Cron events

Indexing on a Solr hub is immediate, not scheduled, so a healthy hub needs no
cron job for search at all. The one worth adding is a periodic full re-index,
as a safety net against records that were saved while Solr was down.

**Cron - Search** (`plg_cron_search`) contributes two events to
[Cron](../12-cron.md). A cron job is a row you create; enabling the plugin
does not create one, and nothing runs at all unless the hub is being ticked —
both explained in
[Scheduled tasks](../../03-maintenance/05-cron.md). Add either event from
**Components > Cron > New**, choosing it under **Event**:

| Event | What it does |
|---|---|
| **Run Full Index** | Re-indexes every component currently in the **Indexed** state, in batches. |
| **Process Queue** | Works the `#__search_queue` table. Legacy — see below. |

**Run Full Index** is the useful one. It is the same batch pass as the
**Rebuild Index** button, run for every indexed component without anyone
watching. Schedule it no more often than the hub can finish it; on a large
hub that is nightly or weekly, not by the minute.

> **Warning:** **Process Queue** is left over from an earlier design in which
> content changes were enqueued and indexed later. Nothing in this tree writes
> to `#__search_queue`, so the event runs, finds an empty queue, and returns.
> Scheduling it does nothing. Indexing is now immediate — see
> [Maintaining the index](05-index.md).

That dead queue is worth understanding, because it is the reason a plausible
piece of the hub does nothing. Working the queue is the only thing in the
tree that raises `search.onIndex`, and seventeen search plugins — including
the forum, wiki, questions and wishlist ones — still implement a handler for
it. Because the queue is never filled, none of those handlers is ever called.
They look like Solr indexers in the plugin manager and in the source, and
they are not: they are the remains of the old design. What actually indexes
content is the pair of plugins described in
[Maintaining the index](05-index.md#how-a-change-reaches-the-index).
