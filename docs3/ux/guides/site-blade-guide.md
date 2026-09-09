# Site Blade View Guide

Practical reference for creating site-facing Blade/daisyUI views in Hubzero CMS
components. Covers patterns, gotchas, and conventions learned from the site
components that are currently Blade-enabled: com_answers, com_blog, com_cart,
com_citations, com_collections, com_content, com_courses, com_developer, com_events,
com_feedback, com_forum, com_help, com_jobs, com_kb, com_newsletter, com_oauth,
com_poll, com_projects, com_redirect, com_resources, com_search, com_storefront,
com_tags, com_usage, com_users, com_whatsnew, com_wiki, and com_wishlist.

For the page shell, global components, and WCAG requirements, see the other
docs in this directory. This guide focuses on the **component view layer**.

## Controller Opt-In

Every component that wants Blade views must declare engine and CSS framework
preferences. Two approaches:

### Per-controller (simplest)

```php
class Events extends SiteController
{
    protected $viewEngines = ['blade', 'php'];
    protected $cssFrameworks = ['daisyui', 'classic'];
}
```

### Shared base controller (multi-controller components)

```php
// site/controllers/componentcontroller.php
class ComponentController extends \Hubzero\Component\SiteController
{
    protected $viewEngines = ['blade', 'php'];
    protected $cssFrameworks = ['daisyui', 'classic'];
}

// site/controllers/citations.php
class Citations extends ComponentController { ... }
```

Used by: com_cart, com_citations, com_collections.

### No view needed (JSON-only endpoints)

If a controller only outputs JSON (e.g., com_cron), skip the view entirely:

```php
public function displayTask()
{
    // ... build $output ...
    session_write_close();
    ob_clean();
    header('Content-type: application/json');
    echo json_encode($output);
    exit();
}
```

Delete the `site/views/` directory entirely in this case.

## Tailwind CSS Source Scanning

Tailwind v4 only generates utility classes it finds in scanned files. Each
component's Blade templates must be registered in the site template's source
CSS.

**File:** `app/templates/hubzero/css/site.src.css`

```css
@source "../../../../core/components/com_events/site/views/**/*.blade.php";
@source "../../../../core/components/com_blog/site/views/**/*.blade.php";
```

After adding a `@source` directive or using a new Tailwind class, rebuild:

```bash
cd app/templates/hubzero
npm run build:css
```

Use `npm run dev:css` during iterative template work.

### Common symptom

A Tailwind class (e.g., `mt-10`) has no effect → check `site.css` for the
class. If missing, add the `@source` directive and rebuild.

## Page Layout Pattern

Every view uses `<x-page-container>` with optional slots:

```blade
<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-primary btn-sm" href="...">Add</a>
    @endslot

    @slot('sidebar')
        {{-- sidebar cards --}}
    @endslot

    {{-- main content --}}
</x-page-container>
```

The component renders:
- `.page-header` — teal bar with `<h1>` and action buttons
- `.page-body` — padded content area
- `.page-layout` — two-column grid when sidebar slot is present
- `.page-sidebar` — sticky sidebar (280px, `align-self: start`)

### Sidebar vertical alignment

The sidebar's first card should use `mt-10` so it starts at the same
vertical position as the main content (below the tab navigation):

```blade
@slot('sidebar')
    <div class="card bg-base-100 border border-base-300 mt-10">
        ...
    </div>
    <div class="card bg-base-100 border border-base-300">
        ...
    </div>
@endslot
```

Only the **first** card gets `mt-10`. Subsequent cards stack naturally.

### Sanctioned exceptions to `<x-page-container>`

Not every Blade view uses `<x-page-container>`. These shell exceptions are
intentional:

- **Auth shell** (com_login; com_users login/logout sub-templates only) —
  centered `login-wrapper` / `login-card` for the sign-in form and logout
  confirmation. com_users' other views (MFA, consent, link, SSO logout) use
  `<x-page-container>`. See the [Auth Shell](../patterns/auth-shell.md) pattern doc.
- **Popup/component shell** (com_mailto) — renders with `tmpl=component`
  in a popup window. Uses a centered card layout with no page header or
  sidebar. The send form uses `<x-form-field>` for inputs but no page-level
  shell component.
