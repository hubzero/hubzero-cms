<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/managers/components/tags
-->
# Tags

Tags are the hub's shared vocabulary. A member types a word into a **Tags**
field on a resource, a group, a wiki page, a support ticket or a profile,
and that word becomes a link that gathers everything else carrying it. This
chapter covers the administrator's side: the tag list, aliases, merging,
tagged items, relationships and focus areas. The
[Hub users](../../users/27-tags.md) book covers what members see at `/tags`.

Almost nothing here is about creating tags. Members create them, in their
hundreds, by typing; your job is to tidy up after them. A hub two years old
has `machine-learning`, `machine learning`, `ML` and `Machine Learning` all
in use, splitting the same body of work across four tag pages that each look
half-empty. Merging those four into one is the characteristic task of this
component, and the rest of the screens exist to support it.

That makes tags different from categories elsewhere in the hub. A category
is a list you define and content is filed into. A tag is whatever somebody
typed. You cannot stop a member inventing a new one, and there is no
approval step — so a tag vocabulary is curated after the fact or not at all.

> **Warning:** The two things a manager does here — merging and deleting —
> both reach out of this component and change every piece of content
> carrying the tag, across every component, in one action. Neither can be
> undone, and neither asks a second time beyond a plain confirmation. Read
> [Merging](#merging) and the note on deleting before you use either on a
> live hub.

Open it under **Components > Tags**. Four sub-menu links sit at the top
left: **Tags**, the list below; **Relationships**; **Focus Areas**; and
**Plugins**, which opens the Plugin Manager filtered to the `tags` folder.
The **Plugins** link appears only if you may manage plugins.

## Tags

The list shows every tag in the system with these columns.

| Column | Notes |
|---|---|
| Raw Tag | The word or phrase as it was typed. |
| Tag | The normalized form: transliterated to ASCII, lowercased, with every non-alphanumeric character stripped. "N.Y.", "NY" and "ny" all normalize to `ny`. |
| Type | **User**, **Admin** or **Core**. |
| # tagged | How many items carry the tag. The number links to the Tagged Items screen filtered to this tag. |
| Aliases | How many aliases the tag has. |
| Created | When the tag was first created, or *(unknown)*. |

Click any column heading to sort by it; click again to reverse. Above the
list, **Search (raw tag)** matches a term against the tag, its raw tag, and
its aliases, then press **Go**. The **Filter** menu narrows the list to
**All tags (default)**, **User tags** or **Admin tags**.

The toolbar offers:

- **Options** — the component's configuration; see [Options](#options).
- **Re-calculate # Tagged and Aliases** — recount the checked tags' items
  and aliases and store the totals.
- **Pierce** — copy one tag's items onto another; see
  [Piercing](#piercing).
- **Merge** — fold two or more tags into one; see [Merging](#merging).
- **New** — create a tag.
- **Delete** — remove the checked tags after a confirmation. This deletes
  the tag, its aliases, and every association it has with content. It is
  permanent.
- **Help** — the built-in help screen.

> **Warning:** Deleting a tag does not just remove a word from a list. It
> unpicks the tag from every resource, group, wiki page, ticket and profile
> that carried it, one row at a time, and those links are gone: the content
> stays, the tag page stops existing, and nothing on the hub records which
> items used to be tagged. The tag's own row is copied into the tag log
> before it goes, so you can see *what* was deleted and by whom, but not
> *what it was on*, so it cannot be rebuilt. If the tag is wrong rather than
> worthless, [merge](#merging) it into the right one instead — that keeps
> every association and moves it.

> **Note:** The **# tagged** and **Aliases** counts are stored on the tag
> row, not counted live. If they look wrong, check the tags and press
> **Re-calculate # Tagged and Aliases**.

## Creating or editing a tag

| Field | Notes |
|---|---|
| Type | **User**, **Core** or **Admin**. Admin tags are hidden from the site: they do not appear in tag clouds, autocompletion or search results for anyone without the Manage permission. They are a way to attach metadata that is not meant for visitors. A new tag defaults to **Admin**. |
| Raw Tag | Required. The tag as it should be displayed. Up to 250 characters. |
| Tag | Read-only. The normalized form, generated from the raw tag when you save. |
| Alias | A comma-separated list of alternate spellings, abbreviations or synonyms. See [Aliases](#aliases). |
| Description | Free text shown above the results on the tag's page on the site. |

The panel beside the form shows the tag's **ID**, **Creator** and
**Created** date, and, once the tag has been edited, **Modifier** and
**Modified**. Below that, **Activity log** lists up to the last hundred
recorded actions on the tag — created, edited, aliases added or removed,
associations moved, copied or deleted — each with a timestamp and the
person responsible.

Press **Save** to save and stay, **Save & Close** to return to the list, or
**Cancel** to discard.

## Aliases

An alias maps one spelling onto another. Give the tag "water" the aliases
`h2o, aqua` and a member who types "aqua" ends up with the tag "water"
instead; the alias itself is never stored on the item.

> **Warning:** Adding an alias that already exists as a tag in its own
> right merges it. When you save, every item tagged with "aqua" is
> re-tagged with "water" and the "aqua" tag is deleted. This cannot be
> undone.

## Merging

Merging is how you fix a split vocabulary. It is the only action here that
consolidates rather than destroys: everything tagged with any of the source
tags comes out tagged with the destination, and the old spellings survive as
aliases, so a member who types one of them still lands in the right place.

Check two or more tags and press **Merge**. The next screen lists the
tags you chose, each with the number of items using it, and asks for the
tag to merge into. Type it into the **Tag** field — it may be an existing
tag, one of the tags being merged, or an entirely new name, which is
created for you. Press **Save & Close** to finish.

Merging moves every item association and every alias from the source tags
onto the destination, adds each source tag's raw text as an alias of the
destination, and then deletes the source tags. There is no undo.

### Merging four spellings into one

Taking the `machine learning` example from the top of the chapter:

1. In **Components > Tags**, type `machine` into **Search (raw tag)** and
   press **Go**. The variants appear together; the **# tagged** column
   shows how much content each is holding.
2. Decide which one wins. Prefer the one with the most items — the fewest
   associations move, and the tag page most people already link to keeps
   working.
3. Tick the losing spellings. Leave the winner unticked; you do not have to
   include it, and naming it as the destination is enough.
4. Press **Merge**. Type the winning tag's text into the **Tag** field
   exactly as it should read, and press **Save & Close**.
5. Open the surviving tag and check its **Alias** field. The old spellings
   are now aliases, which is what redirects the next member who types one.
6. Look at **# tagged**. If it has not moved, tick the tag and press
   **Re-calculate # Tagged and Aliases** — the count is stored, not
   counted live.

Everything that carried the old tags now carries the new one, on every
component of the hub, and nobody was notified. That is normally what you
want; it also means a mistake here is visible to members immediately and is
not something you can put back.

> **Tip:** If you are not sure a merge is right, [pierce](#piercing) first.
> Piercing adds the destination tag to the same items without removing
> anything, so you can look at the result and merge — or not — afterwards.

## Piercing

Piercing is the non-destructive relative of merging. Check the tags, press
**Pierce**, and name a destination tag in the **New Tag** field. Every item
carrying the checked tags is also tagged with the destination. The original
tags and their associations are left exactly as they were.

## Tagged Items

The Tagged Items screen lists individual tag-to-item links. Reach it by
clicking a number in the **# tagged** column, which filters it to that tag,
or from a Tagged Items link. Columns are **ID**, **Tag ID** (hidden when
the list is already filtered to one tag), **Item Type**, **Item ID**,
**Created** and **Creator**. The **Filter** menu lists the item types
actually present, and defaults to **All Types**.

**New** and **Delete** are the only toolbar actions besides **Help**. The
edit form takes three required fields: **Tag ID**, **Item ID** and **Item
type** — the last usually a component name without the `com_` prefix, such
as `wishlist` or `blog`. Deleting a row removes the tag from that item; it
does not touch the tag or the item.

## Relationships

**Relationships** draws a tag and its neighbours as a force-directed graph.
Type a tag into **Find tag**, choose **labels and hierarchy** or
**implicit**, and press **Lookup**. The hierarchical view follows the
`parent` and `label` links you have defined, up to seven levels deep. The
implicit view infers relationships from co-occurrence: tags that appear on
the same items, weighted by how often, ignoring pairs that occur only once.

Click a node and the **Metadata** panel below fills in with its
**Description**, **Labeled**, **Labels**, **Parents** and **Children**.
Edit those lists and press **Update** to save. Naming a tag that does not
exist yet creates it.

## Focus Areas

Focus areas are groups of tags offered as a structured choice during
resource submission, rather than as free typing. Each group has a **Group
name** — itself a tag, whose children are the choices — a set of resource
types it applies to, whether it is **optional**, **mandatory**, or
mandatory **until depth** *n*, and whether choices are
**multiple-select (checkbox)**, **single-select (radio)**, or
single-select until a depth. **Add group** appends a group, **Delete
group** removes one, and **Save** writes the whole set.

Tags nested more deeply than the mandatory level are presented as an
optional, multiple-selection box.

## Options

The **Options** button opens the component configuration. Ten **Focus
Area** fields name tags used by the tool contribution workflow in
[Tools](36-tools.md), and two cache settings control whether the tag clouds
on `/tags` are cached and for how long. Every option is listed with its
values in the
[configuration reference](../../reference/configuration/components/tags.md).

The **Permissions** tab sets, per user group, who may configure the
component, manage it, and create, delete, edit, or change the state of
tags. Manage is what admits you to the administrator screens at all, and it
is also what lets a person see admin tags on the site.

## Plugins

The tags plugins decide what a tag's page on the site can find. Each one
searches its own component and contributes a category to the results:
Questions & Answers, Blogs, Citations, Collections, Courses, Events, Forum,
Groups, Knowledge Base, Members, Publications, Resources, Support Tickets
and Wiki Pages. The Resources and Publications plugins go further and split
their results into sub-categories by resource type.

Disable a plugin and its content disappears from tag pages. Each plugin
applies its own component's visibility rules, so results differ by who is
looking: guests see only public groups, members see groups they belong to,
and profiles are filtered by access level.

## API

The component exposes endpoints under `/api/tags` for listing, reading,
creating, updating and deleting tags, and for adding a tag to or removing
it from an item. See the [API reference](../../reference/api/tags.md).
