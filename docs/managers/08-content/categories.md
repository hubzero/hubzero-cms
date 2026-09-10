<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
-->
# Categories

Most hubs need one or two article categories, and a good many need none. A
category earns its place when a group of pages belongs together — an About
section with an overview, a contact page and an acknowledgements page — or
when you want the pages' addresses to share a prefix. A single About page
reached through a menu item needs no category at all: leave it in
**Uncategorised** and nothing is lost. Creating a category tree before there
is content to put in it is the commonest way a manager makes a small hub feel
complicated.

There is one thing categories do that nothing else does, and it is worth the
trouble on a bigger hub: a category's state and access level apply to
everything filed in it. Unpublishing one category takes a whole branch of the
site down in a single save, and republishing it brings every page back in
whatever state it was already in. That is covered under [States and
permissions](#states-and-permissions) below.

The Category Manager is one screen that several components borrow. Articles,
user notes, knowledge base articles and events all file their content in
categories, and all four open the same component, `com_categories`, to
create and edit them. This chapter is for the administrator who has arrived
at that screen from any of those places and wants to know what it does, what
the categories they are looking at belong to, and why the screen looks
identical no matter where they came from.

`com_categories` has no entry of its own in the **Components** menu, and
that is deliberate — the migration that registers the component says so and
passes `false` where it would otherwise ask for a menu item. You always
reach the Category Manager through the component whose categories you want.

## One manager, four extensions

Every category row carries an `extension` column naming the component it
belongs to. The screen is scoped by an `extension` parameter in the URL that
matches it. Four components ship with a hub and open the manager:

| Extension | Where you open it from | What the categories are for |
|---|---|---|
| `com_content` | **Content → Category Manager**, the **Categories** submenu entry on the [Article Manager](articlemanager.md), and the **Category Manager** icon on the control panel | Filing articles, and building their URLs |
| `com_members` | **Users → User Note Categories**, and **Note Categories** under **Users → Members → Notes** | Filing [user notes](../06-users/user-notes.md) |
| `com_kb` | The **Categories** submenu entry in the Knowledge Base | Grouping knowledge base articles |
| `com_events` | The **Categories** submenu entry in Events | Grouping events |

Each of those links is the same URL with one word changed:

```text
/administrator/index.php?option=com_categories&extension=com_content
/administrator/index.php?option=com_categories&extension=com_members
/administrator/index.php?option=com_categories&extension=com_kb
/administrator/index.php?option=com_categories&extension=com_events
```

The categories are **not** shared between them. A category created under
`com_content` is invisible to notes, to the knowledge base and to events,
and the list, the **Parent** menu and the batch panel are all filtered to
one extension at a time. What is shared is the screen, the database table
and the tree they all hang from.

The page title says which extension you are in: **Category Manager:
Articles** for `com_content`, **Category Manager: Members**, **Category
Manager: Knowledge Base**, **Category Manager: Events**. The name comes from
the owning component's language file, so if that file is not loaded the
title falls back to the raw `com_…` string.

> **Note:** Note categories used to be scoped to `com_users`. Two migrations
> rename them to `com_members`, and nothing in the current code reads the old
> value, so a database upgraded from a version that predates the rename
> without running them would show no note categories at all.

> **Note:** The code allows a second, finer scope — an extension of the form
> `com_something.section` — and passes the section on to the owning
> component when it rebuilds the submenu. Nothing in the tree uses it. Every
> caller passes a plain component name.

### The submenu disappears

When you open the Category Manager, the owning component's submenu is not
drawn by that component any more, so `com_categories` tries to rebuild it:
it looks for `admin/helpers/<name>.php` in the owning component, expects a
class called `<Name>Helper` with an `addSubmenu` method, and calls it.

Only `com_members` has one. Open the Category Manager from Articles, the
Knowledge Base or Events and you lose that component's submenu until you
navigate back to it; open **User Note Categories** and the Users submenu and
the **Notes / Note Categories** sub-submenu are both still there.

### Without the extension parameter

The screen remembers the last extension you looked at, per user, in your
session. Reaching `index.php?option=com_categories` with no `extension`
therefore usually shows the extension you were in last, and every link on
the page carries it forward.

On a fresh session, with nothing remembered, the screen degrades in three
ways at once. The permission check that guards it asks for `core.manage` on
an empty extension name, which only a Super User passes, so everyone else
gets a 404. Anyone who does get in finds the list applying no extension
filter at all — every extension's categories, mixed together in one tree,
with no column to tell them apart — and the **- Select Max Levels -** menu
empty. Open a category from there and the edit form falls back to
`com_content`, so saving would file it under Articles.

Always arrive through one of the menu entries above.

## Nesting

Categories nest to any depth. The table stores the tree as a nested set:
`parent_id`, `lft` and `rgt` bounds, a `level` number, and a `path` of
slash-separated aliases.

All four extensions live in **one** tree. A hub installs a single root row —
id 1, titled `ROOT`, alias `root`, `level` 0, extension `system` — and every
category in every extension is a descendant of it. A top-level category is
therefore `level` 1, and the list indents each row by `level - 1`.

Nothing enforces a maximum depth. `level` is an unsigned integer and no
check anywhere counts it. The real limit is `path`, a `varchar(255)`: the
aliases of a category and all its ancestors are joined with `/` and stored
there, and once that overflows 255 characters the path is silently
truncated. For `com_content` the path is also the URL, so keep aliases
short — see [URLs](urls.md).

The **Parent** menu on the form offers every category in the same extension
plus **- No parent -**, and excludes the category being edited together with
all of its descendants, so you cannot make a category its own child. To swap
a parent and a child, move the child up rather than the parent down.

Saving any category rebuilds the tree from the root — every `lft`, `rgt`,
`level` and `path` in the table, across all four extensions, is rewritten.
This is normally invisible, but it is why a save on a large hub is not
instant.

## The category list

Above the list are a search box and four menus.

| Filter | What it does |
|---|---|
| **Filter:** | Matches the text against the category title or description. |
| **- Select Max Levels -** | Shows only categories nested no deeper than the level you pick. The menu lists the depths that actually exist. |
| **- Select Status -** | Published, Unpublished, Archived, Trashed, or All. |
| **- Select Access -** | One access level. |
| **- Select Language -** | Nothing. See the warning below. |

> **Warning:** The **- Select Language -** menu does not filter. The
> controller reads it from the request and stores it in your session, but it
> never reaches the query, so the list is unchanged whatever you pick. The
> other three menus and the search box all work.

With no status filter set, the list shows everything except trashed
categories. To see trashed categories, set **- Select Status -** to
**Trashed**.

The columns are **Title**, **Status**, **Ordering**, **Access**,
**Language** and **ID**. The alias sits under the title, with the category's
note after it when it has one, and the full path is the title line's tooltip.
Hovering the **ID** shows the row's `lft` and `rgt` bounds.

> **Warning:** The column headed **Access** does not show the access level.
> It prints the category's nesting level, and its sort link sorts by title.
> Nothing in the list shows what access level a category has; open the
> category to see it.

Sort by **Ordering** to enable the up and down arrows and the ordering
boxes. Ordering is kept among siblings — the boxes number each category
within its parent, not within the whole list — and the arrows only appear
where there is a sibling to swap with on the page you are looking at.

Selecting one or more checkboxes enables the toolbar:

| Button | What it does |
|---|---|
| **New** | Opens an empty category in the current extension. |
| **Edit** | Opens the selected category. Selecting the title does the same. |
| **Publish** / **Unpublish** | Sets the state to Published or Unpublished. |
| **Archive** | Sets the state to Archived. |
| **Check In** | Clears the checked-out flag on the selected categories. |
| **Delete** | Sets the state to Trashed. It does not delete anything. |
| **Options** | The **owning component's** configuration, not this one's. |

The state buttons skip any category you lack `core.edit.state` on. They do
say so, but the message is an untranslated developer string — *Can't change
state drop 0* — naming the row's position in the list rather than the
category.

> **Note:** As with articles, Hubzero 2.4 has no permanent delete for a
> category. The toolbar button reads **Delete** and runs the trash task, and
> no controller task removes the row. See [States, trash and
> check-in](states.md). A **Content - Categories** plugin exists to refuse
> the deletion of a category that still has items in it, but nothing ever
> fires the event it listens for, and it only knows how to count articles in
> any case.

> **Note:** **Options** opens the configuration of the extension you are
> scoped to — `com_content`'s options from the article categories, and so
> on. `com_categories` has a `config.xml` of its own, but it declares an
> empty fieldset, so the component has no settings to configure.

### Batch processing

Batch is for the day a section is reorganised: twenty pages that need moving
under a new parent, or a set of pages that should all become **Registered**
at once. It is not a small convenience — moving a category moves its children
with it, and a copy duplicates a whole branch, so check what you have ticked
before you run it. There is no undo; reversing a batch means running another
one back the other way.

Below the list, an administrator holding **Create**, **Edit** and **Edit
State** on the extension gets a **Batch process the selected categories**
panel. It sets the access level and the language of the checked categories,
and moves or copies them — with all of their children — under another
category. Copying leaves the originals alone and applies the other changes
to the copies. A target category is mandatory: the batch is refused with
*No target category selected for batch processing* without one, even if all
you wanted to change was the access level.

> **Warning:** The **Select Category for Move/Copy** control is only drawn
> when **- Select Status -** is set to **Published**, **Unpublished** or
> **Archived**. With the filter at its default, or on **All** or
> **Trashed**, the control is missing — and because a target is mandatory,
> the whole panel is then unusable. Set a status filter first.

## The category form

**New** or **Edit** opens **Category Manager: Add A New Articles Category**
or **Category Manager: Edit Articles Category**, with the extension's name
in the middle. The toolbar is **Save**, **Save & Close**, **Save as Copy**,
**Save & New**, **Cancel** and **Help**.

The main column holds:

| Field | Notes |
|---|---|
| **Title** | Required. |
| **Alias** | The slug. Leave it blank and it is generated from the title: transliterated, lowercased, and reduced to letters, digits and hyphens. A title with nothing usable in it produces a timestamp instead. |
| **Parent** | **- No parent -** for a top-level category, or another category in the same extension. |
| **Status** | Published, Unpublished, Archived or Trashed. |
| **Access** | The viewing level. See [Access levels](../06-users/07-accesslevels.md). |
| **Language** | **All**, or one installed content language. |
| **Description** | Free text, in the site's default editor. |

The right column shows the **Extension**, the **ID**, and who created and
last modified the category and when, above two collapsible panels:

| Panel | Holds |
|---|---|
| **Basic Options** | **Alternative Layout**, **Image**, and **Note** — a short private label shown after the alias in the list. |
| **Metadata Options** | **Meta Description**, **Meta Keywords**, **Author** and **Robots**. |

The form is the same for every extension. Nothing lets a component add its
own fields to a category, so a note category and an article category carry
exactly the same information, including the layout and image options that
only articles use.

An administrator also gets a **Permissions** block across the foot of the
form, which sets this category's own rules and is inherited by its children
and by the items filed in it.

> **Warning:** That Permissions block always lists `com_content`'s category
> actions. The form hardcodes the component and section it asks for instead
> of taking them from the extension you are in, so editing a note, knowledge
> base or event category shows the wrong action list. The rules you save are
> still stored against the right category.

> **Note:** Opening a category does not check it out. The controller sets
> the checked-out fields on the record and then never saves them — the line
> that would is commented out. In practice no category is ever marked as
> being edited by someone else, the checked-out marker never appears in the
> list, and **Check In** has nothing to clear unless another part of the
> hub set the flag.

The form also defines a read-only **Path** field that no template renders,
so the stored path is only ever visible as the tooltip on the list's title.

## States and permissions

A category has the same four states as an article — Published, Unpublished,
Archived and Trashed — and they mean the same things. [States, trash and
check-in](states.md) covers them.

What matters here is that a category's state and access level apply to
everything inside it, not just to the category itself.

For articles this is enforced in the query. Every article listing walks the
category path from the root down to the article's own category and checks
each one:

- If **any** category on that path is not published, the article is treated
  as unpublished, whatever the article's own state says.
- If a category on that path is archived, the articles beneath it are
  treated as archived.
- The access filter is applied to the article's access level **and** to its
  category's, so an article in a **Special** category is not visible to a
  member who lacks Special, even if the article itself is **Public**.

So unpublishing a category is the way to take a whole branch off the site at
once. Nothing is changed on the items themselves, and republishing the
category brings them all back in the state they were already in.

The other three extensions do less. The knowledge base filters child
categories by state and access when it lists them, and events lists only
published categories; neither walks the whole path the way articles do. User
note categories have no state or access behaviour at all — the note list's
category filter is the only thing a note category changes, and the manager's
Status and Access controls have no visible effect on notes.

Permissions on the Category Manager are checked against the **owning
extension**, never against `com_categories`, which ships no `access.xml` of
its own:

| Check | Against | Controls |
|---|---|---|
| `core.manage` | The extension | Whether the screen opens at all. Without it you get a 404. |
| `core.create` | The extension, or a category | **New**, and which categories the **Parent** menu offers. |
| `core.edit`, `core.edit.own` | `extension.category.id` | Opening a category. |
| `core.edit.state` | `extension.category.id` | Publish, Unpublish, Archive, Delete, and whether you may move a category to a different parent. |
| `core.delete` | The extension | Showing the **Delete** button. |
| `core.admin` | The extension | The **Permissions** block and the **Options** button. |

Because the asset name is built from the extension, the same administrator
can be allowed to manage article categories and refused note categories.
Set those permissions on the owning component's **Options** screen, or per
category in the Permissions block; see [Access
groups](../06-users/06-accessgroups.md).
