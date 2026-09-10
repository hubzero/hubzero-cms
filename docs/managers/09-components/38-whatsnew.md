<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
-->
# What's New

What's New answers one question — *what has been added to this hub lately?* —
by asking each of its plugins for the items in their component created inside
a time period, then listing them by category at `/whatsnew`. It also publishes
each of those categories as an RSS feed, which is where the **Feed** buttons a
visitor sees actually lead. The visitor's side is described in
[Search](../../users/24-search.md#what-is-this-feed-button).

There is no administrator screen. The component ships no admin controllers and
no admin views, and its install migration deliberately removes it from the
**Components** menu. Everything a manager decides about it is decided
somewhere else — in the Plugin Manager, in a menu item, or in a module — and
those three places are what this chapter covers.

## What it aggregates

Six plugins, in the `whatsnew` folder of **Extensions > Plugins**:

| Plugin | Category on the page | What it lists |
|---|---|---|
| Whatsnew - Content | Articles | Published articles from the Content component. |
| Whatsnew - Events | Events | Events from the hub calendar. |
| Whatsnew - Knowledge Base | Knowledge Base | Published knowledge base articles. |
| Whatsnew - Publications | Publications | Published publication versions. |
| Whatsnew - Resources | Resources | Published resources, split into one sub-category per major resource type — tools, datasets, presentations and so on, exactly as the resource type list defines them. |
| Whatsnew - Wiki | Topic pages | Wiki pages. |

None of them has any parameters. The only decision is whether each is enabled;
disable one and its category disappears from the page, its counts from the
sidebar, and its feed with it. Enabling all six and enabling none are the two
ends of the range, and there is nothing in between to tune.

Nothing else is aggregated. Blogs, forum posts, questions and answers, groups,
projects, collections, citations and wishlist items have no What's New plugin
and never appear, however new they are.

Each plugin applies its own component's access rules, so two visitors can see
different counts on the same page.

## The page

`/whatsnew` shows a **Category** list down the left with a count beside each
one, and the matching items on the right. Selecting a category restricts the
page to it; the **Resources** category expands into its sub-categories. A
**Time period** select and a **Go** button sit above.

With no category chosen, the page shows the top five items in each category
and a **See more results ›** link. Choose a category and it pages properly,
using the hub's list length.

The URL carries the period as its first segment — `/whatsnew/month` — and a
category is expressed as a prefix on it: `/whatsnew/resources:month`,
`/whatsnew/tools:month`. That prefixed form is the string a menu item and the
module both take.

## The period

| Value | Covers |
|---|---|
| `week` | The past week, ending now. |
| `month` | The past month, ending now. Used when nothing else is given. |
| `quarter` | The past three months. |
| `year` | The past year. |
| `2024` | Fiscal year 2024: 1 September 2023 to 31 August 2024. |
| `c_2024` | Calendar year 2024. |

The **Time period** select offers the four rolling windows, then a fiscal year
and a calendar year entry for each year back to 2002. A fiscal year appears in
the list only once 1 October of that year has passed, so the current fiscal
year is offered from October onwards.

The fiscal year runs September to August, which is fixed in the code and
cannot be changed. So is the 2002 floor on the year lists.

The period a visitor arrives on comes, in order, from the URL, then from the
active menu item's setting, then from the built-in default of `month`.

## What a manager configures

Three things, none of them in this component's own screens.

**Which plugins run.** **Extensions > Plugins**, filtered to the `whatsnew`
folder. See [Plugins](../10-extensions/03-plugins.md).

**A menu item.** Create one of type **What's New > Display results**; see
[Menus](../07-menus.md). Its **Cat/Period** field takes exactly the string
described above — `month`, `year`, `c_2023`, or a category prefix such as
`resources:quarter` — and becomes the page's starting period. Leave it blank
for the past month across all categories.

**The What's New module.** `mod_whatsnew` puts a short list of recent items in
a template position. Its parameters:

| Parameter | Notes |
|---|---|
| CSS ID | An id for the module's wrapper, for styling. |
| Number of items | How many items to list. Five by default. |
| Feed link | Show or hide the RSS link in the module. |
| Category and period | The same prefixed string as the menu item. `resources:month` by default. |
| Tags | Show or hide each item's tags. |
| Cache, Cache time | Off by default; cache time is in minutes. |

Place it under **Extensions > Modules**; see
[Modules](../10-extensions/01-modules.md).

## Feeds

Every category heading on the page carries a **feed** link, and that link is
the component's RSS output for that category over the current period:
`/whatsnew/resources:month/feed.rss`. The feed carries each item's title,
link, date, category and author, with the description truncated to 300
characters, and it is limited the same way the page is — five items per
category when no category is named, the hub's list length when one is.

These are the feeds a visitor is offered from the search results page. They
are ordinary RSS and need no configuration; if a category's plugin is
disabled, its feed stops existing along with its heading.

## API

The component also answers `/api/whatsnew`, taking the same period and
category strings. See the [API reference](../../reference/api/whatsnew.md).

## Options

There are none. The component has no `config.xml`, no `access.xml` and no
Options screen. Access to `/whatsnew` is whatever access its menu item has,
and what appears on it is decided entirely by the plugins and by each item's
own access rules.
