<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/search/blacklist
source-id: 3397
modified: 2019-09-11
-->
# Blacklist

The blacklist strikes an individual document from the search index and keeps
it out. Without it, a document deleted from Solr comes straight back the next
time its record is saved or its component re-indexed.

Most hubs never use it. It exists for the one record that has to stop
appearing in search now, and that you cannot or should not remove from the
hub: a resource whose author has asked for it to be quietly withdrawn while a
correction is prepared, a member profile a spam wave has filled with
keywords, a project page that got indexed while it was briefly public. In
each case the record has to stay where it is, and only its visibility in
search is the problem.

> **Important:** The blacklist is not a way to hide something. The record
> itself is untouched. Blacklisting a resource does not unpublish it, does
> not hide its page, does not restrict its access level, and does not stop
> anyone reaching it by link, by a menu item, or from a listing page — it
> only removes it from search results. If the content should not be readable,
> unpublish it or change its access level; the blacklist is the wrong tool
> and will leave you thinking the problem is solved.

## Blacklisting a document

1. Go to **Components > Search > Searchable Components**.
2. Select the record count beside the type you want, for example
   **Resources**. That opens *Solr Search Indexed Documents* for that type.
3. Find the document. The **Filter** box takes a Solr query, so
   `title:microscope` narrows the list; leaving it empty lists everything.
4. Select **Add to blacklist** in that row.

The document is deleted from Solr as the button is pressed, and the row's
button changes to **Marked for Removal**. There is no queue and no waiting
period: the next search will not find it.

The change is reversible, but not symmetrically: removing the entry lifts the
block without putting the document back, so undoing a blacklisting takes a
re-index of that type as well. Blacklisting one record is instant and
harmless; be more careful about working down a long list, since restoring
what you change your mind about means saving each record again or re-indexing
the whole type.

> **Note:** The screen reports *Successfully marked … for removal* whether or
> not Solr accepted the delete — the helper that sends it does not check the
> response. If a document is still turning up in search afterwards, check the
> Overview screen for the connection, then re-open the document listing.

## Reviewing and undoing

**Components > Search > Index Blacklist** lists every entry, showing the
document id, who added it, and when, each with a **Remove entry** button.
The screen reads *There are no entries on the blacklist* when it is empty.

Removing an entry only lifts the block; it does not put the document back.
The document returns the next time its record is saved, or the next time its
component is re-indexed from
[Searchable Components](02-admin.md#searchable-components) or **Run Full
Index**.

## What the block covers

Every route into the index checks the blacklist before writing:

- the live path, when **Search - Solr** handles a saved record;
- the batch path, when a component is indexed or rebuilt.

The check is by document id, and the id is unique to one record of one type,
so blacklisting a resource has no effect on any other document.
