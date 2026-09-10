<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/collections
source-id: 3376
modified: 2016-07-12
imported: 2026-09-09
-->
# Collections

Collections are a Pinterest-style scrapbook. A hub user makes a collection,
then pins content from around the hub into it as posts. Members and groups
own collections; the administrator interface exists to inspect and clean up
what they have made, not to build collections for them. The user's side is
described in the [Collections chapter](../../users/01-collections.md) of the Hub
users book.

Most hubs never need this screen. Collections is a member convenience: it
gives people somewhere to keep the hub's content they want to come back to,
and it gives a group a board it can point newcomers at. Nothing else depends
on it, no catalogue is built from it, and turning it off breaks nothing. You
will open the administrator screens for one reason — someone reported a
post, or a member deleted an account and left a board nobody can reach — and
then you will close them again.

It is not curation and it is not the catalogue. A collection is one member's
or one group's board; it does not decide what appears in
[Resources](29-resources.md), what is featured, or what a search returns.
Pinning something copies nothing and changes nothing about the original.

Go to **Components → Collections**. Three submenu links sit at the top:

- **Collections** — the scrapbooks themselves.
- **Posts** — one pinning of an item into a collection.
- **Items** — the underlying pieces of content that get pinned.

Every screen needs `core.manage` on `com_collections`.

## The data model

Three tables, three screens:

| Screen | What a row is |
|---|---|
| **Items** | A piece of content: a title, a description, a URL, tags, and any files or links attached to it |
| **Posts** | One item placed in one collection. The same item can be posted to many collections; only the first post is the *original* |
| **Collections** | A named board belonging to a member or a group |

Deleting a collection deletes its posts. Deleting an item deletes every post
of that item, along with its votes, comments and attached files. Deleting a
post leaves the item alone.

> **Warning:** Deleting an item reaches into other people's boards. The same
> item can be posted to many collections, and removing it removes it from
> all of them, with the comments and votes those members left. If a single
> board is the problem, delete the post, not the item.

## Collections

Use this screen to take a board off the site. Setting **State** to
unpublished is the reversible way to do it, and it is what to reach for when
a complaint arrives and you have not yet decided whether it is justified.
**Delete** is not reversible and takes the posts with it.

The list shows **ID**, **Title**, **State**, **Access**, **Owner**, and
**Posts**, all sortable. Filters above it are a **Search** box, a **State**
drop-down (*All States*, Unpublished, Published, Trashed) and an **Access**
drop-down (*Public*, *Registered*, *Private*). The count in the **Posts**
column links to the Posts screen filtered to that collection.

The **State** and **Access** cells are buttons for anyone with
`core.edit.state`. Clicking **State** toggles published and unpublished;
clicking **Access** cycles Public → Registered → Private → Public.

The toolbar carries **Publish**, **Unpublish**, **New**, **Edit**,
**Delete**, and — for `core.admin` — **Options**.

The edit form has two fieldsets:

**Details**

| Field | Notes |
|---|---|
| **Owner type** | Required. **member** or **group**. The *site* option is commented out in the template and cannot be chosen |
| **Owner ID** | Required. The numeric member or group ID. Usernames and group aliases are not accepted |
| **Title** | Required |
| **Alias** | Letters, numbers and dashes. Generated from the title when left empty |
| **Description** | Free text |
| **Layout of posts** | **Grid** or **List** |
| **How posts are sorted** | **Created date (newest to oldest)** or **Defined ordering** |

The right-hand column shows read-only counts: creator, created date, likes,
posts, and followers.

**Publishing** holds **State** and **Access**.

> **Note:** **Owner ID** is not validated against anything. Typing an ID that
> does not exist saves a collection nobody can reach.

## Posts

A post joins an item to a collection. The list shows **ID**, **Description**,
**Posted**, **Poster**, **Item**, **Collection**, and **Original**. Arriving
from a collection's post count filters the list and hides the **Collection**
column.

The edit form asks for the **Item ID** and the **Collection ID** — both
numeric and both required — a **Description**, and, under **Publishing**, the
**Original** flag. Use it to move a post between collections or to correct a
mis-typed item; it is not a way to compose new content.

## Items

Items are where a correction usually belongs. Suppose a member pinned a
dataset and typed a description that names the wrong principal
investigator: fixing it here fixes it in every board the item appears in,
and leaves each member's board otherwise untouched.

The list shows **ID**, **Description**, **Created**, **Creator**, **Type**,
and **Posts**. Filters are a **Search** box and a **Filter by type**
drop-down built from the types actually present in the table.

To edit an item:

1. Go to **Components → Collections** and open the **Items** screen.
2. Find the item. The **Description** column holds the link — the first 75
   characters of the item's description, or *(none)* when it has none.
3. Click it, or tick the row and press **Edit**.
4. Change what you need and press **Save & Close**.

The edit form carries **Title**, **URL**, **Description**, and **Tags**, an
attachment area that takes files and links, a read-only panel of ID, type,
type ID, creator, created and modified dates, a **Publishing** fieldset with
**State** and **Access**, and a list of every post that references the item.

## The Collect button

Users do not pin content from the administrator interface. They use the
`mod_collect` module, which draws a **Collect** button on a content page and
posts the page into one of the user's collections.

The button is the only way anyone collects anything, so a hub that has
enabled the component and sees no collections being made has usually not
placed the module.

The install enables the `mod_collect` extension but creates no module
instance for it, so on a stock hub the button appears nowhere. To place it:

1. Go to **Extensions → Modules**.
2. Press **New** and choose **mod_collect**.
3. Give it a **Position** that the template renders on content pages, set
   **Status** to **Published**, and select **Save & Close**.

The module renders nothing for a guest, and nothing on a page it has no
adapter for. The nine content types it can collect are:

| Type | Collectible on |
|---|---|
| Blog | A single blog entry |
| Content | A single article |
| Courses | A course page |
| Forum | A single thread |
| Knowledge base | A single article |
| Publications | A single publication |
| Resources | A single resource |
| Wiki | A wiki page |
| Wishlist | A single wish |

Collecting stores a post with a link back to the original, so the original
content is untouched.

## Options

Press **Options** in the toolbar of the **Collections** screen. Two settings,
both listed in the
[generated parameter reference](../../reference/configuration/components/collections.md):

- **Upload path** — where files attached to items are stored. Default
  `/site/collections`. Leave it alone unless you are moving the hub's file
  storage; changing it does not move the files already there.
- **Allow comments** — whether users may comment on posts. Default **Yes**,
  which is sensible for a small hub and the first thing to turn off if
  collections start attracting spam. Existing comments stay in the database;
  they stop being displayed.
