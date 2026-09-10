<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/users/search
source-id: 3328
modified: 2016-08-30
imported: 2026-09-09
-->
# Search

Search is for the moment when you know roughly what you want and not where it
lives: a dataset a colleague mentioned, a paper the hub holds, the profile of
the person who wrote a tool. You type words, the hub looks across its content,
and you get a ranked list. The search box at the top of a hub reaches
`/search`.

Read this page if search is not giving you what you expect. Hub search is
narrower than a web search engine, in ways that are not obvious from the
results page, and the fix is usually to search somewhere else rather than to
search harder.

Say a thermal transport lab has published a thin-film conductivity dataset on
the hub, discussed the measurement protocol on its group wiki, and answered
questions about it in the forum. On a hub running the Solr engine, searching
for *thin film conductivity* finds the dataset and neither of the other two,
and no rewording of the query will change that. The rest of this page explains
why, and what to do instead.

## Which engine your hub runs

What search can find, and how the results are ordered, depends on which of two
search engines your hub runs. You can tell them apart at a glance: the Solr
results page has a **Category** list down the left with a count beside each
type, and the Basic results page does not.

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

On a Basic hub the lab's forum thread and wiki protocol *are* reachable from
`/search`, because those two plugins are installed. The gap described below is
specific to hubs that have moved to Solr.

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

If the query could not be parsed, the page says *We were unable to process
your query. Try adjusting your query.* and shows unfiltered results. If
nothing matched at all, the page says so and stops; it does not suggest
alternative spellings.

Results are filtered by what you are allowed to see, inside the query rather
than after it. Two members searching the same words can therefore get
different counts, and the category counts are as restricted as the results.
If a colleague can see a result you cannot, the difference is access, not
indexing.

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
the only way to search them is from inside the feature itself.

That is the answer to the lab's missing protocol and forum thread. Go to the
feature and use its own search:

| What you are looking for | Where to search for it |
|---|---|
| A wiki page | The **Search** box in the wiki sidebar — see [Wiki](23-wiki.md) |
| A forum thread | The forum's own search — see [Forum](09-forum.md) |
| A question or answer | The search box on `/answers` — see [Questions and answers](19-questions.md) |
| A wish | The search box on the wishlist itself — see [Wishlist](29-wishlist.md) |
| An event | Nowhere. The calendar has no search box; browse it by year, month, week or day — see [Events](07-events.md) |
| A support ticket | The search box on your ticket list — see [Support](12-support.md) |

Tags reach further than search does. Every one of those six features can be
tagged, and a tag's page lists tagged items from all of them, so if the lab
tagged its wiki protocol and its forum thread `thinfilm`, opening
`/tags/thinfilm` finds them when `/search` cannot. See [Tags](27-tags.md).

Your administrator also chooses which of the eleven are indexed, and an
indexed type can be emptied again, so the working list on any given hub is
whatever appears in the **Category** list.

### What you can type

Type the words you are looking for. Search does not read operators: before the
query is sent, the hub escapes every character and word that Solr would treat
as syntax — `+ - ! ( ) { } [ ] ^ " ~ * ? : /`, `&&`, `||`, and the words
`AND`, `OR` and `NOT`. So `title:diffusion` searches for the literal text
*title:diffusion*, `pupp*` will not match "puppies", and `cats OR dogs` looks
for records containing the word *OR*. Leave them out and give plain words.

Solr matches those words against five fields, weighted so that a match in the
URL or the title outranks one in the body: `url`, `title`, `description`,
`fulltext`, and `author`. (`fulltext` is only present on articles;
`description` carries the body text for everything else.) There is no `path`
field. An administrator can change the list and the weights.

Because you cannot narrow a query by typing at it, narrow it afterwards
instead: select a type in the **Category** list, and use any filters the
administrator has set up under it.

**Tags.** Where the hub has switched tag search on, the form carries a second
box for tags, and each result lists its tags as links that add that tag to the
query. This is the one way to combine two conditions: words in the box, a tag
beside it.

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

## Search is not the tags page

The two look similar and answer different questions. Search matches the words
you typed against the text of an item, over the eleven indexed types, and
ranks what it finds. A tag page lists items somebody deliberately labelled,
over every type that supports tagging, in date or title order with no ranking.
Search is for "something about thin films"; tags are for "the things this lab
decided belong together". When search comes up short, try the tag.

## What is this "Feed" button?

That is **What's New** (`/whatsnew`), which is a different page from search.
It lists what has been added to the hub recently — resources, publications,
knowledge base articles, wiki pages, articles, and events, depending on which
of its plugins the hub enables — over a period you choose: the past week,
month, quarter, year, or a named fiscal or calendar year.

Each category heading carries a **feed** link. That link is an RSS feed of
that category over that period, which you can add to a feed reader or a
browser to keep up without visiting the page.
