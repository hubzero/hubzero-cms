# Plugin Blade View Guide

Practical reference for creating Blade/daisyUI views in Hubzero CMS **plugins**
(members plugins, groups plugins) and the **host component views** that wrap them.
Plugins have a different rendering pipeline than components — this guide covers
the differences, patterns, and gotchas.

For component views, see [Site Blade Guide](site-blade-guide.md).

## How Plugins Differ from Components

| Aspect | Component | Plugin |
|--------|-----------|--------|
| Rendering | Controller → View → full page | Event handler → View → HTML string fragment |
| Opt-in | `$viewEngines` / `$cssFrameworks` on controller | None — `Plugin::view()` auto-detects `.blade.php` |
| Page shell | `<x-page-container>` | Renders inside host shell (member profile, group, etc.) |
| Variables | Set by controller on view object | Set by `onMembers()` / `onGroups()` on view object |

Plugins return an `$arr['html']` string that the host component injects into its
own page layout. Plugin views never use `<x-page-container>`.

## Creating a Blade View

Place `.blade.php` alongside the legacy `.php` template:

```
core/plugins/members/usage/views/summary/tmpl/
    default.php           ← legacy
    default.blade.php     ← new
```

The `Plugin::view()` method checks for `.blade.php` first when the active
template supports Blade. No configuration needed.

## Variable Translation

| Legacy PHP | Blade |
|------------|-------|
| `$this->member` | `$member` |
| `$this->option` | `$option` |
| `$this->escape($val)` | `e($val)` or `{{ $val }}` |
| `$this->getError()` | `$__view->getError()` |
| `$this->css()` / `$this->js()` | `$__view->css()` / `$__view->js()` |
| `$this->view('partial')` | `$__view->view('partial')` |
| `$this->editor(...)` | `$__view->editor(...)` |

### Sub-template rendering

```blade
@php
  $__view->view('_comment')
      ->set('option', $option)
      ->set('comment', $comment)
      ->set('depth', $depth)
      ->display();
@endphp
```

Use `@php` blocks for `display()` calls (which echo directly). For partials
that return strings, use `{!! $__view->view('_item')->set(...)->loadTemplate() !!}`.

## Language File Encoding (INI)

**This is the most common source of display bugs in plugin Blade views.**

### Use UTF-8 characters, not HTML entities

Blade's `{{ }}` auto-escapes output. If an INI value contains HTML entities,
they get double-encoded:

```ini
; BAD — renders as literal "R&eacute;sum&eacute;" in Blade
PLG_RESUME="R&eacute;sum&eacute;"

; GOOD — renders as "Résumé"
PLG_RESUME="Résumé"
```

The same applies to `&#34;` (double quote), `&#39;` (apostrophe), and any other
HTML entity. Use actual UTF-8 characters instead.

### Quotes inside INI values

INI values are delimited by double quotes. You cannot embed literal double
quotes inside the value — the INI parser will truncate at the first unmatched
quote.

```ini
; BROKEN — parser sees value as "Table 3: " then garbage
PLG_USAGE_CAPTION="Table 3: "and more" Usage"

; OK — use single quotes
PLG_USAGE_CAPTION="Table 3: 'and more' Usage"
```

### HTML in language strings

If a language value contains HTML (links, formatting), the template must use
unescaped output `{!! !!}`:

```ini
; Use single quotes for href to avoid breaking INI quoting
PLG_USAGE_EXPLANATION="Visit the <a href='/usage'>Usage Overview</a> page."
```

```blade
{{-- Use {!! !!} so the <a> tag renders as HTML, not escaped text --}}
<p>{!! Lang::txt('PLG_USAGE_EXPLANATION') !!}</p>
```

### Format strings with `%s`

Language keys with `%s` placeholders are for `sprintf()`-style formatting.
Don't use them directly as display labels:

```ini
; This is a format string — NOT a display label
PLG_MESSAGES_UNREAD="%s Unread Messages"

; Create a separate key for the display label
PLG_MESSAGES_STATUS_UNREAD="Unread"
```

