<!--
status: rewritten
reviewed-against: 2.4-main @ 754ab96b09
reviewed: 2026-09-10
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

## What articles are for

An article is a page of the hub's own prose, written by whoever runs the hub:
the About page, the acceptable use policy, the instructions for citing the
hub's data, a notice about next month's maintenance window. If you type it
once and visitors only read it, it is an article.

Most of a hub's content is not an article, and a manager who does not know
that ends up building the whole hub out of them. Tools, datasets and their
documentation are resources. Formally released datasets are publications.
Pages members edit themselves are wiki pages. A group's own pages belong to
the group. Questions and answers, forum threads, blog entries, support
tickets, knowledge base entries and courses each have their own component,
their own permissions, and their own chapter in
[Components](../09-components/README.md). Those components hold far more than
text — authorship, versions, tags, ratings, review — and an article holds
none of it.

> **Note:** An article has no address and no place in the navigation. Both
> come from a menu item that points at it, and nothing you set on the article
> puts it in a menu. See [URLs](urls.md).

## Worked example: an "About this hub" page

Nearly every hub needs a page saying who runs it, what it is for, and who to
write to. It is the first article most managers create, and it exercises
everything in this chapter. The rest of the chapter refers back to it.

1. **Decide whether it needs a category.** A category does two jobs: it groups
   articles in the list and the batch panel, and — for an article that no menu
   item points at — it supplies the front of the URL. A single About page
   reached through a menu item needs neither, so leaving it in
   **Uncategorised** is a defensible answer. Create a category when you can
   already see the second and third article coming: an About section that will
   also hold a contact page and an acknowledgements page. To create one, go to
   **Content → Category Manager → New**, set **Title** to `About`, leave
   **Alias** blank so it is generated as `about`, leave **Parent Item** at
   **- No parent -**, and select **Save & Close**. This example uses that
   category.

2. **Write the article.** Go to **Content → Article Manager → New**. Set
   **Title** to `About this hub`. Leave **Alias** blank; it is generated as
   `about-this-hub`. Set **Category** to **About**. Most of the rest arrives
   with the value you want: **Status** is **Published**, **Language** is
   **All**, **Start Publishing** is now, and every panel down the right is set
   to **Use Global**. Check that **Access** reads **Public**, and fill in
   **Meta Description** under **Metadata Options** — the sentence a search
   engine prints under the page title. Leave everything else alone.

3. **Type the body.** **Article Text** opens in whichever editor plugin the
   **Default Editor** setting names, at **Site → Global Configuration** on the
   **Site** tab. The installer writes `ckeditor` into the hub's configuration,
   so on a stock hub you get **Editor - CKEditor**: a **Format** menu for
   headings, the usual bold and italic, lists, blockquote and alignment,
   **Link**, **Image** and **Table**, and find and replace. There is no
   **Source** button until someone turns **Source View** on at **Extensions →
   Plugin Manager → Editor - CKEditor**. Write the page as prose and take
   headings from the **Format** menu rather than setting fonts and colours by
   hand; the template supplies the appearance. Under the text area sit four
   more buttons, one per enabled button plugin: **Article**, which inserts a
   link to another article; **Image**, which opens the shared library from
   **Content → Media Manager** and can upload into it; **Page Break**; and
   **Read More**, which marks where the intro text ends. Use **Image** for
   pictures — the **Image** button in CKEditor's own toolbar is a different
   dialog that only takes a URL, because a stock hub gives it no server to
   browse. Select **Save & Close**.

4. **Publish it.** The article is published already, because **Status**
   defaults to **Published**; the list shows it as such in the **Status**
   column. **Status** and **Access** are different questions. **Status** decides
   whether the article exists for the site at all: an unpublished article
   answers 404, even to someone following a menu item straight at it. **Access**
   decides who may read the body of an article that does exist. A published
   article whose **Access** is **Registered** still renders its title to a
   guest; only the body is withheld, and the guest is shown the intro text and
   a link to log in only where **Show Unauthorised Links** is on. The
   category's access level counts too — a **Public** article filed in a
   **Registered** category is not readable by a guest. Leave both at
   **Published** and **Public** for an About page.

5. **Make it reachable.** Publishing does not put the page anywhere. Go to
   **Menus**, open the menu you want it in, and select **New**. Set **Menu
   Item Type** to **Articles → Single Article**, choose **About this hub** in
   the **Select Article** field, set **Menu Title** to `About`, and leave
   **Alias** blank so it is generated as `about`. Save, and the page answers
   at `/about`. [Menus](../07-menus.md) covers the rest of that form, nesting
   items to build an About section, and showing the menu with a module.

6. **Check it on the site.** Open `/about` in a browser where you are not
   logged in to the hub; that is what a visitor sees. The menu item's route is
   the address to publish and to link to. Had you skipped the menu item, the
   article would have answered at its category path instead,
   `/about/about-this-hub`. A 404 means the article is not published, the menu
   item is not published, or the menu item points at something else.
   [URLs](urls.md) explains how the address is decided.

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

To find the About this hub article from the worked example, type `about` in
the search box — it matches the title and the alias — or set **- Select
Category -** to **About**. An article filed in a category nested under About
does not appear under that filter.

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
| **Article Text** | The body, in the site's default editor. On a stock hub that is **Editor - CKEditor**; see step 3 of the worked example. |

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

## Featured articles

The **Featured** field on the article, and the **Featured Articles** menu item
type that lists featured articles, look like the way to choose what appears on
a hub's front page. They are not. As the warning above records, the field
writes one place and the site's featured page reads another, and nothing fills
the table it reads; the page's query joins that table, so it matches no
articles at all. A **Featured Articles** page is empty whatever **Featured**
is set to, and setting **Featured** to **Yes** does nothing you can see.

Set the home page with a menu item instead: open the item you want as the
front page and set **Default Page** to **Yes**. A **Single Article** item
pointed at the About this hub article is a serviceable front page for a new
hub. See [Menus](../07-menus.md).

## The publishing workflow

An article has one of four states.

| State | Meaning |
|---|---|
| **Published** | Visible on the site, subject to the access level and the publishing dates. |
| **Unpublished** | Not visible. |
| **Archived** | Not in ordinary listings; reachable through an **Archived Articles** menu item. |
| **Trashed** | Not visible, and hidden from the article list until you filter for it. |

Unpublish the About this hub article while you rewrite it and the menu item
stays in the menu, but a visitor who follows it gets a 404 — the site refuses
an article that is not published, whether it was reached by menu item or by
category path. You keep seeing it, because anyone holding `core.edit` or
`core.edit.state` on `com_content` is served unpublished articles as well.
Archived articles are served to everyone: archiving takes the article out of
the listings, but a menu item pointed straight at it still opens it.

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

The **About** category from the worked example is as plain as a category
gets: a title, an alias generated from it, no parent. Its alias is what puts
the article at `/about/about-this-hub` when no menu item points at it, and any
permissions set on it are inherited by every article filed there.

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
up. So setting **Show Author** to **Hide** in the About this hub article's
**Article Options** panel drops the byline from `/about`, whatever the menu
item and the global say.

Blog and featured layouts invert that. There the menu item's setting wins,
and the article's value is used only where the menu item's setting is
**Use Article**; if the article has nothing set, the global is used.
