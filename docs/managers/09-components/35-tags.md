<!--
status: rewritten
reviewed-against: 2.4-main @ 6efbbe32ed
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/managers/components/tags
-->
# Tags

Tags are the hub's shared vocabulary. A member types a word into a **Tags**
field on a resource, a group, a wiki page, a support ticket or a profile,
and that word becomes a link that gathers everything else carrying it. This
chapter covers the administrator's side: the tag list, aliases, merging,
tagged items, relationships and focus areas. The
[Hub users](../../users/27-tags.md) book covers what members see at `/tags`.

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

Check two or more tags and press **Merge**. The next screen lists the
tags you chose, each with the number of items using it, and asks for the
tag to merge into. Type it into the **Tag** field — it may be an existing
tag, one of the tags being merged, or an entirely new name, which is
created for you. Press **Save & Close** to finish.

Merging moves every item association and every alias from the source tags
onto the destination, adds each source tag's raw text as an alias of the
destination, and then deletes the source tags. There is no undo.

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
