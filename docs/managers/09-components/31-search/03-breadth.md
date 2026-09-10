<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/search/breadth
source-id: 3393
modified: 2019-09-11
-->
# Breadth

"What can I search for?" The answer is: anything you have permission to see,
of a content type whose component is in the **Indexed** state on the
**Searchable Components** screen.

Read this page before you move a hub to Solr, and read it again the first
time a member says search cannot find something. It is the page that says
what is not in the index, and most of the search complaints a manager fields
come from that list rather than from anything being broken.

## What can be indexed

A content type reaches the Solr index only if one of its models implements
[`Hubzero\Search\Searchable`](../../../../core/libraries/Hubzero/Search/Searchable.php).
Eleven components do in this tree:

| Component | What it contributes |
|---|---|
| Blog | Blog entries |
| Citations | Citation records |
| Collections | Collection posts |
| Content | Articles |
| Courses | Courses |
| Groups | Groups |
| Knowledge base | Articles |
| Members | Member profiles |
| Projects | Projects |
| Publications | Publication versions |
| Resources | Resources, including tools |

Nothing else is in the Solr index. In particular there is no Solr indexer for
forum posts, wiki pages, questions and answers, wishlists, events, or support
tickets — those types have search plugins, but the plugins only serve the
Basic engine. See [Plugins](06-plugins.md).

> **Warning:** That gap is the single most common surprise on a Solr hub, and
> nothing on any screen announces it. A hub whose members live in the group
> forums and the wiki loses search over both the day it switches engines.
> Nobody files a bug, because search still works — it just never returns a
> forum post again. If you inherit a hub where "search misses half the site",
> check **Engine** first; the fix is often to go back to
> **Basic (default)**, not to rebuild anything.

Turning a component's row off has the same effect for that type, so a
half-configured hub can be missing resources or publications too. The list
below the search box is the honest answer to "what is searchable here": if a
type has no category on the results page, it is not in the index.

A discovered component is not indexed until someone activates it, and an
activated one can be emptied again, so the working list on any given hub is
whatever **Searchable Components** shows in the **Indexed** state.

## What a member sees

The search results page lists the indexed types down the side under
**Category**, each with the number of matches in that type, and
**All Categories** at the top. The list is built from the component rows,
using each one's **Title**. Selecting a category restricts the results to
that type; where an
administrator has configured filters for the type, its filter controls appear
underneath.

Results are filtered by access level for every visitor except a Super User,
so two members searching the same words can get different counts. That
filtering happens in the Solr query, not after it, so the category counts are
accurate for the person reading them.

## How wide a query reaches

Terms are matched against the fields named in the **Query Fields** option,
which defaults to:

```text
url^10 title^5 description fulltext author
```

The `^` values are relative weights: a hit in the URL counts ten times a hit
in the full text, a hit in the title five times. **Phrase Fields** does the
same for multi-word phrases, and **Phrase Slop** (default `10`) says how many
words may sit between the terms of a phrase and still count as a match.
The defaults are sensible and there is no reason to touch them on a new hub.
Changing them changes the ranking of every result on the hub at once, with no
preview and no way to compare before and after, so if you do change them,
change one weight, note what it was, and search for a handful of things you
know the right answer to. They are in the
[configuration reference](../../../reference/configuration/components/search.md)
with the rest.

> **Warning:** **Query Fields**, **Phrase Fields**, **Phrase Slop** and every
> boost are applied in one block of code that is skipped entirely when the
> **Tag Search Box** option is on. Turn tag search on and the hub stops
> weighting titles and URLs above body text, as well as ignoring the
> [boosts](04-boosting.md) — results come back in Solr's own default order.
> **Tag Search Box** defaults to off, and that is the sensible setting unless
> you specifically want the tag filter and are willing to give up ranking
> control for it.

> **Note:** The site search box is not a Solr query box. Lucene's special
> characters — including `*`, `?`, `:`, `+`, `-`, quotes, and the words AND,
> OR, and NOT — are escaped before the query is sent, so they are searched for
> literally. Searching `*` finds documents containing an asterisk, not every
> document.

## Seeing what is actually in the index

To look at the index itself, use **Components > Search > Searchable
Components** and select a component's record count. That opens *Solr Search
Indexed Documents*, a paged list of that type's documents showing **ID**,
**Type**, **Title**, **Access**, and **Owner**.

The **Filter** box on that screen is passed to Solr as a raw query, without
the escaping the site search applies, so Lucene syntax works there: it starts
at `*:*` for everything, and `title:microscope` or `access_level:public`
narrow it. That is the one place on the hub where a wildcard search is
possible.
