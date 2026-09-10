<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/search/breadth
source-id: 3393
modified: 2019-09-11
-->
# Breadth

"What can I search for?" The answer is: anything you have permission to see,
of a content type whose component is in the **Indexed** state on the
**Searchable Components** screen.

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
Changing these changes the ranking of every result on the hub; they are in
the [configuration reference](../../../reference/configuration/components/search.md)
with the rest.

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
