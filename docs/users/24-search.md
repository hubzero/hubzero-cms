<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/users/search
source-id: 3328
modified: 2016-08-30
imported: 2026-09-09
-->
# Search

The search box at the top of a hub reaches `/search`. What it can find, and
how the results are ordered, depends on which of two search engines your hub
runs. You can tell them apart at a glance: the Solr results page has a
**Category** list down the left with a count beside each type, and the Basic
results page does not.

Before you conclude that something is missing, read
[What is not searchable](#what-is-not-searchable). Several parts of a hub have
never been in the Solr index, and looking harder will not find them.

## Basic search

Basic search is the default and needs no other software. It queries the hub's
own database through a set of per-type plugins, so its coverage is whatever
those plugins cover: blog entries, citations, collection posts, articles,
courses, events, forum posts, groups, knowledge base articles, member
profiles, projects, publications, questions and answers, resources, wiki
pages, wishlist items, and hand-written site map entries.

Type words into **Search terms** and select **Search**. Each result shows its
title, the section it came from, its date, its contributors, and an excerpt
with your terms highlighted. Related items are grouped under their parent.
There is no field syntax and no faceting; ordering is decided by a handful of
weighting plugins that promote matching titles and contributors.

## Solr search

A hub can instead run Apache Solr alongside itself, which gives ranked
results, counts per content type, and per-type boosting. Setting that up is an
administrator's job — see
[Search](../managers/09-components/31-search/README.md) in the Hub managers book.

The results page has three parts:

- The search box, keeping your terms.
- **Category** down the left: **All Categories** with the total, then one
  entry per indexed content type with the number of matches in it. Selecting
  one restricts the results to that type. Where the administrator has set up
  filters for a type, its filter controls appear underneath.
- The results themselves.

If nothing matched but Solr can suggest a closer spelling, the page offers
**Did you mean:** with the alternatives. If the query itself could not be
parsed, the page says *We were unable to process your query. Try adjusting
your query.* and shows unfiltered results.

Results are filtered by what you are allowed to see, inside the query rather
than after it. Two members searching the same words can therefore get
different counts, and the category counts are as restricted as the results.

### What is not searchable

Only eleven components put anything into the Solr index:

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

Forum posts, wiki pages, questions and answers, wishlist items, calendar
events, and support tickets are **not** in the Solr index. They have search
plugins, but those plugins only serve the Basic engine. On a hub running Solr,
the only way to search them is from inside the feature itself — the forum's
own search, the wiki's, and so on.

Your administrator also chooses which of the eleven are indexed, and an
indexed type can be emptied again, so the working list on any given hub is
whatever appears in the **Category** list.

### Query syntax

Solr searches five fields by default, weighted so that a match in the URL or
the title outranks one in the body: `url`, `title`, `description`,
`fulltext`, and `author`. (`fulltext` is only present on articles;
`description` carries the body text for everything else.) An administrator can
change the list and the weights.

**Restricting to a field.** Prefix a term with a field name and a colon:
`title:diffusion` matches only in titles, `author:smith` only in authors.
Besides the five above, `doi` is set on citations and publication versions, and
`tags` on the types that carry tags. There is no `path` field.

**Partial words.** Solr does not know that "puppy" and "puppies" are the same
word. Use `*` for the rest of a word: `pupp*` matches both.

**Combining terms.** `AND` and `OR` work as you would expect: `cats OR dogs`
returns anything with either, `cats AND dogs` only records with both. Full
syntax is in the [Solr query parser
documentation](https://solr.apache.org/guide/solr/latest/query-guide/standard-query-parser.html).

**Tags.** Where the hub has switched tag search on, the form carries a second
box for tags, and each result lists its tags as links that add that tag to the
query.

> **Note:** Switching tag search on turns the field weights, the phrase
> matching, and the administrator's boosts off — the query is sent without
> them. Results on a tag-search hub are ordered by Solr's plain relevance
> score.

### A result

Each result shows, in this order:

1. **Title**, linking to the item.
2. **Category** — the content type it came from.
3. **Date**, where the type records one.
4. **Author**, where the type records one.
5. **Snippet** — the matching text.
6. **Tags**, as links back into search.
7. The item's **URL**.

## What is this "Feed" button?

That is **What's New** (`/whatsnew`), which is a different page from search.
It lists what has been added to the hub recently — resources, publications,
knowledge base articles, wiki pages, articles, and events, depending on which
of its plugins the hub enables — over a period you choose: the past week,
month, quarter, year, or a named fiscal or calendar year.

Each category heading carries a **feed** link. That link is an RSS feed of
that category over that period, which you can add to a feed reader or a
browser to keep up without visiting the page.