```blade
{{-- Count display — use sprintf --}}
{{ sprintf(Lang::txt('PLG_MESSAGES_UNREAD'), $count) }}

{{-- Status badge — use the label key --}}
<span class="badge badge-sm">{{ Lang::txt('PLG_MESSAGES_STATUS_UNREAD') }}</span>
```

### Format strings with embedded HTML

Some language strings use `%s` formatting **and** contain HTML:

```ini
PLG_RESOURCES_QUESTIONS_VOTE_LIKES="%s<span> Like</span>"
```

When `Lang::txt()` substitutes the `%s` and returns HTML, you must use
unescaped output:

```blade
@php $likesTxt = Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_LIKES', $count); @endphp

{{-- BAD — renders literal "0<span> Like</span>" --}}
{{ $likesTxt }}

{{-- GOOD — renders "0 Like" with proper HTML --}}
{!! $likesTxt !!}
```

**Rule of thumb**: If a `Lang::txt()` call shows `<span>`, `<a>`, or other
tags as visible text, switch from `{{ }}` to `{!! !!}`. Only do this for
trusted language strings — never for user input.

Also note: `Lang::txt()` returns the key string itself (not `null`) when a key
is not found, so `Lang::txt('MISSING_KEY') ?? 'fallback'` will never reach the
fallback. Check with `Lang::hasKey()` or compare the return value to the key.

## CSS Framework Gating in PHP

Plugins that output HTML directly in PHP (not via Blade templates) must gate
on the CSS framework. Common cases:

### Error messages returned as HTML strings

```php
use Hubzero\Facades\Document;

$cls = Document::getCssFramework() === 'daisyui' ? 'alert alert-error' : 'error';
$arr['html'] = '<p class="' . $cls . '">'
    . Lang::txt('PLG_MEMBERS_USAGE_ERROR_MISSING_TABLE') . '</p>';
```

### Static `out()` methods

Some plugins have static methods that return formatted HTML (e.g., link
snippets, badges). Gate these with a framework check:

```php
public static function out($data)
{
    if (Document::getCssFramework() === 'daisyui') {
        return '<a class="link link-hover text-primary" href="' . $url . '">'
            . e($data->title) . '</a>'
            . '<span class="text-sm text-base-content/70">' . e($data->subtitle) . '</span>';
    }

    // Legacy markup
    return '<a href="' . $url . '">' . htmlspecialchars($data->title) . '</a>'
        . '<span class="subtitle">' . htmlspecialchars($data->subtitle) . '</span>';
}
```

## Tailwind Source Scanning

Blade templates in plugin view directories are scanned via glob patterns in
`app/templates/hubzero/css/site.src.css`:

```css
/* Plugin Blade templates */
@source "../../../../core/plugins/members/*/views/**/*.blade.php";

/* Plugin PHP files with inline daisyUI classes (out() methods, error messages) */
@source "../../../../core/plugins/members/*/*.php";
```

The second `@source` line is needed when PHP plugin files contain daisyUI class
strings in gated `out()` methods or inline HTML returns. Without it, Tailwind
won't generate those utility classes.

After adding new classes or `@source` directives:

```bash
cd app/templates/hubzero && node_modules/.bin/tailwindcss -i css/site.src.css -o css/site.css --minify
```

## Email Templates

Email templates (e.g., `digest_html.blade.php`, `digest_plain.blade.php`) are
an exception to all the above rules:

- **Inline styles are correct** — email clients don't support external CSS
- **Table-based layout is correct** — email clients don't support flexbox/grid
- **No daisyUI classes** — email clients won't load the stylesheet
- **No CSP concerns** — emails aren't rendered in a browser security context

Keep email templates with inline styles and table layout. This is intentional,
not technical debt.

## Common daisyUI Patterns in Plugin Views

### Status badges

