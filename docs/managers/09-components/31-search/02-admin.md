<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/search/admin
source-id: 3392
modified: 2019-09-11
-->
# Administration

**Components > Search** has four tabs when the engine is Solr: **Overview**,
**Searchable Components**, **Index Blacklist**, and **Boosts**. Every tab
carries an **Options** button that opens the same component configuration.

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

**Cron - Search** (`plg_cron_search`) contributes two events to
[Cron](../12-cron.md). Add either from **Components > Cron > New**, choosing
it under **Event**:

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
