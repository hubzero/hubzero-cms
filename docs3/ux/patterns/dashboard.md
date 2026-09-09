# Dashboard

Multi-pane overview page with widgets, summaries, and quick actions. Used for
user dashboards, admin control panels, project overviews, and group landing pages.

## When to Use

- User profile dashboard (my submissions, my groups, activity feed)
- Admin control panel (com_cpanel)
- Project overview (files, members, activity in one view)
- Group landing page (description, members, recent activity)

## daisyUI Components Used

- [Stats](https://daisyui.com/components/stat/) — numeric summaries
- [Card](https://daisyui.com/components/card/) — widget panels
- [Badge](https://daisyui.com/components/badge/) — status indicators
- [Menu](https://daisyui.com/components/menu/) — widget link lists
- [Button](https://daisyui.com/components/button/) — actions
- [Avatar](https://daisyui.com/components/avatar/) — activity feed icons
- [Timeline](https://daisyui.com/components/timeline/) — activity feed (alt)

## New Markup

```html
<section class="py-8">
  <div class="max-w-7xl mx-auto px-4">

    <!-- Dashboard header -->
    <header class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold">Dashboard</h2>
      <div class="flex gap-2">
        <a class="btn btn-primary" href="...">New Entry</a>
      </div>
    </header>

    <!-- Stats summary -->
    <div class="stats shadow-sm w-full mb-8">
      <div class="stat">
        <div class="stat-title">Publications</div>
        <div class="stat-value">12</div>
      </div>
      <div class="stat">
        <div class="stat-title">Groups</div>
        <div class="stat-value">3</div>
      </div>
      <div class="stat">
        <div class="stat-title">Citations</div>
        <div class="stat-value">47</div>
      </div>
      <div class="stat">
        <div class="stat-title">Downloads</div>
        <div class="stat-value">1,204</div>
      </div>
    </div>

    <!-- Widget grid -->
    <div class="grid gap-6 sm:grid-cols-2">

      <!-- Recent activity (wide) -->
      <div class="card bg-base-100 shadow-sm sm:col-span-2"
           aria-label="Recent activity">
        <div class="card-body">
          <div class="flex items-center justify-between mb-2">
            <h3 class="card-title">Recent Activity</h3>
            <a class="link link-primary text-sm" href="...">View all</a>
          </div>

          <div class="space-y-4">
            <div class="flex gap-3">
              <div class="avatar placeholder">
                <div class="bg-base-200 text-base-content/60 rounded-full w-8 h-8">
                  <span class="text-xs">E</span>
                </div>
              </div>
              <div>
                <p class="text-sm">
                  You updated
                  <a class="link link-hover" href="...">Research Data Management</a>
                </p>
                <time class="text-xs text-base-content/50"
                      datetime="2026-01-15T14:30:00Z">2 hours ago</time>
              </div>
            </div>

            <div class="flex gap-3">
              <div class="avatar placeholder">
                <div class="bg-base-200 text-base-content/60 rounded-full w-8 h-8">
                  <span class="text-xs">C</span>
                </div>
              </div>
              <div>
                <p class="text-sm">
                  <a class="link link-hover" href="...">Jane Smith</a> commented on
                  <a class="link link-hover" href="...">Your Publication</a>
                </p>
                <time class="text-xs text-base-content/50"
                      datetime="2026-01-15T10:00:00Z">6 hours ago</time>
              </div>
            </div>

            <div class="flex gap-3">
              <div class="avatar placeholder">
                <div class="bg-base-200 text-base-content/60 rounded-full w-8 h-8">
                  <span class="text-xs">U</span>
                </div>
              </div>
              <div>
                <p class="text-sm">
                  You uploaded
                  <a class="link link-hover" href="...">dataset-v3.csv</a>
                  to <a class="link link-hover" href="...">Research Project</a>
                </p>
                <time class="text-xs text-base-content/50"
                      datetime="2026-01-14T16:45:00Z">Yesterday</time>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- My submissions -->
      <div class="card bg-base-100 shadow-sm" aria-label="My submissions">
        <div class="card-body">
          <div class="flex items-center justify-between mb-2">
            <h3 class="card-title">My Submissions</h3>
            <a class="link link-primary text-sm" href="...">View all</a>
          </div>
          <ul class="menu menu-sm p-0">
            <li>
              <a href="..." class="justify-between">
                Research Data Management
                <span class="badge badge-warning badge-sm">Draft</span>
              </a>
            </li>
            <li>
              <a href="..." class="justify-between">
                Climate Model Dataset v2
                <span class="badge badge-success badge-sm">Published</span>
              </a>
            </li>
            <li>
              <a href="..." class="justify-between">
                Workshop Materials 2026
                <span class="badge badge-info badge-sm">Under Review</span>
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- My groups -->
      <div class="card bg-base-100 shadow-sm" aria-label="My groups">
        <div class="card-body">
          <div class="flex items-center justify-between mb-2">
            <h3 class="card-title">My Groups</h3>
            <a class="link link-primary text-sm" href="...">View all</a>
          </div>
          <ul class="menu menu-sm p-0">
            <li>
              <a href="..." class="justify-between">
                Computational Research Lab
                <span class="text-xs text-base-content/50">12 members</span>
              </a>
            </li>
            <li>
              <a href="..." class="justify-between">
                Data Science Working Group
                <span class="text-xs text-base-content/50">8 members</span>
              </a>
            </li>
          </ul>
          <div class="card-actions mt-2">
            <a class="btn btn-sm btn-ghost" href="...">Browse Groups</a>
          </div>
        </div>
      </div>

      <!-- Empty widget -->
      <div class="card bg-base-100 shadow-sm" aria-label="Projects">
        <div class="card-body">
          <h3 class="card-title">My Projects</h3>
          <div class="text-center py-8">
            <p class="text-sm text-base-content/60 mb-3">
              You don't have any projects yet.
            </p>
            <a class="btn btn-sm btn-primary" href="...">Start a Project</a>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>
```

## Structure Reference

| Element | daisyUI Class | Purpose |
|---------|---------------|---------|
| Stats row | `.stats` | Horizontal stat container |
| Single stat | `.stat` | One metric |
| Stat number | `.stat-value` | The number |
| Stat label | `.stat-title` | What the number means |
| Widget | `.card .card-body .shadow-sm` | Content panel |
| Widget title | `.card-title` | Panel heading |
| "View all" link | `.link .link-primary` | See-more link |
| Widget actions | `.card-actions` | Bottom action area |
| Item list | `.menu .menu-sm` | Link list inside widget |
| Status pill | `.badge .badge-sm` | Item state indicator |
| Activity icon | `.avatar .placeholder` | Event type indicator |
| Activity time | Tailwind utility | Relative timestamp |
| Wide widget | `.sm:col-span-2` | Spans full grid width |
| Empty state | Centered text + `.btn` | No-content placeholder |

## Widget Patterns

### List Widget

```html
<div class="card bg-base-100 shadow-sm">
  <div class="card-body">
    <div class="flex items-center justify-between mb-2">
      <h3 class="card-title">Title</h3>
      <a class="link link-primary text-sm" href="...">View all</a>
    </div>
    <ul class="menu menu-sm p-0">
      <li>
        <a href="..." class="justify-between">
          Item name
          <span class="badge badge-sm badge-success">Status</span>
        </a>
      </li>
    </ul>
  </div>
</div>
```

### Empty Widget

```html
<div class="text-center py-8">
  <p class="text-sm text-base-content/60 mb-3">No items to display.</p>
  <a class="btn btn-sm btn-primary" href="...">Create One</a>
</div>
```

### Stat Card

```html
<div class="stats shadow-sm">
  <div class="stat">
    <div class="stat-title">Label</div>
    <div class="stat-value">42</div>
    <div class="stat-desc">Optional description</div>
  </div>
</div>
```

## Variant: Tabbed Statistics (com_usage)

com_usage is a dashboard-style overview that displays plugin-generated statistics
in a tabbed layout. Each tab corresponds to a usage plugin (e.g. "Overview",
"Domain", "Tools") and the content is rendered entirely by the plugin as raw HTML.

Key differences from the widget-grid dashboard above:

- **Tabs, not cards** — content is organized by `<x-filter-tabs>` rather than a
  card grid. Only one section is visible at a time.
- **Plugin-generated HTML** — sections are rendered by usage plugins and injected
  with `{!! $section !!}`. The view is a thin shell, not a layout owner.
- **Fragment mode** — when `$no_html` is true, the view skips `<x-page-container>`
  and renders only the active section (for AJAX tab switching).
- **Uses `<x-page-container>`** with the `tabs` slot for the tab bar.

```blade
<x-page-container :title="$title">
    @slot('tabs')
        <x-filter-tabs :options="$tabOptions" :active="$activeTab" />
    @endslot

    @foreach($sections as $k => $section)
        <div id="usage-{{ e(key($cats[$k])) }}"
             class="{{ key($cats[$k]) !== $task ? 'hidden' : '' }}">
            {!! $section !!}
        </div>
    @endforeach
</x-page-container>
```

## Variant: Group Intro Landing (com_groups)

com_groups' intro page (`display.blade.php`) is a dashboard-style landing that
combines a search entry point with categorized group listings. It uses
`<x-page-container>` with a "Create New Group" action button but no sidebar.

Key differences from the widget-grid dashboard above:

- **Search-first** — the top section is a `<x-search-bar>` that submits to the
  browse page, paired with a "Browse available groups" link button.
- **Categorized group lists** — separate sections for "My Groups", "Groups
  Matching My Interests", "Popular Groups", and "Featured Groups", each
  rendering group cards via the `_group` partial.
- **Guest vs. member** — the "My Groups" and "Interests" sections only render
  for authenticated users. Guest users see just search + popular/featured.
- **Empty states** — each section uses `<x-empty-state>` when no groups match.
- **No stat cards or widget grid** — content is vertically stacked sections,
  not a card grid.

See `com_groups/site/views/groups/tmpl/display.blade.php` for the reference
implementation.

## Accessibility Notes

- Widget cards use `aria-label` describing their content
- `<time datetime="...">` for all timestamps
- Activity icons are decorative (meaning conveyed by text)
- Badges convey status visually; text label is always present — color is never
  the sole indicator (WCAG 1.4.1)
- Stat values are plain text (not images), so screen readers can read them
- Menu items inside widgets are keyboard navigable via daisyUI's `.menu`
- Empty widget states use descriptive text + CTA button
- See [Accessibility Guide](../guides/accessibility.md) for full WCAG 2.2 AA requirements