```blade
<span @class([
    'badge badge-sm',
    'badge-ghost' => $item->isRead(),
    'badge-info'  => !$item->isRead(),
])>
    {{ $item->isRead() ? Lang::txt('PLG_MESSAGES_READ') : Lang::txt('PLG_MESSAGES_STATUS_UNREAD') }}
</span>
```

### Empty state (no content yet)

```blade
@if ($authorized)
  <div class="text-center py-12">
    <h4 class="text-lg font-semibold mb-2">{{ Lang::txt('PLG_MEMBERS_BLOG_NO_ENTRIES') }}</h4>
    <p class="text-base-content/60 mb-4">{{ Lang::txt('PLG_MEMBERS_BLOG_START_WRITING') }}</p>
    <a class="btn btn-primary btn-sm" href="{{ Route::url($base . '&task=new') }}">
      {{ Lang::txt('PLG_MEMBERS_BLOG_NEW_ENTRY') }}
    </a>
  </div>
@endif
```

### Data tables

```blade
<div class="overflow-x-auto">
  <table class="table table-zebra w-full">
    <caption class="text-left text-sm font-semibold mb-2">{{ $caption }}</caption>
    <thead>
      <tr>
        <th>{{ Lang::txt('...') }}</th>
        <th class="text-right">{{ Lang::txt('...') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($rows as $row)
        <tr>
          <td>{{ $row->title }}</td>
          <td class="text-right">{{ number_format($row->count) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="2" class="text-center text-base-content/60">
            {{ Lang::txt('PLG_NO_RESULTS') }}
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
```

### Comment threads (recursive)

Use two partials: `_list.blade.php` (container) and `_comment.blade.php`
(single item). The comment partial includes `_list` for its replies, creating
recursion.

**Important: Use CSS grid, not flexbox, for comments with nested replies.**

The `<li class="comment">` element has three children: a photo element, a
content `<div>`, and a nested replies `<ol>`. Using `display: flex` causes the
nested `<ol>` to compete as a third flex child, collapsing the content div to
`width: 0px`.

Use CSS grid with explicit column/row assignments:

```css
/* .blade.css */
ol.comments .comment {
  display: grid;
  grid-template-columns: 3rem 1fr;
  gap: 0 1rem;
  padding: 1rem 0;
  border-bottom: 1px solid var(--color-border);
}

ol.comments .comment-member-photo {
  grid-column: 1;
  grid-row: 1;
}

ol.comments .comment-content {
  grid-column: 2;
  grid-row: 1;
  min-width: 0;
}

/* Nested replies span both columns, indented */
ol.comments .comment > ol.comments {
  grid-column: 1 / -1;
  margin-left: 4rem;
}
```

```blade
{{-- _comment.blade.php --}}
<li class="comment">
  <p class="comment-member-photo"><img ... /></p>
  <div class="comment-content">
    <p class="font-medium">{{ $comment->creator->get('name') }}</p>
    <div class="prose prose-sm">{!! $comment->get('content') !!}</div>
  </div>
  {{-- Nested replies — third child of <li>, handled by grid --}}
  @php
    $__view->view('_list')
        ->set('comments', $comment->replies)
        ->set('depth', $depth + 1)
        ->display();
  @endphp
</li>
```

**Diagnosis**: If comment text appears in a very narrow column, inspect the
computed width of `.comment-content`. If it's `0px`, the layout is using flex
instead of grid.

## Host Component Views

Plugins render inside a **host component** (com_members, com_groups) that provides
the page shell, sidebar navigation, and toolbar. When converting plugins to Blade,
these host views often need a daisyUI code path too.

### Key host view files

| Component | File | Purpose |
|-----------|------|---------|
| com_groups | `views/groups/tmpl/view.blade.php` | Two-pane layout (sidebar + content) |
| com_groups | `views/groups/tmpl/_menu.blade.php` | Plugin tab navigation sidebar |
| com_groups | `views/groups/tmpl/_toolbar.blade.php` | Membership action buttons |
| com_members | `views/profiles/tmpl/view.blade.php` | Member profile layout |

Host views use `{!! $content !!}` to inject the plugin's rendered HTML fragment.

