# Global Blade Components

Reusable anonymous Blade components in `core/blade/components/`. These provide
consistent structure for common UI patterns across all site-facing component
views. The template CSS (`site.src.css`) controls visual styling — component
views never contain raw Tailwind utilities.

> **Prefer global components over raw HTML.** When a global component exists
> for a pattern, use it. This ensures structural consistency, reduces
> boilerplate, and makes future template changes automatic. If you are writing
> markup that a global component already handles, refactor to use the component
> instead.

## Directory & Resolution

```
core/blade/components/           ← global, no prefix: <x-page-container />
app/templates/hubzero/blade/     ← template overrides (same filename wins)
  components/
core/components/com_blog/        ← extension-scoped: <x-com-blog::foo />
  blade/components/
```

Template overrides take precedence over core. Extension-scoped components use
a hyphenated prefix derived from the directory name (`com_blog` → `com-blog`).

---

## Page Structure

### `<x-page-container>` — Full page wrapper

The primary page wrapper. Combines the page header, optional tabs, page body,
and optional sidebar into one component. **Every blade view should use this**
instead of writing raw `page-header` + `page-body` + `page-layout` HTML.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `''` | Page `<h1>` text |
| `bodyClass` | string | `''` | Extra class on `.page-body` (e.g. `edit-form`) |

| Slot | Description |
|------|-------------|
| default | Main page content |
| `actions` | Buttons in the page header (right-aligned) |
| `tabs` | Tab navigation bar below the title |
| `sidebar` | Sidebar content — when present, enables the two-column layout |

```blade
<x-page-container title="Blog">
    @slot('actions')
        <a class="btn" href="{{ $feedUrl }}">Feed</a>
    @endslot

    @slot('sidebar')
        {{-- sidebar cards --}}
    @endslot

    {{-- main content --}}
</x-page-container>
```

For edit forms, add `bodyClass="edit-form"` to get form-specific input styling:

```blade
<x-page-container title="Edit Entry" bodyClass="edit-form">
    ...
</x-page-container>
```

**File**: `core/blade/components/page-container.blade.php`
**CSS classes**: `.page-header`, `.page-header-content`, `.page-header-actions`,
`.page-body`, `.page-layout`, `.page-main`, `.page-sidebar`

### `<x-page-header>` — Standalone page header

Use only when you need the header without the page-body wrapper (rare).
Prefer `<x-page-container>` in most cases.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `''` | Page `<h1>` text |

| Slot | Description |
|------|-------------|
| default | Action buttons |

```blade
<x-page-header title="Delete Entry">
    <a class="btn" href="{{ $cancelUrl }}">Cancel</a>
</x-page-header>
```

**File**: `core/blade/components/page-header.blade.php`

### `<x-page-layout>` — Standalone main + sidebar grid

Use only when you need a sidebar layout without `<x-page-container>` (rare).

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `sidebar` | slot | `null` | Sidebar content |

**File**: `core/blade/components/page-layout.blade.php`

---

## Forms

### `<x-form-section>` — Grouped form fields

Groups related form fields inside a bordered card with an optional heading.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `heading` | string | `''` | Section heading text |

| Slot | Description |
|------|-------------|
| default | Form fields |

```blade
<x-form-section heading="Details">
    <x-form-field name="title" label="Title" required>
        <input type="text" id="title" name="entry[title]"
               class="input w-full" required />
    </x-form-field>
</x-form-section>
```

**File**: `core/blade/components/form-section.blade.php`
**CSS classes**: `.form-section`, `.form-section-heading`, `.form-section-body`

### `<x-form-field>` — Label + input + hint

Wraps a single form input with its label, hint text, and error message.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `''` | Input `id` / `for` value |
| `label` | string | `''` | Label text |
| `type` | string | `'text'` | Input type (informational) |
| `hint` | string | `''` | Help text below input |
| `required` | bool | `false` | Show red asterisk |
| `error` | string | `''` | Error message (replaces hint) |

| Slot | Description |
|------|-------------|
| default | The input element |

```blade
<x-form-field name="field-tags" label="Tags" hint="Comma-separated list.">
    <input type="text" id="field-tags" name="tags" class="input w-full" />
</x-form-field>
```

**File**: `core/blade/components/form-field.blade.php`
**CSS classes**: `.form-field`, `.form-field-label`, `.form-field-hint`

---

## Content Display

### `<x-card-grid>` — Responsive card grid

CSS grid container for card layouts.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `cols` | int | `3` | Number of columns (`3` or `4`) |

