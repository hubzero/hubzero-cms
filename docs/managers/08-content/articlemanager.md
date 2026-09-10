<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/content/articlemanager
source-id: 3368
modified: 2014-10-10
imported: 2026-09-09
-->
# Article Manager

The Article Manager lists every article on the hub and opens the editor for
one. Reach it at **Content → Article Manager**, or directly at
`/administrator/index.php?option=com_content`. The screen is titled **Article
Manager: Articles**, and carries a submenu with two entries, **Articles** and
**Categories**.

## The article list

Seven filters sit above the list, and a search box.

| Filter | What it does |
|---|---|
| **Filter:** | Matches the search text against the article title or alias. |
| **- Select Status -** | Published, Unpublished, Archived, Trashed, or All. |
| **- Select Category -** | One category. Articles in its children are not included. |
| **- Select Access -** | One access level. |
| **- Select Max Levels -** | Nothing. See the warning below. |
| **- Select Author -** | Nothing. |
| **- Select Language -** | Nothing. |

> **Warning:** Only the search box and the Status, Category, and Access
> filters work. The controller reads **Max Levels**, **Author**, and
> **Language** from the request and stores them, but never applies them to
> the query; the Author and Max Levels menus are also handed empty option
> lists, so they have nothing in them to pick. The search box's placeholder
> offers an `ID:` prefix that the list does not implement either.

With no status filter set, the list shows everything except trashed
articles. To see trashed articles, set **- Select Status -** to **Trashed**.

The columns are **Title** (with the alias beneath it), **Status**,
**Category**, **Ordering**, **Access**, **Created by**, **Date**,
**Language**, and **ID**. Every column heading except Ordering sorts. Sort by
**Ordering** to enable the up and down arrows and the ordering boxes;
ordering is kept per category, so the list is grouped by category while you
reorder it.

Selecting the checkboxes of one or more articles enables the toolbar
buttons:

| Button | What it does |
|---|---|
| **New** | Opens an empty article. |
| **Edit** | Opens the selected article. Clicking the title does the same. |
| **Publish** / **Unpublish** | Sets the state to Published or Unpublished. |
| **Archive** | Sets the state to Archived. |
| **Check In** | Releases an article left checked out by someone else. |
| **Delete** | Sets the state to Trashed. It does not delete anything. |
| **Options** | The component's configuration. |

Each button skips articles you lack `core.edit.state` on and reports the ones
it skipped.

### Batch processing

Below the list, administrators who hold **Create**, **Edit**, and **Edit
State** on `com_content` get a **Batch process the selected articles**
panel. It sets the access level and the language of the checked articles,
and moves or copies them into another category. Choosing a target category
is mandatory — the batch is refused without one, even if all you wanted to
change was the access level. Copying leaves the originals alone and applies
the other changes to the copies.

## The editor

**New** or **Edit** opens **Article Manager: Add New Article** or **Article
Manager: Edit Article**. The toolbar is **Save**, **Save & Close**, **Save as
Copy**, **Save & New**, **Cancel**, and **Help**. **Save as Copy** and
**Save & New** both blank the alias, reset the created and start-publishing
dates to now, and clear the finish-publishing date.

The main column holds:

| Field | Notes |
|---|---|
| **Title** | Required. |
| **Alias** | The article's own slug. Leave it blank and it is generated from the title. |
| **Category** | Required. Lists only the categories you may create in. A new article defaults to **Uncategorised**. |
| **Status** | Published, Unpublished, Archived, or Trashed. Read-only without `core.edit.state`. |
| **Access** | The viewing level. A stock hub ships three: **Public**, **Registered**, and **Special**. See [Access levels](../06-users/07-accesslevels.md). |
| **Featured** | Yes or No. See the warning below. |
| **Language** | **All**, or one installed content language. |
| **Article Text** | The body, in the site's default editor. |

The right column shows the article's ID, who created it and when, and who
last modified it and when, above a stack of collapsible panels:

