<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/search/index
source-id: 3395
modified: 2019-09-11
-->
# Maintaining the index

Once the first index is built there is very little to do. Solr stores the
index on the server's filesystem, so it survives a reboot or a crash of the
Solr process and does not have to be rebuilt after either.

## How a change reaches the index

Indexing is immediate, not queued. Saving a record raises a content event,
and two plugins carry it to Solr:

1. **System - HUBzero** (`plg_system_content`) listens for `onContentSave`
   and `onContentDestroy` on any model built on the hub's ORM, and raises
   `search.onAddIndex` or `search.onRemoveIndex`.

   <!--include: core/plugins/system/content/content.php:23-40-->

2. **Search - Solr** (`plg_search_solr`) picks that up, checks that the
   record's component is in the **Indexed** state and that the document is not
   on the blacklist, converts the record to a Solr document, and sends it.

Both plugins must be enabled under **Extensions > Plug-ins**. **Search -
Solr** ships disabled, so a hub that has switched engines but never enabled
it will index nothing new, however healthy the Overview screen looks.

Documents are sent with a `commitWithin` deadline rather than an immediate
commit, taken from the **CommitWithin** option (default `300000`, five
minutes in milliseconds). A saved record is therefore visible to search
within that window, not on the next page load. Lowering it makes changes
appear sooner at the cost of more work in Solr.

## Rebuilding

Three things do a full pass over a component's records, all of them the same
batch operation:

- The **Active?** toggle and the **Rebuild Index** button on
  [Searchable Components](02-admin.md#searchable-components).
- The **Run Full Index** event of **Cron - Search**, which does it for every
  component in the **Indexed** state.
- [`muse searchmigration run`](../../../reference/muse.md#muse-searchmigration) on
  the command line, with `--all` or `-components`, and `--rebuild` to include
  components already indexed.

Each pass reads records in blocks of the **Batch Size** option (default
`2000`) and pushes each block to Solr before reading the next. A full index of
a large hub takes a long time; it is the reason the batch size and the commit
deadline are configurable at all. Later passes are much quicker, because Solr
is replacing documents rather than creating them.

Prefer the command line for anything big. The screen version runs inside a
web request and is bounded by the web server's time limit.

## Removing documents

- **Delete component(s) results** on the Searchable Components screen deletes
  every document of the checked types from Solr and sets them back to
  **Not Indexed**.
- The [blacklist](07-blacklist.md) removes one document and keeps it out.

Neither touches the hub's own data. Both are reversible by re-indexing —
except a blacklisted document, which is skipped until its blacklist entry is
removed.

## Optimizing

**Optimize Index** on the Overview screen asks Solr to defragment its index.
Solr manages this itself in normal operation; running it by hand after a
large delete or a full rebuild is reasonable, on a schedule it is not.

## When something looks wrong

| Symptom | Where to look |
|---|---|
| *The search engine is not responding* | The connection settings on the **Solr** tab, and whether the Solr service is running. |
| New content never appears | **Search - Solr** and **System - HUBzero** in the plugin manager. |
| A whole type is missing | Its state on **Searchable Components**. A **Not Indexed** or trashed component contributes nothing. |
| One record is missing | The [Index Blacklist](07-blacklist.md). |
| Counts look stale | **Last Document Insert** on the Overview screen, against the **CommitWithin** window. |