| Slot | Description |
|------|-------------|
| default | Card elements |

```blade
<x-card-grid cols="3">
    <div class="poll-card">...</div>
    <div class="poll-card">...</div>
</x-card-grid>
```

**File**: `core/blade/components/card-grid.blade.php`
**CSS classes**: `.card-grid`, `.card-grid-3`, `.card-grid-4`

### `<x-empty-state>` — No results / empty content

Centered placeholder for empty pages, search results, or missing content.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `'Nothing here yet'` | Heading text |
| `message` | string | `''` | Description text |
| `icon` | string | `''` | Custom SVG (raw HTML) |

| Slot | Description |
|------|-------------|
| default | Action button or link |

```blade
<x-empty-state
    title="No entries found"
    message="Try adjusting your search criteria."
>
    <a class="btn btn-ghost" href="{{ $clearUrl }}">Clear filters</a>
</x-empty-state>
```

**File**: `core/blade/components/empty-state.blade.php`
**CSS classes**: `.empty-state`, `.empty-state-icon`, `.empty-state-title`,
`.empty-state-message`, `.empty-state-action`

### `<x-author-card>` — Author info sidebar card

Sidebar card showing author avatar, name, affiliation, and optional bio.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | `''` | Author display name |
| `avatar` | string | `''` | Avatar image URL |
| `profileUrl` | string | `''` | Link to author profile |
| `affiliation` | string | `''` | Organization / department |
| `bio` | string | `''` | Short biography |

| Slot | Description |
|------|-------------|
| default | Extra content below author info (links, stats) |

```blade
<x-author-card
    :name="$author->get('name')"
    :avatar="$author->picture()"
    :profileUrl="Route::url($author->link())"
    :affiliation="$author->get('organization')"
/>
```

**File**: `core/blade/components/author-card.blade.php`
**CSS classes**: `.author-card`, `.author-card-inner`, `.author-card-avatar`,
`.author-card-info`, `.author-card-name`, `.author-card-affiliation`,
`.author-card-bio`, `.author-card-extra`

### `<x-tag-cloud>` — Tag list

Ordered list of linked tag pills.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `tags` | Collection | `[]` | Tag model collection with `tag` and `raw_tag` |

```blade
<x-tag-cloud :tags="$entry->tags()" />
```

**File**: `core/blade/components/tag-cloud.blade.php`
**CSS classes**: `.tags`, `.tag`

### `<x-stat-card>` — Metric display card

Single metric with label, value, optional description and trend indicator.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string | `''` | Metric label |
| `value` | string | `''` | Metric value |
| `description` | string | `''` | Extra context |
| `trend` | string | `''` | `'up'` or `'down'` for color |

```blade
<x-stat-card
    label="Total Voters"
    :value="$totalVotes"
/>
```

**File**: `core/blade/components/stat-card.blade.php`
**CSS classes**: `.stat-card`, `.stat-card-label`, `.stat-card-value`,
`.stat-card-desc`, `.stat-trend-up`, `.stat-trend-down`

---

## Interactive

### `<x-comment>` — Threaded comment

Individual comment with avatar, metadata, body, actions, and nested replies.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | string | `''` | Comment ID (for anchor) |
| `name` | string | `''` | Author name |
| `avatar` | string | `''` | Avatar URL |
| `profileUrl` | string | `''` | Profile link |
| `date` | string | `''` | Display date |
| `time` | string | `''` | Display time |
| `datetime` | string | `''` | ISO datetime for `<time>` |
| `isAuthor` | bool | `false` | Highlight as entry author |
| `isReported` | bool | `false` | Show "reported" placeholder |
| `anonymous` | bool | `false` | Show as "Anonymous" |
| `depth` | int | `0` | Nesting depth |

| Slot | Description |
|------|-------------|
| default | Comment body HTML |
| `actions` | Reply/edit/report/delete buttons |
| `replies` | Nested `<x-comment>` elements |

```blade
<x-comment
    :id="$comment->get('id')"
    :name="$comment->creator->get('name')"
    :avatar="$comment->creator->picture()"
    :datetime="$comment->get('created')"
    :date="Date::of($comment->get('created'))->toLocal('M d, Y')"
    :time="Date::of($comment->get('created'))->toLocal('g:i A')"
>
    {!! $comment->get('content') !!}

    @slot('actions')
        <a class="btn btn-xs btn-ghost" href="?reply={{ $comment->get('id') }}">Reply</a>
    @endslot
</x-comment>
```