- **Project shell** (com_projects internal/external) — bespoke two-pane
  layout with sidebar navigation and top header bar. The project dashboard,
  plugin content, and settings views use their own shell partials
  (`_topheader`, `_header`, `_topmenu`, `_menu`) instead of `<x-page-container>`.
  The error view does use `<x-page-container>`.
- **Embedded help** (com_help) — plain iframe/popup views with no page
  chrome.

## Sub-Template Rendering

Blade views use `$__view->view()` to render partials (sub-templates in the
same view directory):

```blade
{!! $__view->view('item')
    ->set('option', $option)
    ->set('row', $row)
    ->loadTemplate() !!}
```

Partial naming convention: prefix with underscore (`_sidebar`, `_nav`, `_comment`)
for partials that are never rendered as standalone layouts.

## Tab Navigation

Use daisyUI tabs for view switching (year/month/week/day, etc.):

```blade
<div role="tablist" class="tabs tabs-bordered mb-6">
    <a role="tab" href="{{ $yearUrl }}"
        @class(['tab', 'tab-active' => $task == 'year'])>
        Year
    </a>
    <a role="tab" href="{{ $monthUrl }}"
        @class(['tab', 'tab-active' => $task == 'month'])>
        Month
    </a>
</div>
```

These are navigation tabs (links), not JS-driven content tabs.

## Sidebar Widgets

### Category filter

Compact dropdown + button. Label is screen-reader-only:

```blade
<div class="card bg-base-100 border border-base-300">
    <div class="card-body p-4">
        <form action="{{ $formAction }}" method="get">
            <label class="sr-only" for="event-category">Category</label>
            <div class="flex gap-2 items-center">
                <select name="category" id="event-category"
                    class="select select-bordered select-sm flex-1">
                    <option value="">All Categories</option>
                    @foreach ($categories as $id => $title)
                        <option value="{{ $id }}" @selected($current == $id)>
                            {{ $title }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                    class="btn btn-sm btn-primary px-3 whitespace-nowrap">
                    Go
                </button>
            </div>
        </form>
    </div>
</div>
```

### Prev/next navigation (year, month, week)

DB-driven navigation with disabled state for boundaries:

```blade
<div class="card bg-base-100 border border-base-300">
    <div class="card-body p-4">
        <div class="flex items-center justify-between">
            <a href="{{ $prevUrl }}"
                @class(['btn btn-ghost btn-sm',
                    'btn-disabled opacity-40' => $prevDisabled])>
                &lsaquo;
            </a>
            <span class="font-semibold text-lg">{{ $year }}</span>
            <a href="{{ $nextUrl }}"
                @class(['btn btn-ghost btn-sm',
                    'btn-disabled opacity-40' => $nextDisabled])>
                &rsaquo;
            </a>
        </div>
    </div>
</div>
```

For disabled states, render a non-link element (`<span>` or `<button
type="button">`) with `btn-disabled opacity-40` and `aria-disabled="true"`.
Do not use `javascript:void(0);` URLs.

### Mini calendar

Use a plain `<table>` — do NOT use daisyUI `.table` classes (they add padding
and styling that breaks the compact calendar layout):

```blade
<table class="text-center w-full text-sm border-collapse">
    <caption class="pb-2">
        <span class="flex items-center justify-between">
            <a class="px-1 hover:text-primary" href="{{ $prev }}">&lsaquo;</a>
            <span class="font-semibold">{{ $monthName }}</span>
            <a class="px-1 hover:text-primary" href="{{ $next }}">&rsaquo;</a>
        </span>
    </caption>
    ...
</table>
```

Use flexbox for the caption prev/next arrows (not float).

## List Items

### Two-column date | content layout

For event-style listings with date/time on the left:

```blade
<li class="py-4 border-b border-base-300 last:border-b-0">
    <div class="flex gap-6">
        <div class="shrink-0 w-32 text-right text-sm">
            <div class="font-semibold">{{ $date }}</div>
            <div class="text-base-content/60">{{ $startTime }}</div>
            <div class="text-base-content/60">{{ $endTime }}</div>
        </div>
        <div class="flex flex-col gap-1 min-w-0">
            <p class="font-semibold">
                <a href="{{ $url }}" class="link link-primary link-hover">
                    {{ $title }}
                </a>
            </p>
            <p class="text-sm text-base-content/70">{{ $description }}</p>
        </div>
    </div>
</li>
```

