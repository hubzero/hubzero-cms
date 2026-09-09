# List / Browse

Filterable, paginated collection of items. Used by blog entries, resources,
members, publications, support tickets, and most other components.

## When to Use

- Displaying a searchable/filterable collection of records
- Browse pages with sorting, categories, or tag filters
- Search results

## daisyUI Components Used

- [Tabs](https://daisyui.com/components/tab/) — filter bar
- [List](https://daisyui.com/components/list/) — item rows
- [Badge](https://daisyui.com/components/badge/) — status indicators
- [Join](https://daisyui.com/components/join/) — pagination
- [Card](https://daisyui.com/components/card/) — sidebar widgets
- [Input](https://daisyui.com/components/input/) — search field

## Global Components

Use these [global blade components](../reference/global-components.md) for browse pages:

- **`<x-page-container>`** — wraps page header + body + sidebar layout
- **`<x-empty-state>`** — empty/no-results placeholder
- **`<x-card-grid>`** — responsive card layouts (when using cards instead of lists)

## New Markup

```blade
<x-page-container title="Blog">
    @slot('actions')
        <a class="btn" href="/blog/feed.rss">Feed</a>
    @endslot

    @slot('sidebar')
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-sm">Popular</h2>
                <ul class="menu menu-sm p-0">
                    <li><a href="...">Popular Entry Title</a></li>
                </ul>
            </div>
        </div>
    @endslot

    <!-- Search -->
    <form action="" method="get" role="search" class="mb-6">
        <label for="search-field" class="sr-only">Search entries</label>
        <input type="search" id="search-field" name="search"
               class="input input-bordered w-full"
               placeholder="Search entries..." aria-label="Search entries" />
    </form>

    <!-- Section heading -->
    <h2 class="text-lg font-semibold mb-4">Latest Entries</h2>

    <!-- Item list -->
    <ul class="list bg-base-100 rounded-box shadow-sm" aria-label="Blog entries">
        <li class="list-row">
            <div class="list-col-grow">
                <h3 class="text-base font-semibold">
                    <a class="link link-hover text-primary" href="...">Entry Title</a>
                </h3>
                <div class="flex items-baseline gap-x-3 text-sm text-base-content/60">
                    <time datetime="2026-01-15">January 15, 2026</time>
                    <span>by <a class="link link-hover" href="...">John Doe</a></span>
                </div>
            </div>
            <div>
                <span class="badge badge-success badge-sm">Published</span>
            </div>
        </li>
    </ul>

    <!-- Pagination -->
    <nav aria-label="Page navigation" class="flex justify-center mt-8">
        <div class="join">
            <a class="join-item btn btn-sm btn-active" aria-current="page">1</a>
            <a class="join-item btn btn-sm" href="?start=10">2</a>
        </div>
    </nav>

    <!-- Empty state (when no results) -->
    <x-empty-state
        title="No entries found"
        message="No entries match your criteria."
    >
        <a class="btn btn-ghost" href="...">Clear filters</a>
    </x-empty-state>
</x-page-container>
```

## Structure Reference

| Element | Class | Purpose |
|---------|-------|---------|
| Page wrapper | `<x-page-container>` | Header + body + sidebar (global component) |
| Empty state | `<x-empty-state>` | No-results placeholder (global component) |
| Card layout | `<x-card-grid>` | Card grid alternative to list (global component) |
| Search input | `.input .input-bordered` | Full-text search |
| Filter bar | `.tabs .tabs-border` | Category/status tabs |
| Item list | `.list` | Container for rows |
| Single item | `.list-row` | One entry in the list |
| Item content | `.list-col-grow` | Flexible content area |
| Status pill | `.badge .badge-success` | Item state indicator |
| Pagination | `.join` + `.join-item .btn` | Page navigation |
| Sidebar block | `.card .card-body` | Sidebar widget |
| Sidebar links | `.menu .menu-sm` | Link list in sidebar |

## Heading Hierarchy (WCAG 2.4.6)

`<x-page-container>` renders the `<h1>` inside `.page-header`. Every page
has exactly one `<h1>`. The site name in the navbar is **not** an `<h1>`.

```
h1  — Page title (rendered by <x-page-container>)
├── h2  — Section heading ("Latest Entries", "Search results for...")
│   └── h3  — Individual entry titles
├── h2  — Sidebar: "Archive by Year"
└── h2  — Sidebar: "Popular Entries"
```

## Design Notes

- **Metadata alignment**: Use `items-baseline` (not `items-center`) on the
  date/author/comments flex row. Inside a daisyUI `.list-row` grid, `items-center`
  causes the comments link to appear vertically shifted relative to the date text.
  `items-baseline` aligns all text to the same typographic baseline.

- **Page header actions**: Buttons in `.page-header-actions` can include inline
  SVG icons (e.g., an RSS feed icon). The icon is part of the view markup since
  it's content-specific — the template CSS only controls the button's size and
  colors.

## Pattern Variants

### Table-based browse (com_tags)

When the data is inherently tabular (e.g., tag name + aliases + actions), use a
daisyUI `table table-zebra` instead of `.list` rows. The browse still uses
`<x-page-container>` with a sidebar card for explanatory text, sort tabs, and
search — the only difference is the content area uses `<table>` markup.

See `com_tags/site/views/tags/tmpl/browse.blade.php` for the reference
implementation.

### Article content browse (com_content)

com_content has five browse/list shapes spread across four view directories.
All use `<x-page-container>` and `<x-empty-state>`.

**Categories index** (`categories/default.blade.php`) — A flat list of top-level
categories using `.list` / `.list-row` with linked titles, optional description
previews (`.line-clamp-2`), and article-count badges. Optional base description
rendered from params or parent category. No search or pagination.

**Category list** (`category/default.blade.php`) — The standard article list
within a single category. Uses `<x-search-bar>` (button mode) with hidden
fields for ordering state. Subcategories render in a `<x-sidebar-card>` sidebar
with count badges. Each article row shows title (with access-gated login link
for restricted items), display date, author, and hit count. Supports "Create
article" button for authorized users. Conditional pagination.

**Category blog** (`category/blog.blade.php`) — Mixed layout with leading
articles (full-width, rendered by an `_item` partial), intro articles (2-column
grid), and link articles (compact `menu` list in a card). Subcategories appear
below articles with descriptions and count badges. No search bar — this layout
prioritizes editorial presentation over filtering. Conditional pagination.

**Featured** (`featured/default.blade.php`) — Same leading/intro/link three-tier
layout as the category blog, without category description or subcategories. No
search bar or sidebar. Conditional pagination.

**Archive** (`archive/default.blade.php`) — Time-filtered article list with
`<x-search-bar>` (button mode) plus month/year selector dropdowns passed via
the default slot. Articles render in `.list` / `.list-row` with optional author,
category, create date, hit count, and truncated intro text. Pagination below
the list.

See `com_content/site/views/` for the reference implementations.

### Collections browse with card grid and social actions (com_collections)

com_collections has two browse views — posts and collections — sharing the same
shell: `<x-page-container>` with `<x-filter-tabs>` (posts count / collections
count / Getting Started) and `<x-search-bar>` (button mode with clear). Both
views offer a "New" action button for logged-in users.

**Posts browse** (`collections/posts.blade.php`) — Cards in a `space-y-4` stack.
Each card shows the item title (linked), parsed description, media assets
(images rendered inline), creator avatar and name, like/repost/comment counts,
and action buttons (Collect, Like, Comment — or Edit/Delete for the owner).
Guest users get login-gated action buttons.

**Collections browse** (`collections/collections.blade.php`) — Cards in a
3-column responsive `grid`. Each card shows the collection title (with privacy
badge for private collections), parsed description preview (`.line-clamp-3`),
likes/posts counts, creator avatar and name, and action buttons
(Collect/Follow/Unfollow — or Edit/Delete for the owner). Pagination below
the grid.

Both views use `<x-empty-state>` with a contextual "New" CTA for no-results.

See `com_collections/site/views/collections/tmpl/` for the reference
implementations.

### Citation browse with sidebar filters (com_citations)

The citations browse page combines `<x-search-bar>` (button mode with clear
button and hidden `task` field in default slot), affiliation filter tabs
(All / Affiliated / Non-affiliated using `tabs tabs-border`), and a
multi-faceted sidebar for type, year range, and sort controls.

**Sidebar** uses three `<x-sidebar-card>` widgets:
1. **Type filter** — `menu menu-sm` listing all citation types with `active`
   class on the selected type
2. **Year range** — a compact inline form with two text inputs (From/To) and a
   search icon button, preserving all other filter state via hidden inputs
3. **Sort** — `menu menu-sm` with sort options (Date, Title, etc.), active item
   highlighted

**Results** render in a daisyUI `.list` with `.list-row` items. Each row shows:
- Title as a primary-colored linked heading
- Author list (truncated to 100 chars), year, and journal/booktitle in a
  metadata line
- Optional DOI as a linked external reference
- Type badge and optional affiliation badge in a right-aligned column

**Result count** appears right-aligned next to the affiliation tabs.

**Pagination** uses the model's paginator with filter params injected via
`setAdditionalUrlParam()`. `<x-empty-state>` handles no-results with a
contextual "Clear" action when filters are active.

The **landing page** (`display.blade.php`) is a separate overview with
introduction cards, `<x-search-bar>` (button mode, submits to the browse URL),
and metrics tables (yearly stats by affiliation, type distribution with progress
bars).

See `com_citations/site/views/citations/tmpl/browse.blade.php` (browse) and
`com_citations/site/views/citations/tmpl/display.blade.php` (landing) for the
reference implementations.

### Raw filter/sort browse (com_answers)

The com_answers search page uses `<x-search-bar>` (with hidden fields in the
default slot for filter state) and `<x-results-heading>` for the count label.
The area tabs and sort/status selects use raw markup rather than the shared
`<x-filter-tabs>` and `<x-filter-sort>` components, because the Q&A browse has
tightly coupled filter state (area, sortby, filterby, search query) that doesn't
cleanly map to the generic component APIs. Sidebar cards use `<x-sidebar-card>`.
The page follows the shell pattern (`<x-page-container>` with sidebar slot) and
uses `<x-vote-widget>` per result row and `<x-empty-state>` for no-results.

### Thread listing with sort tabs (com_forum)

The forum category display is a table-based browse with daisyUI
`tabs tabs-bordered` sort controls (Created, Activity, Replies, Title). Each tab
toggles ASC/DESC when clicked while already active. The thread table includes
status icons (star for sticky, lock for closed, chat bubble for normal), linked
titles with date/author, reply counts, and last-post metadata. Sidebar has a
"last post" card and a "New Discussion" button (hidden when category is closed).

See `com_forum/site/views/categories/tmpl/display.blade.php` for the reference
implementation.

### Search results table (com_forum)

The forum search page uses `<x-search-bar>` with `buttonLabel` for an explicit
submit button and `name="q"` for the query parameter. Results render in a
daisyUI `table` with status icons, titles highlighted via `preg_replace` with
`<mark>`, date/author, and section/category columns. Uses `<x-empty-state>`
for no results.

See `com_forum/site/views/categories/tmpl/search.blade.php` for the reference
implementation.

### Overview landing page (com_kb)

The KB overview is a discovery-oriented landing page rather than a traditional
paginated browse. It opens with `<x-search-bar>` (button mode, submits to the
"all articles" URL) and then presents two content sections:

1. **Popular & Recent cards** — a 2-column `grid` with raw `card bg-base-100
   shadow-sm` cards (not `<x-sidebar-card>`, since the titles are linked
   headings that navigate to the full sorted list). Each card lists up to 5
   article links.

2. **Category grid** — another 2-column `grid` where each category with
   articles gets a card showing its title (linked), article count badge, and
   the 3 most recent article links.

The sidebar uses `<x-sidebar-card>` for contextual help links to com_answers,
com_wishlist, and com_support (each conditionally rendered via
`Component::isEnabled()`).

See `com_kb/site/views/articles/tmpl/display.blade.php` for the reference
implementation.

### Category article list (com_kb)

The category browse page is a filtered, sorted, paginated article list. It
uses `<x-search-bar>` (button mode) and raw `tabs tabs-border` sort controls
(Popular / Recent) — not `<x-filter-tabs>`, since there are only two static
sort options with no complex filter state.

The sidebar uses `<x-sidebar-card>` for a category navigation menu. The menu
lists all categories with article counts; when a category is active, its
children expand inline as a nested `<ul>`. Active items use the `active` class
on menu links.

Results render in a daisyUI `.list` with `.list-row` items. Each row shows the
article title (linked), category attribution (when viewing "all"), last-modified
timestamp, and a thumbs-up count with an SVG icon. The view uses `<x-empty-state>`
for no-results.

A result count label (`$category title (N)`) appears right-aligned next to the
sort tabs. Pagination uses the model's `->paginated()` method with additional
URL params for search and sort.

See `com_kb/site/views/articles/tmpl/category.blade.php` for the reference
implementation.

### Resource browse with sort tabs and tag filter (com_resources)

The resources browse page combines `<x-search-bar>` (button mode with hidden
fields for sortby and tag state), removable tag pills, sort tabs
(`role="tablist"` with `tab-active` / `aria-selected`), and a type filter
`<select>` in a single browse view. Results render in a daisyUI `.list` with
`.list-row` items showing title, type, date, authors, ranking/rating bar,
truncated description, and model-rendered tag cloud. The sidebar uses two
`<x-sidebar-card>` widgets: a contextual help card and a popular tags cloud.
`<x-empty-state>` handles no-results. Pagination uses the model's paginator
with additional URL params for tag, type, and sortby.

The sort tabs use raw `tabs tabs-bordered` markup (not `<x-filter-tabs>`)
because the sort options are dynamically built from component config (ranking
is conditional on `show_ranking`).

See `com_resources/site/views/browse/tmpl/default.blade.php` for the reference
implementation.

### Publication browse with sort tabs and category filter (com_publications)

The publications browse page combines `<x-search-bar>` (button mode with hidden
fields for sortby, tag, and category state), removable tag pills, sort tabs
(`role="tablist"` with `tab-active` / `aria-selected`), and a category filter
`<select>` in a single browse view. Results render in a daisyUI `.list` with
`.list-row` items showing thumbnail, title, optional ranking/rating bar,
metadata row (date, category, contributors, DOI), and a truncated
abstract/description. Access-level badges appear on non-public items. The
sidebar uses `<x-sidebar-card>` for a popular tags cloud. `<x-empty-state>`
handles no-results. Pagination uses the model's paginator with additional URL
params for tag, category, and sortby.

The sort tabs use raw `tabs tabs-bordered` markup (not `<x-filter-tabs>`)
because the sort options are dynamically built from component config (ranking
is conditional on `show_ranking`), matching the same pattern used by
com_resources.

See `com_publications/site/views/browse/tmpl/default.blade.php` for the
reference implementation.

### Curation queue (com_publications)

The curation queue is an admin-facing browse of publications pending curation
review. It uses `<x-page-container>` with no sidebar. Unlike the public
publication browse, this view targets curators and admins who need to triage
and assign incoming submissions.

**Filter tabs** — a `tabs tabs-bordered` bar with two `role="tab"` links
(All / Assigned to me), toggling the `curator=owner` filter. Active tab uses
`tab-active` and `aria-selected="true"`.

**Sort tabs** — a second `tabs tabs-bordered` bar (right-aligned via
`justify-between`) with five sort options: ID, Title, Submitted, Status,
Content Type. Each tab links to the same route with `t_sortby` and
`t_sortdir` params; clicking an active tab toggles ASC/DESC.

**Results table** — a `table table-zebra w-full` with columns for ID,
thumbnail (`size-8 rounded`), title with submitter/date metadata, version
label, content type, submitted date, status badge (`badge badge-sm` with
`badge-warning` for pending, `badge-info` for awaiting author changes), curator
assignment, and action buttons. Titles in pending state link to the curation
review form; non-pending titles render as plain text.

**Curator assignment** — authorized users see an "Assign" button
(`btn btn-sm btn-ghost`) or a linked curator name that opens an assignment
dialog via fancybox. Non-authorized users see the curator name as plain text.

**Row actions** — a "Review" button (`btn btn-sm btn-primary`) for pending
items, a "History" button (`btn btn-sm btn-ghost`) opening a fancybox dialog,
and a public-page link.

**Pagination** — standard `<nav>` with the model's paginator output, with
filter params injected into pagination URLs.

**Empty state** — `<x-empty-state>` when no publications match the filters.

See `com_publications/site/views/curation/tmpl/display.blade.php` for the
reference implementation.

### Calendar browse with period tabs (com_events)

com_events has four browse views — year, month, week, day — sharing a common
shell: `<x-page-container>` with a sidebar slot and a period tab bar rendered
by a `_nav.blade.php` partial. The tabs use daisyUI `tabs tabs-border` with
four `<a role="tab">` links (Year / Month / Week / Day), each conditionally
`tab-active` based on the current task. All four views share the same sidebar
partial (`_sidebar.blade.php`).

The **sidebar** stacks three raw card widgets (not `<x-sidebar-card>`, since
the interactive controls inside them — dropdowns, prev/next buttons, clickable
calendar cells — don't fit the simple title+content card pattern):

1. **Category filter** — select dropdown + Go button with sr-only label
2. **Year navigation** — `‹ YYYY ›` with DB-driven boundaries and disabled
   states (`btn-disabled opacity-40`)
3. **Mini calendar** — a handcrafted month grid rendered by a `calendar`
   partial using plain `<table>` with event-day links (not daisyUI `.table`)

The **month view** lists events in `<ul class="list-none">` with an `item`
partial rendering each event as a two-column flex row (date/time on the left,
title/description on the right). The **week view** groups events by day inside
separate cards, with a "Today" badge on the current day. The **year view** and
**day view** follow the same item-list pattern with adjusted headings.

See `com_events/site/views/browse/tmpl/month.blade.php` for the primary
reference implementation.

### Feedback landing and quotes list (com_feedback)

com_feedback has two browse-like views:

**Landing page** (`display.blade.php`) — an action grid of feedback channels
(Success Stories, Polls, Feature Suggestions, Bug Reports, etc.) laid out as
daisyUI cards with icons and descriptions. Each card links to the relevant
feedback action. This is more of a hub/directory than a traditional list browse.

**Quotes page** (`quotes.blade.php`) — a card-list browse of user success story
quotes. Each quote renders in a card with the user's picture, name, org, and
quote text. Uses `<x-empty-state>` when no quotes are available. No pagination
or filtering — renders all published quotes in a single list.

Both views use `<x-page-container>` for their shell.

See `com_feedback/site/views/feedback/tmpl/quotes.blade.php` for the quotes
list reference implementation.

### Poll browse with voting cards (com_poll)

com_poll's browse page (`display.blade.php`) renders all published polls in a
`<x-card-grid cols="3">`. Each card adapts based on poll state:

- **Open polls** render as a `<form>` with radio-button options, a Vote button,
  and a Results link.
- **Closed polls** render compact bar-chart results with percentages using the
  `data-style-width` CSP-safe pattern (see site.js `initDataStyleWidth()`).

Every card has a footer with vote count and an open/closed status badge. The
actions slot links to the "Take the latest poll" page.

See `com_poll/site/views/polls/tmpl/display.blade.php` for the reference
implementation.

### Storefront category and product grids (com_storefront)

com_storefront uses `<x-card-grid cols="3">` for all browse views. The
homepage (`home.blade.php`) shows a category grid with optional images; each
category card links to a collection browse page. Collection and search views
share a `_product-card.blade.php` partial (in `views/shared/tmpl/`) rendering
product image, title, and link.

All three browse views use `<x-search-bar>` in button mode. The collection
view passes a hidden `cId` field via the search bar's default slot to scope
search within the collection. The search view preserves the query string via
`:query="$search"`. `<x-empty-state>` handles no-results in all views.

The **product detail** (`display.blade.php`) is a bespoke commerce layout:
2-column grid with product image on the left and pricing, option radio groups,
quantity select, and add-to-cart form on the right. Product option data is
passed via a `data-sf-options` attribute for CSP-safe JavaScript. Uses
`<x-alert-list>` for notifications. This is justified bespoke UI — the
commerce interaction pattern does not map to any shared component.

The **overview landing** (`overview/default.blade.php`) is an auth-gated page
shown to guests. When a custom landing page article is configured, it renders
that article's content with a login button; otherwise it shows a simple message
prompting login. This is a gate, not a browse view — authenticated users are
redirected to the homepage grid.

See `com_storefront/site/views/overview/tmpl/default.blade.php` (auth gate),
`com_storefront/site/views/storefront/tmpl/home.blade.php` (categories),
`com_storefront/site/views/browse/tmpl/collection.blade.php` (products), and
`com_storefront/site/views/product/tmpl/display.blade.php` (detail) for
reference implementations.

### Site-wide search with category sidebar (com_search — basic)

com_search's basic engine renders a full-text search page using `<x-search-bar>`
in button mode (`buttonLabel`, `name="terms"`), paginated results, and a sidebar
category filter. The search bar searches across all content types simultaneously,
unlike component-scoped search bars that filter within a single component.

**Results** render in a `space-y-6` stack (not `.list` rows). Each result shows:
- Title as a `link link-primary` heading
- Metadata row: category `badge`, date, and contributor links (with `/members/`
  profile URLs)
- Highlighted excerpt with `{!! !!}` unescaped output (search highlighting
  injects `<strong>` tags)
- **Nested child results** grouped by section type (e.g., "Answers", "Forum
  posts") with border-left indentation and collapsible section headings

The **sidebar** uses `<x-sidebar-card>` with a `menu menu-sm` list showing:
- "All Categories" with total count (bold when active, linked when filtered)
- Per-plugin categories with counts (`badge badge-sm`)
- Sub-sections nested as `<ul>` under each category (e.g., forum sub-categories)

Active filter items render as bold `<span>` with `badge-primary`; inactive items
are `<a>` links.

See `com_search/site/views/basic/tmpl/default.blade.php` for the reference
implementation.

### Solr faceted search with view overrides (com_search — solr)

com_search's Solr engine renders a faceted search page using `<x-search-bar>` in
button mode (`buttonLabel`, `name="terms"`), with hidden fields and an optional
tag multi-entry widget passed via the default slot. The form connects to a
faceted sidebar and partial-based results with per-hubtype view overrides.

**Results** render in a `space-y-6` stack using an `_result.blade.php` partial
for default rendering. Each result shows title, category badge, date, author,
snippet with search highlighting, tags (either from child documents or a tags
array), access level badge (admin-only), and a URL link. Hub-type-specific view
overrides (`$viewOverrides[$hubType]`) can replace the default partial with
custom PHP views registered by search components.

The **sidebar** uses `<x-sidebar-card>` with a `menu menu-sm` category list.
Category links and counts are rendered by `SearchComponent::formatWithCounts()`,
which outputs raw HTML (`{!! !!}`). When a type filter is active, the sidebar
shows only that type's sub-filters (including checkbox filter partials from
`filters/tmpl/list.blade.php`).

**No-results state** uses `<x-empty-state>` when terms are provided but no
results found. Spell suggestions render as `alert alert-info` links when
available from the Solr spellcheck component.

See `com_search/site/views/solr/tmpl/display.blade.php` for the reference
implementation.

### Wishlist browse with vote widgets and dual tab bars (com_wishlist)

The wishlist browse page combines search, dual tab navigation (sort + filter),
and per-row vote widgets in a single list view. It uses `<x-search-bar>` in
button mode with hidden fields for filter state (tags, sortby, filterby, task,
newsearch). Below the search bar, two `tabs tabs-border` tab rows provide
sort controls (Ranking / Bonus / Feedback / Submitter / Date) and filter
controls (All / Active / Accepted / Rejected / Granted, plus admin-only
filters). Active tag filters render as `badge badge-primary` pills with
remove links.

Results render in a daisyUI `.list` with `.list-row` items. Each row shows
the wish title (linked), metadata (ID, proposer, date, comment count), a
`<x-vote-widget>` for like/dislike voting, a status `badge` (color-coded by
status: success/error/warning/info/ghost), and optional bonus points and
private indicators. Vote URLs include a CSRF token from `Session::getFormToken()`.

The sidebar uses `<x-sidebar-card>` for an "About" card with contextual
description and admin notices, plus a conditional tag cloud for general
wishlists. `<x-results-heading>` shows the filtered count. `<x-empty-state>`
handles no-results with contextual actions (add a wish, or clear filters).

See `com_wishlist/site/views/wishlists/tmpl/display.blade.php` for the
reference implementation.

### Grouped results by category with period filter (com_whatsnew)

com_whatsnew's results page is an aggregated browse that collects new content
from multiple components (blog, resources, wiki, etc.) via plugin events and
groups the results by category in separate cards.

The **sidebar** has two `<x-sidebar-card>` widgets:

1. **Period filter** — a `select select-bordered` dropdown (Week / Month /
   Quarter / Year / Fiscal Year / Calendar Year) with a Go button, preserving
   the active category via a hidden input.
2. **Category navigation** — a `menu menu-sm` with per-category links and
   `badge badge-sm` counts. Sub-categories render as nested `<ul>` items.
   Active items use the `active` class.

Each **category card** (`card bg-base-100 shadow-sm`) has a title with a
result count label, an RSS feed link button, and a `list` / `list-row` item
list. Items show a linked title and a truncated excerpt. The view supports
plugin-provided custom rendering via `documents()`, `before()`, `out()`, and
`after()` callback methods on whatsnew plugin classes.

**Pagination behavior** varies by mode:
- **Filtered to one category**: standard URL-based pagination with
  `$__view->pagination()`
- **Overview (all categories)**: shows top 5 results per category with a
  "See more results" link to the filtered view

Uses `<x-empty-state>` when no results are found across any category.

See `com_whatsnew/site/views/results/tmpl/display.blade.php` for the reference
implementation.

### Wiki special pages (com_wiki)

com_wiki has several special page views that function as browse/list patterns,
each rendered through the wiki's special page routing system
(`views/special/tmpl/`). All share the same shell: `<x-page-container>` with
a conditional sidebar (the `_wikimenu` partial, which uses `<x-sidebar-card>`
for search, navigation, and tools menus) and the `_submenu` partial for
page-level tabs.

**AllPages** (`allpages.blade.php`) — A namespace-filtered alphabetical index.
The page renders letter headings (`border-b border-base-300`) with linked page
titles underneath, wrapped in a `card bg-base-100 border border-base-300`. A
compact namespace filter (`select select-bordered select-sm w-48`) sits at the
top. Below the page index, a "Special Pages" section lists available special
pages in a multi-column grid (`grid-cols-2 md:grid-cols-3 lg:grid-cols-4`).

**Search** (`search.blade.php`) — Full-text search using `<x-search-bar>` with
button mode. Results render in a `table table-sm` with title (linked), path,
and modified date columns. Pagination is standard URL-based.

**RecentChanges / NewPages** — Table-based listings with title, date, author,
and edit summary columns. These use `table table-sm` with straightforward
iteration.

**LongPages / ShortPages / FileList** — Sorted table listings of pages by
content length or uploaded files, using `table table-sm`.

See `com_wiki/site/views/special/tmpl/` for the reference implementations.

### Pipeline browse with search (com_tools)

The tool pipeline list uses `<x-search-bar>` (button mode, search + clear) with
a `filterby` dropdown for status filtering (all / published / mine). Results
render as daisyUI `table` rows showing tool name, version, status badge, and
last update date. Uses `<x-empty-state>` when no tools match. Unlike most
browse pages, the pipeline list has no sidebar — it uses full-width layout.

See `com_tools/site/views/pipeline/tmpl/pipeline.blade.php` for the reference
implementation.

### Ticket board with query sidebar (com_support)

The support ticket list is a bespoke two-panel layout: a collapsible query
sidebar (folder tree + saved queries) on the left and a sortable ticket table
on the right. This view intentionally does **not** use `<x-search-bar>` or
`<x-filter-tabs>` — the query system is a domain-specific filtering mechanism
with nested boolean conditions, not a simple search/filter flow. The table uses
raw `<table>` markup with sortable column headers (clicking toggles
`sort`/`sortdir` params). Uses `<x-page-container>` for the page shell.

This is the most complex browse pattern in the codebase. Other components
should **not** adopt this pattern — it exists because support ticket querying
has unique requirements (saved queries, shared folders, boolean expression
builders).

See `com_support/site/views/tickets/tmpl/display.blade.php` for the reference
implementation.

### Support portal landing page (com_support)

The support portal is a discovery-oriented overview using `<x-card-grid>` to
present conditionally-rendered link cards in three sections (Finding Content,
Community Help, Getting Support). Each card links to another component
(resources, tags, wiki, etc.) and is conditionally shown via
`Component::isEnabled()`. This is a landing/index pattern, not a browse
pattern, but is documented here because it serves a navigation-hub role.

See `com_support/site/views/index/tmpl/display.blade.php` for the reference
implementation.

### Job listings with search and pagination (com_jobs)

Job browse page using `<x-search-bar>` for keyword search with clear button.
Results render via a `_list.blade.php` partial that displays job cards with
title, company, location, category badge, and posted date. Uses
`<x-empty-state>` when no jobs match. Pagination via standard `$pageNav`.
Action buttons in the header link to the employer dashboard and post-a-job form.

The resume search view (`resumes/tmpl/default.blade.php`) is a related browse
pattern for employers, using `<x-sidebar-card>` with `<x-form-field>` inputs
for keyword, category, and type filtering plus radio sort controls.

See `com_jobs/site/views/jobs/tmpl/default.blade.php` for the reference
implementation.

### Group browse with policy filters (com_groups)

com_groups' browse page uses `<x-search-bar>` (with `:action` and `:query`
props) for keyword search, followed by a sort/filter bar and paginated group
cards. The search bar and filter controls are in separate `<form>` elements to
avoid nested-form issues.

**Sort controls** use `btn btn-xs btn-ghost/btn-active` link buttons for Title
and Alias sorting. **Filter controls** use `select select-bordered select-xs`
dropdowns for State (Active/Archived) and Policy (Open/Restricted/Invite
Only/Closed), with `data-submit-on-change` for auto-submit. Hidden inputs
preserve the current search/sort state across filter submissions.

**Group cards** render via the `_group` partial (`card bg-base-100 shadow-sm`)
showing: group logo (or text placeholder), alias, title (linked), join policy
badge (`badge badge-outline`), and a "Join Group" or membership status button.
For members, cards show last activity timestamp, member count, and a
cancel/edit action link.

The sidebar uses two `<x-sidebar-card>` components: a help text card and a
"Looking for someone?" card linking to the Members page.

Pagination uses `$__view->pagination()` with additional URL params for
search/sort/filter state preservation.

See `com_groups/site/views/groups/tmpl/browse.blade.php` and
`com_groups/site/views/groups/tmpl/_group.blade.php` for the reference
implementation.

### Course catalog browse with sort tabs and sidebar (com_courses)

The courses browse page has two entry points: a **landing page**
(`intro.blade.php`) with `<x-search-bar>`, tagline, and a popular courses grid
via `<x-card-grid cols="3">` with `_course` card partials; and a **catalog
browse** (`browse.blade.php`) with search, sort, tag filter, and paginated
results.

**Landing page** — `<x-page-container>` with optional "Create Course" action
button. `<x-search-bar>` (button mode, submits to browse URL). Popular courses
section uses `<x-card-grid>` with `_course.blade.php` partials showing logo,
title, and truncated blurb. `<x-empty-state>` for no popular courses. "More
courses" link when additional courses exist.

**Catalog browse** — `<x-page-container>` with sidebar slot. `<x-search-bar>`
(button mode with clear, plus hidden fields for sort/index/tag/group state).
Applied tag bar with removable tag link. Optional group header when filtering
by group. Sort tabs via `<x-filter-tabs>` (Title / Alias / Popularity). Results
render in a daisyUI `.list` with `.list-row` items showing logo, title (linked),
instructors, and truncated blurb. Pagination below the list. Sidebar has two
`<x-sidebar-card>` widgets: "Finding a Course" help text and a popular
categories tag cloud.

The sort tabs use `<x-filter-tabs>` because the three sort options (title,
alias, popularity) are simple URL-based toggles with no complex filter state.

See `com_courses/site/views/courses/tmpl/intro.blade.php` (landing),
`com_courses/site/views/courses/tmpl/browse.blade.php` (browse), and
`com_courses/site/views/courses/tmpl/_course.blade.php` (card partial) for the
reference implementations.

## Accessibility Notes

- `aria-label` on the list element describes what the list contains
- `aria-live="polite"` on results count so screen readers announce changes
- `aria-current="page"` on the active pagination button
- Pagination wrapped in `<nav>` with `aria-label="Page navigation"`
- Previous/next buttons use `aria-label` (don't rely on `«`/`»` characters)
- Search input has a visible or `.sr-only` label
- Filter tabs use `role="tablist"` / `role="tab"` with `aria-selected`
- Empty state uses `role="status"` so screen readers announce it
- `<h1>` is the page title in `.page-header`, not the site name
- See [Accessibility Guide](../guides/accessibility.md) for full WCAG 2.2 AA requirements