**File**: `core/blade/components/comment.blade.php`
**CSS classes**: `.comment`, `.comment-author`, `.comment-reported`,
`.comment-meta`, `.comment-meta-text`, `.comment-name`, `.comment-time`,
`.comment-body`, `.comment-actions`, `.comment-replies`

### `<x-vote-widget>` — Up/down vote buttons

Thumbs up/down voting with counts and active state.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `likes` | int | `0` | Like count |
| `dislikes` | int | `0` | Dislike count |
| `vote` | string | `''` | User's vote: `'yes'`/`'like'` or `'no'`/`'dislike'` |
| `likeUrl` | string | `''` | Vote up URL |
| `dislikeUrl` | string | `''` | Vote down URL |
| `disabled` | bool | `false` | Disable voting (not logged in) |

```blade
<x-vote-widget
    :likes="$entry->get('positive')"
    :dislikes="$entry->get('negative')"
    :vote="$userVote"
    :likeUrl="Route::url('...')"
    :dislikeUrl="Route::url('...')"
/>
```

**File**: `core/blade/components/vote-widget.blade.php`
**CSS classes**: `.vote-widget`, `.vote-btn`, `.vote-icon`, `.vote-active`,
`.vote-disabled`

### `<x-confirm-dialog>` — Delete/destructive action confirmation

Centered confirmation card for destructive actions.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `'Are you sure?'` | Heading |
| `description` | string | `'This action cannot be undone.'` | Explanation |
| `action` | string | `''` | Form action URL |
| `method` | string | `'post'` | HTTP method |
| `confirmLabel` | string | `'Delete'` | Confirm button text |
| `cancelUrl` | string | `''` | Cancel link URL |
| `cancelLabel` | string | `'Cancel'` | Cancel button text |
| `error` | string | `''` | Error message to display |

| Slot | Description |
|------|-------------|
| default | Impact details (what will be deleted) |
| `hiddenFields` | Hidden form inputs (CSRF token, IDs) |

```blade
<x-confirm-dialog
    title="Delete Entry?"
    description="This will permanently delete the blog entry."
    :action="Route::url('...')"
    :cancelUrl="Route::url('...')"
>
    <ul>
        <li>{{ $entry->get('title') }}</li>
        <li>{{ $commentCount }} comments</li>
    </ul>

    @slot('hiddenFields')
        <input type="hidden" name="id" value="{{ $entry->get('id') }}" />
        {!! Html::input('token') !!}
    @endslot
</x-confirm-dialog>
```

**File**: `core/blade/components/confirm-dialog.blade.php`
**CSS classes**: `.confirm-delete`, `.confirm-card`, `.confirm-icon`,
`.confirm-title`, `.confirm-desc`, `.confirm-impact`, `.confirm-actions`,
`.btn-danger`

---

## Navigation & Progress

### `<x-step-nav>` — Multi-step wizard progress

Numbered step indicator with completed/current/upcoming states.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `steps` | array | `[]` | Step labels (strings or `['label' => '...', 'url' => '...']`) |
| `current` | int | `0` | Zero-based current step index |

```blade
<x-step-nav
    :steps="['Details', 'Authors', 'Review', 'Submit']"
    :current="1"
/>
```

Completed steps with URLs become clickable links:

```blade
<x-step-nav
    :steps="[
        ['label' => 'Details', 'url' => '?step=0'],
        ['label' => 'Authors', 'url' => '?step=1'],
        'Review',
        'Submit',
    ]"
    :current="2"
/>
```

**File**: `core/blade/components/step-nav.blade.php`
**CSS classes**: daisyUI `.steps`, `.steps-horizontal`, `.step`, `.step-primary`
**Accessibility**: `aria-current="step"` on the active step

---

## Media

### `<x-media-upload>` — File upload dropzone

Sidebar file upload widget with dropzone, file list, and upload button.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `action` | string | `''` | Upload form action URL |
| `accept` | string | `''` | File type filter (e.g. `image/*`) |
| `maxSize` | string | `''` | Max file size label (e.g. `2MB`) |
| `multiple` | bool | `false` | Allow multiple files |
| `listUrl` | string | `''` | URL for async file list loading |

| Slot | Description |
|------|-------------|
| default | Existing file list HTML |
| `hiddenFields` | Hidden form inputs |

```blade
<x-media-upload
    :action="Route::url('...')"
    accept="image/*"
    maxSize="2MB"
>
    @foreach($files as $file)
        <li class="file-row">{{ $file->get('name') }}</li>
    @endforeach
</x-media-upload>
```