Use `<ul class="list-none">` as the container (not daisyUI `.list`).

### Simple content list

For blog-style listings, use daisyUI `.list` + `.list-row`:

```blade
<ul class="list bg-base-100 rounded-box shadow-sm">
    @foreach ($rows as $row)
        <li class="list-row">
            <div class="list-col-grow">
                <a class="link link-hover font-medium" href="...">
                    {{ $row->get('title') }}
                </a>
                <div class="text-sm text-base-content/60">
                    <time datetime="...">{{ $date }}</time>
                </div>
            </div>
        </li>
    @endforeach
</ul>
```

## daisyUI Gotchas

### Compact buttons

Prefer daisyUI's stock button sizes (`.btn-sm`) and adjust layout around them.
Avoid adding inline `style=""` height overrides in new site views.

Some early Blade templates, especially `com_events`, still use inline height
overrides for compact sidebar controls. Treat that as cleanup debt, not the
pattern to copy forward.

### Table classes

daisyUI `.table` adds padding, borders, and hover effects that break compact
widgets like mini calendars. Use plain Tailwind classes on `<table>` instead.

### List container

For custom item layouts (two-column, cards), use `<ul class="list-none">`
instead of daisyUI `.list`. The `.list` class enforces `.list-row` grid
layout that may conflict with custom flex layouts.

## Event Navigation Pattern

Calendar-style components need multiple navigation levels. The com_events
sidebar demonstrates the standard stack:

1. **Category filter** — dropdown + Go button (sr-only label)
2. **Year navigation** — ‹ 2026 › with DB-driven boundaries
3. **Mini calendar** — month grid with event-day links

The year boundaries are computed from the database:

