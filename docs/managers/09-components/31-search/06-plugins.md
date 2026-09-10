<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/search/plugins
source-id: 3396
modified: 2019-09-11
-->
# Plugins

Twenty-six plugins ship in the **search** group, listed under **Extensions >
Plug-ins** with the filter **Type: search**. They do not all do the same
thing, and which ones matter depends on the engine the hub is running.

## Plugins for the Basic engine

These turn a query into results by searching the hub's own database. Each one
is a content type; disabling one drops that type out of Basic search results.
All of them are enabled on a new hub.

| Plugin | Searches |
|---|---|
| **Search - Blogs** | Blog entries |
| **Search - Citations** | Citations |
| **Search - Collections** | Collection posts |
| **Search - Content** | Articles |
| **Search - Courses** | Courses |
| **Search - Events** | Calendar events |
| **Search - Forum** | Forum posts |
| **Search - Groups** | Groups |
| **Search - Kb** | Knowledge base articles |
| **Search - Members** | Member profiles |
| **Search - Projects** | Projects |
| **Search - Publications** | Publications |
| **Search - Questions** | Questions and answers |
| **Search - Resources** | Resources |
| **Search - Sitemap** | Hand-written site map entries |
| **Search - Wiki** | Wiki pages |
| **Search - Wishlists** | Wishlist items |

**Search - Sitemap** is the odd one. As well as contributing results, it
supplies the whole administrative screen the Basic engine shows at
**Components > Search**: a small editor for site map entries, so that a query
about the structure of the hub — rather than its content — can be answered
with a hand-written link.

## Plugins that shape Basic ranking

These contribute no results of their own. They adjust the terms or reorder
what the content plugins returned, and are enabled by default.

| Plugin | What it does |
|---|---|
| **Search - Suffixes** | Expands query terms with common word endings. |
| **Search - Weighttitle** | Raises results whose title matches the query. |
| **Search - Weightcontributor** | Raises results whose contributors match. |
| **Search - Weighttools** | Weights resources of the tool type. |
| **Search - Sortcourses** | Groups near-identical course resources together. |
| **Search - Sortevents** | Orders event results. |

## Plugins for the Solr engine

Solr indexing does not go through the content plugins above. It goes through
the models that implement `Hubzero\Search\Searchable`, driven by two plugins,
both of which ship **disabled**:

| Plugin | Directory | What it does |
|---|---|---|
| **Search - Solr** | `core/plugins/search/solr` | Handles `search.onAddIndex` and `search.onRemoveIndex`, converts the saved record to a Solr document, and writes it. This is the plugin that keeps the index current; a hub using Solr must enable it. |
| **Search - Solr** | `core/plugins/search/remote` | Posts documents to a separate indexing service over HTTP, for a Solr instance shared by several hubs. It hears only the batch operations — a component index, rebuild, or clear — not individual saves. Its **Connection Info** parameters take that service's URL and access token. Leave it disabled unless the hub is part of such an arrangement. |

> **Warning:** Both of those plugins declare `Search - Solr` as their name, so
> the plugin manager shows that name twice. The one to enable for ordinary
> Solr indexing is the one whose element is `solr`; the duplicate is
> `remote`. The support-ticket plugin described below has the same problem
> — its manifest names it `Search - Resources`, so that name appears twice
> as well. Go by the element, not by the displayed name.

The remaining plugin is the one in `core/plugins/search/tickets`. It
contributes no Basic search results and no Solr documents; it only answers
the type-list event used by the search API. Support tickets are not
searchable from the site.

## The system plugin

Indexing also depends on one plugin outside the search group: **System -
HUBzero** (`plg_system_content`) under **Type: system**, which raises the
event that **Search - Solr** listens for. It is enabled by default. If new
content stops appearing in search, check that plugin and **Search - Solr**
before anything else.
