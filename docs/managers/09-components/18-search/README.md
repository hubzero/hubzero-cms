<!--
status: rewritten
reviewed-against: 2.4-main @ ddeb90135f
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/managers/components/search
-->
# Search

Search is what members use to find resources, publications, wiki pages,
forum threads, questions, group content, and members themselves. A hub runs
one of two search engines, chosen under **Components > Search** with the
**Engine** option:

- **Basic** uses the hub's own database. It needs no other software, it
  works out of the box, and it matches on titles and text without ranking
  or faceting. Small hubs can stay on it indefinitely.
- **Solr** runs Apache Solr alongside the hub and gives ranked results,
  facets, and per-type weighting. It has to be installed, configured, and
  filled with an index before it returns anything, and the index has to be
  kept current afterwards.

The chapters below cover the Solr engine. On a hub running Basic search,
the only screen that applies is the engine setting itself.

## What Solr search is made of

The **Index** holds a document for every searchable thing on the hub. The
**search plugins** in `core/plugins/search/` decide what goes into it: one
plugin per kind of content, each turning its own records into documents.
**Boosts** raise or lower the rank of chosen documents or document types.
The **blacklist** hides individual documents from results without deleting
them from the hub.

Four screens under **Components > Search** manage this: **Solr**, for the
connection and index health; **Searchable**, for which content types are
indexed and when; **Boosts**; and the blacklist, reached from the Solr
screen.

## In this section

- [Installation and first-time configuration](install.md) — installing
  Solr, pointing the hub at it, and building the first index.
- [Administration](admin.md) — the day-to-day screens.
- [Breadth](breadth.md) — which content types are searched and how widely a
  query reaches.
- [Boosting](boosting.md) — changing the order of results.
- [Maintaining the index](index.md) — keeping the index current and
  rebuilding it.
- [Plugins](plugins.md) — the per-type indexers.
- [Blacklist](blacklist.md) — hiding a document from results.

Every option, including the Solr host, port, core, batch size, and the
query and phrase field weights, is listed in the
[configuration reference](../../../reference/configuration/components/search.md).
