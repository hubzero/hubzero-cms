<!--
status: rewritten
reviewed-against: 2.4-main @ 6efbbe32ed
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/users/tags
-->
# Tags

Tags are keywords that tie together everything on the hub that has
something in common. Resources, publications, groups, wiki pages, blog
posts, questions, forum threads, courses, events, support tickets and
member profiles can all be tagged, and a tag's page collects all of them in
one list. Tags live at `/tags` on the hub.

## Adding tags to your content

Tags are not created on a page of their own. You add them while you are
creating or editing something: a resource, a project, a group, a wiki page,
your profile. Look for the field labelled **Tags**.

Type a word and press **Enter**, or pick one of the suggestions the field
offers as you type. Repeat for as many tags as you want — there is no
limit. To take a tag off, hover over it and click the **x** beside it. Save
the item and the tags are stored with it.

A tag you type does not have to exist already. If it does not, the hub
creates it, and it becomes available to everybody from then on.

> **Note:** The hub stores a normalized form of every tag alongside what
> you typed. Capitals, spaces and punctuation are stripped, so "N.Y.", "NY"
> and "ny" are all the same tag. Type it the way you want it read; the hub
> will match it to the right one.

Some tags are aliases for others. If the hub's managers have set "aqua" up
as an alias of "water", typing "aqua" tags your item with "water".

## The tags page

`/tags` is the hub's front door for tags. It has a search box that takes
one or more tags, and two clouds: **Recently Used** and **Top Used**. Click
any tag in either cloud to open its page. **Browse the full list** goes to
the complete list.

## Browsing all tags

`/tags/browse` lists every tag on the hub in a table with its aliases.
**Search tags** filters the list by a keyword or phrase, and two sort links
reorder it: **Popular** by how many items carry each tag, **Alphabetically**
by name. Click either a second time to reverse the direction. Click a tag
to open its page.

The panel beside the list explains what an alias is: another name for a
tag, commonly an abbreviation, such as "H2O" for "water".

> **Note:** The **# tagged** count includes everything carrying the tag,
> including pending, unpublished and private items, so it may be larger
> than the number of results you can actually see.

## A tag's page

Open a tag and you get everything on the hub tagged with it, at
`https://<your hub>/tags/<tag>`. If a manager has written a description for
the tag, it appears at the top.

Down the side, **Categories** breaks the results up by what kind of thing
they are — All Categories, Resources, Publications, Groups, Members, Wiki
Pages, Blogs, Questions & Answers, Forum, Citations, Collections, Courses,
Events, Knowledge Base, Support Tickets — with a count beside each. Only
categories that actually have results are listed. Resources and
publications break down further by resource type. Click a category to see
just those results; the address becomes `/tags/<tag>/<category>`.

Two links above the results reorder them: **Title/Name** sorts
alphabetically, **Date** sorts newest first. Date is the default. Long
result sets are paged.

The search box at the top of the page is filled in with the tag you are
looking at. Add a second tag to it, separated by a comma, and press
**Search** to narrow the results to items carrying *all* of the tags — not
either one. **More tags** returns you to the tags home page.

> **Note:** Results do not include pending, unpublished, or some private
> items, and each category applies its own rules about who may see what.
> Two people can get different result counts for the same tag.

Every tag page also publishes a feed at
`https://<your hub>/tags/<tag>/feed.rss`.

## What you cannot do

You cannot delete a tag, and you cannot edit its description or its
aliases. Those are reserved for the hub's managers. What you can do is
remove a tag from your own content, by editing the item and deleting the
tag from its **Tags** field.

You also cannot see who tagged something. The hub records the tagger, but
that information is not shown to members.