**File**: `core/blade/components/media-upload.blade.php`
**CSS classes**: `.file-manager-card`, `.file-manager-header`,
`.file-manager-upload`, `.file-manager-dropzone`, `.file-manager-dropzone-icon`,
`.file-manager-dropzone-text`, `.file-manager-dropzone-hint`,
`.file-manager-actions`, `.file-manager-list`

---

## Browse & Filter

### `<x-search-bar>` — Search input form

Search field with optional submit button and clear link. Two modes: simple
(auto-submit on Enter) and button (visible Submit + optional Clear).

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `action` | string | `''` | Form action URL |
| `query` | string | `''` | Current search value |
| `placeholder` | string | `''` | Input placeholder text |
| `name` | string | `'search'` | Input field name |
| `label` | string | `''` | Screen-reader label (falls back to placeholder) |
| `buttonLabel` | string | `''` | Submit button text (empty = simple mode) |
| `clearUrl` | string | `''` | Clear link URL (shown when query is non-empty) |

| Slot | Description |
|------|-------------|
| default | Hidden inputs to preserve filter state |

```blade
{{-- Simple (auto-submit on Enter) --}}
<x-search-bar
    :action="Route::url('index.php?option=com_blog&task=browse', false)"
    :query="$filters['search']"
    placeholder="{{ Lang::txt('COM_BLOG_SEARCH_PLACEHOLDER') }}"
>
    <input type="hidden" name="option" value="{{ $option }}" />
</x-search-bar>

{{-- With button + clear --}}
<x-search-bar
    :action="Route::url('index.php?option=com_citations&task=browse', false)"
    :query="$filters['search']"
    placeholder="{{ Lang::txt('COM_CITATIONS_SEARCH_PLACEHOLDER') }}"
    :buttonLabel="Lang::txt('COM_CITATIONS_SEARCH')"
    :clearUrl="Route::url('index.php?option=com_citations&task=browse', false)"
/>
```

**File**: `core/blade/components/search-bar.blade.php`

### `<x-filter-tabs>` — Horizontal tab filter

Single-select filter rendered as daisyUI tabs. Each option is a link URL.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array | `[]` | `[url => label]` pairs |
| `active` | string | `''` | URL of the currently active tab |

```blade
@php
    $tabOptions = [
        Route::url($base . '&sort=popularity', false) => Lang::txt('COM_KB_SORT_POPULAR'),
        Route::url($base . '&sort=recent', false) => Lang::txt('COM_KB_SORT_RECENT'),
    ];
    $activeTab = Route::url($base . '&sort=' . $filters['sort'], false);
@endphp
<x-filter-tabs :options="$tabOptions" :active="$activeTab" />
```

**File**: `core/blade/components/filter-tabs.blade.php`

### `<x-filter-sort>` — Sort/filter dropdown form

One or more `<select>` dropdowns that auto-submit on change via
`data-submit-on-change`. Includes `<noscript>` fallback button.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `action` | string | `''` | Form action URL |
| `selects` | array | `[]` | Array of select definitions (see below) |

Each select definition:

| Key | Description |
|-----|-------------|
| `id` | Select element ID |
| `name` | Form field name |
| `label` | Visible label text |
| `value` | Currently selected value |
| `options` | `[value => label]` pairs |

| Slot | Description |
|------|-------------|
| default | Hidden inputs to preserve filter state |

```blade
<x-filter-sort
    :action="Route::url('index.php?option=' . $option, false)"
    :selects="[
        [
            'id' => 'sort-select',
            'name' => 'sortby',
            'label' => Lang::txt('COM_ANSWERS_SORT'),
            'value' => $filters['sortby'],
            'options' => $sortOptions,
        ],
        [
            'id' => 'filter-select',
            'name' => 'filterby',
            'label' => Lang::txt('COM_ANSWERS_FILTER'),
            'value' => $filters['filterby'],
            'options' => $statusOptions,
        ],
    ]"
>
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="task" value="search" />
</x-filter-sort>
```

**File**: `core/blade/components/filter-sort.blade.php`

### `<x-results-heading>` — Results count heading

Section heading with a parenthesized count in muted text.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `''` | Heading text |
| `count` | int | `0` | Result count |

```blade
<x-results-heading
    :title="Lang::txt('COM_ANSWERS_FILTER_OPEN')"
    :count="$total"
/>
```

**File**: `core/blade/components/results-heading.blade.php`

---

## Sidebar

### `<x-sidebar-card>` — Card with title and content