```php
$db->setQuery(
    "SELECT MIN(publish_up) min, MAX(publish_down) max FROM `#__events`"
    . " WHERE `scope`='event' AND `state`=1 AND `approved`=1"
);
$range = $db->loadObjectList();
$firstEvent = new \DateTime($range[0]->min ?? '');
$lastEvent = new \DateTime($range[0]->max ?? '');
```

## Notification Alerts

Use the `<x-alert-list>` component instead of inline notification loops. It
handles both data shapes used across components:

```blade
{{-- Replaces 10+ lines of @foreach / match / alert markup --}}
<x-alert-list :notifications="$notifications" />
```

The component accepts indexed tuples (`[$message, $type]` — used by com_cart)
and associative arrays (`['message' => ..., 'type' => ...]` — used by
com_citations). Type values map to daisyUI alert classes: `error` →
`alert-error`, `warning` → `alert-warning`, `info` → `alert-info`, default →
`alert-success`.

Place `<x-alert-list>` at the top of the page content, after `<x-page-container>`
opens and before the main form or content.

## Cache and Reload

After modifying Blade templates:

```bash
rm -f app/cache/views/*.php    # Clear compiled Blade cache
```

After modifying controller PHP:

```bash
./frankenphp reload            # Reload OPcache
```

After adding new Tailwind classes:

```bash
cd app/templates/hubzero
npm run build:css
```

## Conversion Checklist

When converting a component's site views to Blade:

1. **Add controller opt-in** — `$viewEngines` and `$cssFrameworks` properties
2. **Add `@source` directive** — in `site.src.css` for the component's view directory
3. **Create `.blade.php` files** — alongside or replacing `.php` view templates
4. **Use `<x-page-container>`** — for every page layout
5. **Use global components** — `<x-form-section>`, `<x-form-field>`, `<x-empty-state>`, `<x-alert-list>`, etc.
6. **Add `mt-10`** — to first sidebar card for vertical alignment
7. **Test in browser** — clear Blade cache, rebuild CSS, verify rendering
8. **Check dark mode** — toggle theme and verify contrast/readability
9. **Update view-inventory.md** — mark the component as Done

## Current Site Blade Footprint

This is the current repository state, not the target roadmap.

### Blade-enabled components

| Component | Blade files | Key patterns |
|-----------|-------------|--------------|
| com_answers | 5 | Browse, Detail, New, Voting, Comments |
| com_blog | 8 | Browse, Detail, Edit, Delete, Comments, Media |
| com_cart | 16 | Checkout wizard, Browse, Orders, Download |
| com_citations | 8 | Intro, Browse, Detail, Edit, Import wizard |
| com_collections | 6 | Browse, Detail, Edit, Collect |
| com_content | 9 | Article, Archive, Categories, Category, Featured, Form |
| com_courses | 25 | Intro, Browse, Badge, Course detail (plugin tabs + inline edit), Edit, Delete, Copy, Fork, NewOffering, Offering LMS shell, Enroll (3), Certificate, Managers, partials (_course, _tags, _button, _instructor, _intro_edit/view, _summary_edit/view, _plugin_tabs, _page_form) |
| com_dataviewer | 3 | Spreadsheet (embedded DataTables grid), Unauthorized, Gallery (standalone popup — no page-container) |
| com_developer | 21 | Dashboard, Browse, Edit, API console, OAuth |
| com_events | 19 | Calendar, Detail, Edit, Registration, Emails |
| com_feedback | 5 | Quotes, Story, Poll, Thanks |
| com_forum | 8 | Sections, Category/Threads, Edit, Search, Thread Detail, Comments (recursive) |
| com_help | 2 | Embedded help display + index (plain iframe views, no page-container) |
| com_jobs | 13 | Browse, Detail, Edit, Dashboard, Resumes, Apply, Subscribe, Intro |
| com_kb | 4 | Overview, Category/Browse, Detail, Comments (recursive) |
| com_login | 8 | Login/Logout, MFA, Consent, Account Link, SSO Logout, Redirect |
| com_mailto | 2 | Send form, Sent confirmation (popup shell, no page-container) |
| com_members | 27 | Browse (filter sidebar + member cards), View (two-pane profile with plugin tabs + sidebar menu partial + member card partial), Display (intro/welcome), Edit (dynamic profile fields), Activity (users/guests tables), Change Password, Raise Limit, Spamjail, Unapproved; Credentials (remind, reset, verify, setpassword); Register (default multi-section form, create, confirm, change, select, send, unconfirmed, update, raceethnic); ORCID (display with CSP-safe data-attributes, redirect); Media (upload iframe) |
| com_newsletter | 9 | Detail, Subscribe/Unsubscribe, Email Preferences, Reply |
| com_oauth | 1 | OAuth authorization consent |
| com_poll | 3 | Browse with voting cards, Vote form, Results with bar graph + sidebar switcher |
| com_projects | 37 | Intro, Browse, Internal/External (bespoke layouts), Setup wizard, Reports, Change Owner, Error; 12 email templates skipped; 1 new JS (reports-charts.js) |
| com_publications | 22 | Browse, Detail, Curation, Edit wizard, Media, Forks, Versions |
| com_redirect | 1 | External link redirect interstitial with countdown |
| com_resources | 35 | Intro, Browse, Browse Tags, View (8 type layouts + 9 partials), Create (display/delete/thanks), Steps (10 templates), Play, Watch |
| com_search | 5 | Basic search (form + results + category sidebar), Solr search (form + faceted results + sidebar), result partial, 404, filter checkboxes |
| com_storefront | 5 | Homepage (category grid + search), Collection browse, Search results, Product detail (options, pricing, add-to-cart), Overview/landing |
| com_support | 17 | Portal, Ticket list (query sidebar), New ticket (guest/auth), Ticket detail (comments, attachments), Stats (Flot.js charts), Query builder, Report abuse |
| com_tags | 5 | Landing/Cloud, Faceted Results, Browse Table, Edit, Cloud Partial |
| com_tools | 31 | Pipeline browse/search, Tool edit wizard, Versions, License, Finalize, Status (step-nav), Sessions list, Storage, Resource preview |
| com_usage | 1 | Tabbed plugin-generated statistics display |
| com_users | 8 | Login/Logout, MFA, Consent, Account Link, SSO Logout, Redirect |
| com_whatsnew | 1 | Results listing by category/period, sidebar filter, RSS feed links |
| com_wiki | 28 | Page detail, Edit (wiki markup + media), Delete, Rename, Comments (recursive), History (compare), Special pages (AllPages, Search, RecentChanges, etc.) |
| com_wishlist | 7 | Browse (search/filter/sort tabs, vote widgets), Detail (voting, admin actions, comments, plan), Edit, Settings, Comment partials (recursive) |
