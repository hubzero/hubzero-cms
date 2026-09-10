<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/search/plugins
source-id: 3396
modified: 2019-09-11
-->
# Plugins

Twenty-six plugin directories ship in the **search** group. Twenty-four of
them are registered at install and appear under **Extensions > Plug-ins**
with the filter **Type: search**; the other two are not — see
[Two plugins that are not installed](#two-plugins-that-are-not-installed).
They do not all do the same thing, and which ones matter depends on the
engine the hub is running.

Read this page when a content type is missing from search results, or before
disabling anything in this group. On a Basic hub each plugin is a content
type, so turning one off is how you take a type out of search — the only way,
in fact, since there is no per-type setting anywhere else. On a Solr hub
almost none of them matter, and the two that do ship switched off.

## Plugins for the Basic engine

These turn a query into results by searching the hub's own database. Each one
is a content type; disabling one drops that type out of Basic search results
immediately, with no re-index and nothing to rebuild — re-enable it and the
type comes back on the next query. All of them are enabled on a new hub.

This is the lever to reach for when a hub has a content type it does not use.
A hub with the forum switched off has no forum posts to find, so the plugin
costs a pointless database query on every search; disabling it is safe. It is
also the lever for a hub whose wiki is an internal scratchpad that should not
surface in site search.

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
what the content plugins returned. All are enabled on a new hub except
**Search - Weighttools**, which ships disabled — enable it on a tool-centred
hub where tools should outrank the presentations and datasets that mention
them.

They are the Basic engine's equivalent of [boosting](04-boosting.md), and
they are cruder: each is on or off, with no strength to set. Turning one off
and running the same search again is the whole of the tuning available.

| Plugin | What it does |
|---|---|
| **Search - Suffixes** | Expands query terms with common word endings. |
| **Search - Weighttitle** | Raises results whose title matches the query. |
| **Search - Weightcontributor** | Raises results whose contributors match. |
| **Search - Weighttools** | Weights resources of the tool type. **Ships disabled.** |
| **Search - Sortcourses** | Groups near-identical course resources together. |
| **Search - Sortevents** | Orders event results. |

## Plugins for the Solr engine

Solr indexing does not go through the content plugins above. It goes through
the models that implement `Hubzero\Search\Searchable`, driven by a single
plugin, which ships **disabled**:

| Plugin | Directory | What it does |
|---|---|---|
| **Search - Solr** | `core/plugins/search/solr` | Handles `search.onAddIndex` and `search.onRemoveIndex`, converts the saved record to a Solr document, and writes it. This is the plugin that keeps the index current; a hub using Solr must enable it. |

> **Warning:** **Search - Solr** ships **disabled**, and it is the only thing
> that keeps the index current after the first build. A hub that switched to
> Solr, built its index, and never enabled this plugin has a search that
> stopped at the moment of the switch and will never move again. Enable it
> before you build the first index, not after.

Enabling it is safe on a hub that has not switched engines: with **Engine**
set to **Basic (default)** it has nothing to do. The risk runs the other way.

## Two plugins that are not installed

Two directories in `core/plugins/search` have no extension row on a new hub,
so they do not appear in the plugin manager at all and cannot be enabled from
it. Nothing is missing as a result — neither is needed — but they are worth
knowing about, because running **Discover** in the extension manager pulls
them in and produces two confusing entries.

| Directory | Manifest name | What it is |
|---|---|---|
| `core/plugins/search/remote` | **Search - Solr** | Posts documents to a separate indexing service over HTTP, for a Solr instance shared by several hubs. It hears only the batch operations — a component index, rebuild, or clear — not individual saves. Its **Connection Info** parameters take that service's URL and access token. |
| `core/plugins/search/tickets` | **Search - Resources** | Contributes no Basic search results and no Solr documents; it answers only the type-list event used by the search API. Support tickets are not searchable from the site either way. |

> **Warning:** Both manifests are wrong about themselves. `remote.xml` names
> the plugin **Search - Solr** and declares its element as `solr`, colliding
> with the real Solr plugin; `tickets.xml` names it **Search - Resources**
> and points at a `resources.php` that is not in that directory. If you have
> run **Discover** and now see **Search - Solr** twice, the one to enable is
> the one whose element is `solr` and whose files are in `search/solr`. Do
> not enable the `remote` one unless the hub genuinely shares a Solr instance
> with other hubs; there is nothing for it to talk to otherwise.

## The plugins that look like indexers and are not

Seventeen search plugins — every one in the first table except
**Search - Sitemap**, plus the uninstalled `tickets` plugin — carry an
`onIndex` method that turns a record into index data. It is easy to read
that as Solr support for forum posts, wiki pages, questions and wishlists.
It is not. The only thing in this tree that
raises `search.onIndex` is the **Process Queue** cron event, and that event
reads a queue table nothing ever writes to. None of those seventeen handlers
has ever been called on a running hub.

Take the practical point and ignore the archaeology: what is in the Solr
index is decided by the eleven models listed in
[Breadth](03-breadth.md#what-can-be-indexed), and by nothing else.

## The system plugin

Indexing also depends on one plugin outside the search group: **System -
HUBzero** (`plg_system_content`) under **Type: system**, which raises the
event that **Search - Solr** listens for. It is enabled by default. If new
content stops appearing in search, check that plugin and **Search - Solr**
before anything else.