Simple card wrapper for sidebar widgets. Renders the standard
`card bg-base-100 shadow-sm` + `card-body` + `card-title text-sm` pattern.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `''` | Card heading (omitted if empty) |

| Slot | Description |
|------|-------------|
| default | Card body content (menus, text, forms) |

```blade
<x-sidebar-card :title="Lang::txt('COM_BLOG_POPULAR_ENTRIES')">
    @if($popular->count())
        <ul class="menu menu-sm p-0">
            @foreach($popular as $row)
                <li><a href="{{ Route::url($row->link(), false) }}">{{ $row->get('title') }}</a></li>
            @endforeach
        </ul>
    @else
        <p class="text-sm text-muted-foreground">{{ Lang::txt('COM_BLOG_NO_ENTRIES_FOUND') }}</p>
    @endif
</x-sidebar-card>
```

**File**: `core/blade/components/sidebar-card.blade.php`

---

## Authentication

### `<x-auth-gate>` — Login prompt for guests

Renders a "You must log in to..." message with a login link that includes
a return URL for post-login redirect.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `returnUrl` | string | `''` | Full URL to redirect to after login |
| `message` | string | `''` | Custom message (use `:login` as placeholder for the link) |

```blade
{{-- Default message --}}
<x-auth-gate :returnUrl="Route::url($row->link() . '#comments', false, true)" />

{{-- Custom message --}}
<x-auth-gate
    :returnUrl="Route::url($row->link(), false, true)"
    message="You must :login to post a comment."
/>
```

**File**: `core/blade/components/auth-gate.blade.php`
**CSS classes**: `.login-to-comment`

---

## Quick Reference

| Component | Use For | Key Props |
|-----------|---------|-----------|
| `<x-page-container>` | Every page | `title`, `bodyClass`, slots: `actions`, `tabs`, `sidebar` |
| `<x-form-section>` | Form field groups | `heading` |
| `<x-form-field>` | Form inputs | `name`, `label`, `hint`, `required`, `error` |
| `<x-card-grid>` | Card layouts | `cols` |
| `<x-empty-state>` | No results | `title`, `message` |
| `<x-author-card>` | Author sidebars | `name`, `avatar`, `profileUrl`, `affiliation` |
| `<x-tag-cloud>` | Tag lists | `tags` |
| `<x-stat-card>` | Metrics | `label`, `value`, `description`, `trend` |
| `<x-comment>` | Comments | `id`, `name`, `avatar`, `datetime` |
| `<x-vote-widget>` | Voting | `likes`, `dislikes`, `vote`, `likeUrl`, `dislikeUrl` |
| `<x-confirm-dialog>` | Delete confirmation | `title`, `description`, `action`, `cancelUrl` |
| `<x-step-nav>` | Wizard progress | `steps`, `current` |
| `<x-media-upload>` | File uploads | `action`, `accept`, `maxSize` |
| `<x-search-bar>` | Search input | `action`, `query`, `placeholder`, `buttonLabel` |
| `<x-filter-tabs>` | Tab filter | `options`, `active` |
| `<x-filter-sort>` | Sort/filter dropdowns | `action`, `selects` |
| `<x-results-heading>` | Results count heading | `title`, `count` |
| `<x-sidebar-card>` | Sidebar widgets | `title` + `$attributes` merge |
| `<x-alert-list>` | Notification alerts | `notifications` (indexed or assoc arrays) |
| `<x-auth-gate>` | Login prompt | `returnUrl`, `message` |

## Usage in Converted Components

