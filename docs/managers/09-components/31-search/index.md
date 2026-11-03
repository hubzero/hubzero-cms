<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
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

## Which hubs need this section

Most do not. Basic search is the default, needs no other software, no index,
and no scheduled job, and it covers sixteen content types — including forum
posts, wiki pages, questions and wishlist items — plus a hand-written site
map. A hub of a few hundred resources will not outgrow it.

Read the rest of this section if you are considering Solr, or if you have
inherited a hub that already runs it. The usual reason to move is ranking and
faceting: a hub with several thousand resources and publications, where
members complain that the right result is on the third page and there is no
way to narrow by content type. That hub is the running example through these
chapters.

> **Warning:** Moving to Solr **narrows** what search covers. Only eleven
> components put documents in the Solr index. Forum posts, wiki pages,
> questions and answers, wishlists, calendar events and support tickets have
> Basic search plugins but no Solr indexer, so on a Solr hub they stop being
> findable altogether. If your members search the forum, that alone is a
> reason to stay on Basic. See [Breadth](03-breadth.md).

## Turning Solr on is not one switch

This is the part managers get wrong, because the **Overview** screen reports a
healthy connection long before search actually works. Setting **Engine** to
**Apache Solr** changes which controller answers `/search` and nothing else.
All of these have to be true as well:

1. A Solr service is installed, running, and reachable at the configured host
   and port.
2. The **Search - Solr** plugin (`plg_search_solr`) is enabled. **It ships
   disabled.** Without it, nothing a member saves ever reaches the index.
3. Each content type has been discovered and set to **Indexed**, and its first
   full index has finished.
4. You accept that the types listed in the warning above are now unsearchable.

A hub that has done step 1 and stopped has a search box that returns nothing
and an administrator screen with a green tick on it. [Installation and
first-time configuration](01-install.md) walks the whole sequence.

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