### Dual code path pattern

When a shared Blade template must render differently for daisyUI vs legacy CSS,
gate on the framework:

```blade
@php
  use Hubzero\Facades\Document;
  $isDaisyUI = Document::getCssFramework() === 'daisyui';
@endphp

@if($isDaisyUI)
  {{-- daisyUI markup --}}
  <ul class="menu bg-base-100 rounded-box w-full">
    ...
  </ul>
@else
  {{-- Legacy markup — must render identically to original --}}
  <ul {!! $classOrId !!}>
    ...
  </ul>
@endif
```

**Critical rule**: The legacy `@else` branch must be a verbatim copy of the
original template output. Don't "improve" it — the legacy CSS depends on exact
HTML structure, classes, and attributes.

## Sidebar Navigation Menus

### SVG icon mapping

Map plugin names to inline SVG icons (Heroicons outline, 20×20 viewBox). Define
the map in a `@php` block and look up each plugin by name:

```blade
@php
  $iconMap = [
      'overview'      => '<svg class="w-5 h-5" ...>...</svg>',
      'members'       => '<svg class="w-5 h-5" ...>...</svg>',
      'blog'          => '<svg class="w-5 h-5" ...>...</svg>',
      // ... more plugins
  ];
  $fallbackIcon = '<svg class="w-5 h-5" ...><!-- generic document icon --></svg>';
@endphp

@foreach($tabs as $tab)
  @php $svg = $iconMap[$tab['name']] ?? $fallbackIcon; @endphp
  <li>
    <a href="{{ $tab['link'] }}" class="{{ $tab['active'] ? 'active' : '' }}">
      {!! $svg !!}
      <span class="flex-1">{{ $tab['title'] }}</span>
    </a>
  </li>
@endforeach
```

Use `{!! $svg !!}` (unescaped) since these are trusted static strings.

### Count badges

Plugin tabs often show metadata counts (member count, post count). Use a ghost
badge:

```blade
@if($meta_count)
  <span class="badge badge-sm badge-ghost">{{ $meta_count }}</span>
@endif
```

### Restricted / locked tabs

For tabs the user can't access, render a non-interactive item with reduced opacity
and a lock icon:

```blade
<li class="disabled" title="{{ $restrictMsg }}">
  <span class="opacity-50">
    {!! $svg !!}
    <span>{{ $title }}</span>
    <svg class="w-4 h-4 ml-auto opacity-50" fill="none" viewBox="0 0 24 24"
         stroke-width="1.5" stroke="currentColor" aria-hidden="true">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5
               a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75
               a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
    </svg>
  </span>
</li>
```

### CSS ID collision gotcha

**This is the most common styling bug when adding daisyUI paths to host views.**

Legacy CSS targets elements by ID (e.g., `#page_menu li a`). If the daisyUI path
reuses the same ID, legacy selectors will override daisyUI component styles because
ID selectors have higher specificity.

```blade
{{-- BAD — legacy CSS rules for #page_menu override daisyUI's .menu styles --}}
<ul id="page_menu" class="menu bg-base-100 rounded-box w-full">

{{-- GOOD — no ID on the daisyUI path --}}
<ul class="menu bg-base-100 rounded-box w-full">
```

The legacy path keeps the original ID so its CSS continues to work:

```blade
@else
  <ul id="page_menu">
    ...
  </ul>
@endif
```

**Symptoms**: Active state highlight invisible, unexpected padding/margins, icon
rendering broken (Fontcons `::before` pseudo-elements appearing on SVG items).

**Diagnosis**: Use browser DevTools to check computed styles on the affected element.
Look for legacy selectors with ID specificity overriding class-based daisyUI rules.

## Framework-Specific Assets

Plugins can load CSS/JS variants based on the active framework. Name the variant
files with a `.blade` infix:

```
assets/css/
    reviews.css            ← legacy CSS (loaded in classic mode)
    reviews.blade.css      ← daisyUI CSS (loaded in daisyUI mode)
assets/js/
    reviews.js             ← legacy JS (jQuery, inline handlers)
    reviews.blade.js       ← daisyUI JS (vanilla JS, CSP-safe)
```

### How Auto-Resolution Works

When a Blade template calls `$__view->css('reviews')` or `$__view->js('reviews')`,
the `Css` and `Javascript` view helpers check the active CSS framework via
`Document::getCssFramework()`. When the framework is `'daisyui'`, the
`resolveBladeAsset()` method in `Hubzero\View\Helper\Css` (line 78) substitutes
`.blade.css` for `.css` before loading.

**Key file**: `core/libraries/Hubzero/View/Helper/Css.php:78-88`

```php
protected function resolveBladeAsset($path)
{
    if (Document::getCssFramework() !== 'daisyui') {
        return $path;
    }
    $bladePath = preg_replace('/\.css$/', '.blade.css', $path);
    return file_exists($bladePath) ? $bladePath : $path;
}
```

**Critical implication**: Without a `.blade.css` file, the plugin falls back to
the legacy `.css` file, which uses hardcoded colors and legacy selectors that
clash with daisyUI. When converting a plugin to Blade, always create both
`.blade.css` and `.blade.js` files.

### `.blade.css` Conventions

- Use CSS custom properties from daisyUI: `var(--color-primary)`,
  `var(--color-base-content)`, `var(--color-border)`, `var(--radius-field)`,
  `var(--radius-box)`
- Use opacity syntax: `var(--color-base-content/0.5)` for muted text
- Use flexbox/grid instead of floats
- Use relative units (`rem`) instead of `px`

### `.blade.js` Conventions

- Vanilla JS only — no jQuery dependency
- CSP-safe: no inline event handlers, no `javascript:` URLs
- Use `data-*` attribute delegation for interactive behavior
- Use `fetch()` for AJAX instead of `$.ajax()`
- Wrap in `document.addEventListener('DOMContentLoaded', function () { ... })`

## Semantic Colors

**All daisyUI paths must use semantic color tokens exclusively.** Never use
hardcoded hex, RGB, or named Tailwind colors (e.g., `text-blue-500`, `bg-gray-200`).

| Instead of | Use |
|------------|-----|
| `bg-white` | `bg-base-100` |
| `bg-gray-100` | `bg-base-200` |
| `text-gray-500` | `text-base-content/60` |
| `text-blue-600` | `text-primary` |
| `border-gray-300` | `border-base-300` |
| `bg-green-100 text-green-800` | `badge-success` or `alert-success` |
| `bg-red-100 text-red-800` | `badge-error` or `alert-error` |

Semantic tokens adapt to the active theme (light, dark, custom). Hardcoded colors
break theme switching and create visual inconsistencies.

## Conversion Checklist

When converting a plugin's views to Blade:

1. **No controller opt-in needed** — `Plugin::view()` auto-detects `.blade.php`
2. **Create `.blade.php` files** alongside existing `.php` templates
3. **Translate variables** — `$this->prop` → `$prop`, helpers via `$__view`
4. **Audit language files** — replace HTML entities with UTF-8 characters
5. **Audit language usage** — ensure format strings (`%s`) aren't used as labels
6. **Gate PHP HTML output** — any `out()`, error messages, or inline HTML in the
   plugin class must check `Document::getCssFramework()`
7. **Check host views** — if the host component (com_members, com_groups) has
   sidebar menus or toolbars, add a daisyUI code path there too
8. **Avoid CSS ID collisions** — don't reuse legacy HTML IDs on daisyUI elements
9. **Use semantic colors only** — no hardcoded hex/RGB/named Tailwind colors
10. **Add `@source` directives** — for both `.blade.php` and `.php` files with
    daisyUI classes
11. **Rebuild Tailwind CSS**
12. **Test in browser** — clear Blade cache (`rm -f app/cache/views/*.php`),
    verify rendering
13. **Check email templates** — keep inline styles, don't convert to daisyUI