| Component | Components Used |
|-----------|----------------|
| **com_blog** | `page-container`, `page-header`, `empty-state`, `search-bar`, `sidebar-card`, `auth-gate`, `author-card`, `form-section`, `form-field`, `confirm-dialog` |
| **com_answers** | `page-container`, `vote-widget`, `empty-state`, `search-bar`, `results-heading`, `form-section`, `form-field`, `stat-card`, `confirm-dialog`, `sidebar-card` |
| **com_citations** | `page-container`, `empty-state`, `search-bar`, `sidebar-card`, `form-section`, `form-field`, `step-nav`, `alert-list` |
| **com_kb** | `page-container`, `vote-widget`, `empty-state`, `search-bar`, `sidebar-card`, `auth-gate` |
| **com_poll** | `page-container`, `card-grid`, `empty-state`, `stat-card` |
| **com_projects** | `page-container`, `search-bar`, `step-nav`, `empty-state` (browse, intro, features, error views; internal.blade.php and external.blade.php use bespoke project-shell layouts) |
| **com_cart** | `page-container`, `step-nav`, `empty-state`, `alert-list`, `sidebar-card`, `form-field` |
| **com_collections** | `page-container`, `filter-tabs`, `search-bar`, `empty-state` |
| **com_tags** | `page-container`, `search-bar`, `sidebar-card`, `form-section`, `form-field`, `empty-state` |
| **com_forum** | `page-container`, `sidebar-card`, `empty-state`, `form-section`, `form-field`, `auth-gate` |
| **com_groups** | `page-container`, `sidebar-card`, `empty-state`, `search-bar`, `form-section`, `form-field` |
| **com_content** | `page-container`, `empty-state`, `sidebar-card`, `search-bar` |
| **com_courses** | `page-container`, `search-bar`, `card-grid`, `empty-state`, `sidebar-card`, `filter-tabs`, `form-section`, `form-field`, `confirm-dialog` (course detail and offering views use bespoke layouts for plugin tabs and two-pane LMS shell) |
| **com_dataviewer** | None (specialized embedded DataTables grid — no page-container; gallery is standalone popup) |
| **com_developer** | `page-container`, `empty-state`, `sidebar-card`, `form-section`, `form-field` |
| **com_events** | `page-container` |
| **com_feedback** | `page-container`, `form-section`, `form-field`, `empty-state` |
| **com_mailto** | `form-field` (no `page-container` — popup/component shell) |
| **com_members** | `page-container`, `search-bar`, `sidebar-card` (profile view uses bespoke two-pane layout, not `page-container`) |
| **com_jobs** | `page-container`, `search-bar`, `empty-state`, `sidebar-card`, `form-section`, `form-field` |
| **com_newsletter** | `page-container`, `sidebar-card`, `form-section`, `form-field`, `empty-state` |
| **com_oauth** | `page-container`, `form-field` |
| **com_publications** | `page-container`, `search-bar`, `sidebar-card`, `empty-state` |
| **com_redirect** | `page-container` |
| **com_resources** | `page-container`, `search-bar`, `card-grid`, `sidebar-card`, `form-section`, `form-field`, `empty-state`, `step-nav` |
| **com_search** | `page-container`, `search-bar`, `sidebar-card`, `empty-state` |
| **com_storefront** | `page-container`, `search-bar`, `card-grid`, `empty-state`, `alert-list` |
| **com_support** | `page-container`, `form-field`, `card-grid` (portal only; ticket board/detail views are bespoke) |
| **com_tools** | `page-container`, `search-bar`, `empty-state`, `sidebar-card`, `form-field`, `step-nav`, `icon` |
| **com_usage** | `page-container` |
| **com_users** | `page-container`, `form-field` (login/logout use auth-shell, not page-container) |
| **com_whatsnew** | `page-container`, `sidebar-card`, `empty-state` |
| **com_wiki** | `page-container`, `sidebar-card`, `search-bar`, `form-section`, `form-field` |
| **com_wishlist** | `page-container`, `vote-widget`, `empty-state`, `search-bar`, `sidebar-card`, `results-heading`, `form-section`, `form-field` |
| **com_demo** | `page-container`, `card-grid`, `empty-state`, `stat-card`, `step-nav`, `tag-cloud`, `vote-widget`, `author-card`, `media-upload`, `confirm-dialog`, `form-section`, `form-field`, `comment` |

## Admin Components

Admin-specific global components for the `hzadmin` template. Full reference
in [Admin Views](../guides/admin.md).

| Component | Use For | Key Props |
|-----------|---------|-----------|
| `<x-admin-toolbar>` | Toolbar setup | `title`, `icon`, `canDo`, `option`, `edit` |
| `<x-admin-form>` | List view wrapper | `option`, `controller`, `sort`, `sortDir` |
| `<x-admin-edit>` | Edit form wrapper | `option`, `controller`, slot: `sidebar` |
| `<x-admin-filters>` | Filter bar | slots: `search`, default |

**Files**: `core/blade/components/admin-*.blade.php`
**CSS**: `core/templates/hzadmin/css/admin.src.css`

---

## Adding New Components

1. Create `core/blade/components/your-component.blade.php`
2. Use `@props([...])` for typed props with defaults
3. Use semantic CSS class names — no raw Tailwind utilities
4. Add CSS rules in `app/templates/hubzero/css/site.src.css`
5. Add an `@source` path in `site.src.css` if the component directory is new
6. Document the component in this file
7. Rebuild CSS: `cd app/templates/hubzero && npm run build:css`
