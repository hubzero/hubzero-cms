<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/search
-->
# Search

Search is what members use to find resources, publications, blog entries,
knowledge base articles, courses, projects, groups, and each other. A hub
runs one of two search engines, chosen under **Components > Search** with the
**Options** button and the **Engine** field:

- **Basic (default)** searches the hub's own database. It needs no other
  software and works out of the box. Each content type contributes results
  through its own search plugin, and a handful of weighting plugins order
  them. Small hubs can stay on it indefinitely.
- **Apache Solr** runs Solr alongside the hub and gives ranked results,
  faceted counts by content type, and per-type boosting. Solr has to be
  installed and running, the hub has to be pointed at it, and the index has
  to be built before anything is findable.

Those are the only two engines in this tree. The chapters below are about
the Solr engine. A hub running Basic search has no index to manage:
**Components > Search** shows a single **Site Map** screen, contributed by
the **Search - Sitemap** plugin, where you can add hand-written entries for
queries about the hub's structure rather than its content.

## What Solr search is made of

The **index** holds one document for each searchable record on the hub. What
goes into it is decided by the models that implement
[`Hubzero\Search\Searchable`](../../../../core/libraries/Hubzero/Search/Searchable.php)
— eleven components in this tree — and by the **Searchable Components**
screen, which says which of them are indexed.

Two plugins keep the index current as content changes: **System - HUBzero**
raises an event whenever a record is saved or destroyed, and **Search -
Solr** turns that record into a Solr document. **Boosts** raise or lower the
rank of a resource type or of citations. The **index blacklist** removes an
individual document from Solr and keeps it from coming back.

Four screens under **Components > Search** manage all of this, reached from
the tabs across the top:

| Tab | What it is for |
|---|---|
| **Overview** | Whether Solr is answering, when the last document was indexed, and the **Optimize Index** button. |
| **Searchable Components** | Which content types are indexed, how many documents each has, and the buttons that build and clear those indexes. |
| **Index Blacklist** | Documents struck from the index. |
| **Boosts** | Per-type adjustments to result ranking. |

## In this section

- [Installation and first-time configuration](01-install.md) — pointing the
  hub at Solr and building the first index.
- [Administration](02-admin.md) — the four screens and the cron events.
- [Breadth](03-breadth.md) — what is in the index and how a query reaches it.
- [Boosting](04-boosting.md) — changing the order of results.
- [Maintaining the index](05-index.md) — keeping the index current.
- [Plugins](06-plugins.md) — what each search plugin contributes.
- [Blacklist](07-blacklist.md) — hiding a document from results.

Every option, including the Solr host, port, core, batch size, and the query
and phrase field weights, is listed in the
[configuration reference](../../../reference/configuration/components/search.md).