| Panel | Holds |
|---|---|
| **Publishing Options** | **Start Publishing** and **Finish Publishing**, plus **Revision** and **Hits** once the article has them. Both dates are shown in your own time zone, and the abbreviation is printed next to the label. |
| **Article Options** | Per-article overrides for everything on the component's **Articles** options — Show Title, Show Author, Show Hits, and the rest. Each defaults to **Use Global**. |
| **Key Reference** | A free-text field for tying the article to a record in another system. |
| **Configure Edit Screen** | Only for administrators. Turns the Publishing Options and Article Options panels on or off for everyone, and turns on the **Images and links** panel. |
| **Images and links** | Intro and full-article images with alt text, captions and float, and three arbitrary links. Hidden unless **Configure Edit Screen** turns it on. |
| **Metadata Options** | Meta description, keywords, robots, author, and content rights. |

Administrators also get a **Permissions** block across the foot of the form,
setting Delete, Edit, and Edit State for this one article.

Opening an article checks it out to you. Leaving with **Save & Close** or
**Cancel** checks it back in; closing the browser tab does not, which is what
**Check In** on the list is for.

> **Warning:** The **Featured** field writes to the article's `featured`
> column, but the site's **Featured Articles** page reads a separate
> `#__content_frontpage` table that nothing in the administrator interface
> writes to. Setting **Featured** to **Yes** therefore has no visible effect.
> The administrator's own Featured Articles screen is unreachable — its menu
> entry is commented out, and the code behind it refers to a model class that
> does not exist. Both are recorded with the project.

## The publishing workflow

An article has one of four states.

| State | Meaning |
|---|---|
| **Published** | Visible on the site, subject to the access level and the publishing dates. |
| **Unpublished** | Not visible. |
| **Archived** | Not in ordinary listings; reachable through an **Archived Articles** menu item. |
| **Trashed** | Not visible, and hidden from the article list until you filter for it. |

**Start Publishing** and **Finish Publishing** narrow a published article to
a window. An article published with a start date in the future stays hidden
until then.

To recover a trashed article, filter the list by **Trashed**, open the
article, and set **Status** back to **Published**.

> **Note:** Hubzero 2.4 has no permanent delete for articles. The toolbar
> button reads **Delete** but runs the trash task, and no controller task
> removes the row. An article set to Trashed stays in the database until
> someone removes it outside the interface.

## Categories

Articles are filed in categories, and categories are what the URL of an
article without a menu item is built from. They are managed by a separate
component, `com_categories`, which serves every extension that has
categories; the Article Manager's **Categories** submenu entry and
**Content → Category Manager** both open it filtered to `com_content`.

The list is titled **Category Manager: Articles** and behaves like the
article list: a search box over the title and description, and filters for
**Max Levels**, **Status**, **Access**, and **Language** — all four of which
work here. Categories nest, so the list is a tree ordered by position, and
the same **New**, **Edit**, **Publish**, **Unpublish**, **Archive**, **Check
In**, **Delete**, and **Options** toolbar applies. As with articles,
**Delete** trashes rather than deletes.

A category has a **Title**, an **Alias**, a **Parent Item**, a **Status**, an
**Access** level, a **Language**, and a **Description**, plus panels for
options, metadata, and — for administrators — permissions. The permissions
you set on a category are inherited by the articles in it.

A stock hub installs an **Uncategorised** category, which new articles
default to. An article in **Uncategorised** is reachable at
`/{article-alias}` with nothing in front of it; see [URLs](urls.md).

> **Note:** An article belongs to exactly one category. There is no way to
> file one under two. If a page needs to appear in two places, point two menu
> items at the same article rather than duplicating it.

## Options

**Options** on either list opens the `com_content` configuration: the site
defaults for article, category, blog, and list layouts, the integration
settings, and the component-wide permissions. Every parameter is listed in
the
[Content configuration reference](../../reference/configuration/components/content.md).

On a single-article layout the precedence is straightforward: a setting on
the article wins over the same setting on the menu item, which wins over the
global here. A setting left at **Use Global** falls through to the next level
up.

Blog and featured layouts invert that. There the menu item's setting wins,
and the article's value is used only where the menu item's setting is
**Use Article**; if the article has nothing set, the global is used.
