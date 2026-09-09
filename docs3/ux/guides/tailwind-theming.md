# Tailwind + daisyUI Theming Guide

How to set up and customize the Hubzero design system using
[Tailwind CSS](https://tailwindcss.com/) and
[daisyUI](https://daisyui.com/).

## Browser Requirements

Tailwind CSS v4 and daisyUI 5 require modern browsers that support
`@property`, `color-mix()`, and `oklch()`:

| Browser | Minimum Version | Release Date |
|---------|-----------------|--------------|
| Chrome | 111+ | March 2023 |
| Edge | 111+ | March 2023 |
| Firefox | 128+ | July 2024 |
| Safari | 16.4+ | March 2023 |
| iOS Safari | 16.4+ | March 2023 |
| Samsung Internet | 22+ | March 2023 |

Older browsers will not render colors or certain layout features correctly.
If you must support browsers older than these versions, use
Tailwind v3 + daisyUI 4 instead.

**Fallback option**: daisyUI 4 remains available for projects that need
broader backward compatibility. It pairs with Tailwind v3, which supports
all browsers with >0.5% market share.

## Why Tailwind + daisyUI

- **Tailwind CSS** provides utility classes for layout, spacing, typography
- **daisyUI** adds semantic component classes (`.btn`, `.card`, `.alert`, etc.)
  so PHP templates stay readable
- Both are MIT licensed
- daisyUI's class names are an industry standard (similar to Bootstrap)
- Built-in theme system with CSS variables — no recompilation needed

## Setup

### Install

```bash
npm install tailwindcss @tailwindcss/typography daisyui
```

### Configure Tailwind

```js
// tailwind.config.js
import daisyui from 'daisyui'

export default {
  content: [
    'app/templates/**/*.php',
    'core/templates/**/*.php',
    'core/components/**/tmpl/**/*.php',
    'packages/hubzero/components/**/tmpl/**/*.php',
  ],
  plugins: [
    require('@tailwindcss/typography'),
    daisyui,
  ],
  daisyui: {
    themes: ['hubzero', 'hubzero-dark'],
  },
}
```

### Main CSS

```css
/* resources/css/app.css */
@tailwind base;
@tailwind components;
@tailwind utilities;
```

## daisyUI Class Reference

Every class used in the page type docs maps to a daisyUI or Tailwind component.
No custom/invented class names are needed.

### Actions

| Class | Component | Doc |
|-------|-----------|-----|
| `.btn` | Button | [daisyui.com/components/button](https://daisyui.com/components/button/) |
| `.btn-primary` | Primary action | |
| `.btn-ghost` | Subtle / cancel | |
| `.btn-error` | Destructive action | |
| `.btn-outline` | Outlined variant | |
| `.btn-sm`, `.btn-xs` | Size modifiers | |

### Data Display

| Class | Component | Doc |
|-------|-----------|-----|
| `.card` | Card container | [daisyui.com/components/card](https://daisyui.com/components/card/) |
| `.card-body` | Card content area | |
| `.card-title` | Card heading | |
| `.card-actions` | Card button area | |
| `.badge` | Status pill | [daisyui.com/components/badge](https://daisyui.com/components/badge/) |
| `.badge-success` | Green status | |
| `.badge-warning` | Amber status | |
| `.badge-info` | Blue status | |
| `.badge-error` | Red status | |
| `.badge-outline` | Outlined (tags) | |
| `.stats` | Stat container | [daisyui.com/components/stat](https://daisyui.com/components/stat/) |
| `.stat` | Single stat | |
| `.stat-title` | Stat label | |
| `.stat-value` | Stat number | |
| `.list` | List container | [daisyui.com/components/list](https://daisyui.com/components/list/) |
| `.list-row` | List item | |
| `.list-col-grow` | Flexible column | |
| `.table` | Data table | [daisyui.com/components/table](https://daisyui.com/components/table/) |
| `.avatar` | User photo | [daisyui.com/components/avatar](https://daisyui.com/components/avatar/) |
| `.divider` | Section separator | [daisyui.com/components/divider](https://daisyui.com/components/divider/) |

### Navigation

| Class | Component | Doc |
|-------|-----------|-----|
| `.tabs` | Tab container | [daisyui.com/components/tab](https://daisyui.com/components/tab/) |
| `.tab` | Single tab | |
| `.tab-active` | Active tab | |
| `.tabs-border` | Bordered style | |
| `.menu` | Link list | [daisyui.com/components/menu](https://daisyui.com/components/menu/) |
| `.menu-sm` | Compact menu | |
| `.steps` | Step indicator | [daisyui.com/components/steps](https://daisyui.com/components/steps/) |
| `.step` | Single step | |
| `.step-primary` | Completed step | |
| `.join` | Join items | [daisyui.com/components/join](https://daisyui.com/components/join/) |
| `.join-item` | Joined element | |
| `.link` | Styled link | [daisyui.com/components/link](https://daisyui.com/components/link/) |
| `.link-hover` | Underline on hover | |
| `.link-primary` | Brand-colored link | |
| `.breadcrumbs` | Breadcrumb nav | [daisyui.com/components/breadcrumbs](https://daisyui.com/components/breadcrumbs/) |

### Feedback

| Class | Component | Doc |
|-------|-----------|-----|
| `.alert` | Alert message | [daisyui.com/components/alert](https://daisyui.com/components/alert/) |
| `.alert-error` | Error message | |
| `.alert-warning` | Warning message | |
| `.alert-info` | Info message | |
| `.alert-success` | Success message | |
| `.loading` | Spinner | [daisyui.com/components/loading](https://daisyui.com/components/loading/) |
| `.tooltip` | Tooltip | [daisyui.com/components/tooltip](https://daisyui.com/components/tooltip/) |

### Data Input

| Class | Component | Doc |
|-------|-----------|-----|
| `.input` | Text input | [daisyui.com/components/input](https://daisyui.com/components/input/) |
| `.input-bordered` | Bordered input | |
| `.textarea` | Textarea | [daisyui.com/components/textarea](https://daisyui.com/components/textarea/) |
| `.textarea-bordered` | Bordered textarea | |
| `.select` | Select dropdown | [daisyui.com/components/select](https://daisyui.com/components/select/) |
| `.select-bordered` | Bordered select | |
| `.checkbox` | Checkbox | [daisyui.com/components/checkbox](https://daisyui.com/components/checkbox/) |
| `.radio` | Radio button | [daisyui.com/components/radio](https://daisyui.com/components/radio/) |
| `.file-input` | File upload | [daisyui.com/components/file-input](https://daisyui.com/components/file-input/) |
| `.fieldset` | Field group | [daisyui.com/components/fieldset](https://daisyui.com/components/fieldset/) |
| `.fieldset-legend` | Group title | |
| `.label` | Field label | [daisyui.com/components/label](https://daisyui.com/components/label/) |
| `.label-text` | Label text | |
| `.label-text-alt` | Hint/error text | |
| `.form-control` | Field wrapper | daisyUI layout helper |

### Layout (Tailwind utilities)

These are standard Tailwind, not daisyUI:

| Pattern | Purpose |
|---------|---------|
| `max-w-7xl mx-auto px-4` | Page container |
| `lg:grid lg:grid-cols-[1fr_280px] lg:gap-8` | Sidebar layout |
| `grid gap-6 sm:grid-cols-2` | Dashboard widget grid |
| `flex items-center justify-between` | Header/action rows |
| `space-y-6` | Vertical spacing |
| `prose max-w-none` | Rich text formatting (typography plugin) |

### Color Tokens (daisyUI semantic)

daisyUI uses semantic color names that adapt to the active theme:

| Token | Purpose |
|-------|---------|
| `base-100` | Lightest — cards, list rows, elevated surfaces |
| `base-200` | Mid — main content reading area |
| `base-300` | Darkest — page body/background, visible as gutter |
| `base-content` | Default text |
| `primary` | Brand action color |
| `primary-content` | Text on primary |
| `error` | Destructive actions |
| `warning` | Caution indicators |
| `success` | Positive status |
| `info` | Informational |

> **Three-tier background system**: The Hubzero template inverts the typical
> daisyUI convention where `base-100` is the page background. Instead, the
> body uses `bg-base-300` (darkest), the main content area uses `bg-base-200`
> (mid), and cards/list items use `bg-base-100` (lightest). This creates
> clear visual depth without borders or shadows.

### Semantic Color Tokens (admin)

The admin template defines semantic color tokens as CSS custom properties that
**auto-derive from any daisyUI 5 theme** using `color-mix()` in oklch. These
are defined in `core/templates/hzadmin/css/admin.src.css` inside a
`[data-theme]` block — any community theme populates them automatically.

**File**: `core/templates/hzadmin/css/admin.src.css` (after the
`@plugin "daisyui/theme"` block)

#### Background: Why tokens instead of opacity

Tailwind's opacity modifier `text-base-content/60` mixes the color with
`transparent`. The resulting contrast depends on whatever is behind the
element — it passes WCAG AA on white but may fail on `base-200` or `base-300`.
When we enforced WCAG AA compliance, all varied opacity levels (`/40`, `/50`,
`/60`) were flattened to `/70`, destroying the visual hierarchy: table headers,
metadata, hints, and placeholders all became the same shade.

The fix: mix with the **target background** (`base-100`) instead of
`transparent`. `color-mix(in oklch, base-content 55%, base-100)` produces a
solid color with deterministic contrast, regardless of compositing.

#### Why oklch

[oklch](https://developer.mozilla.org/en-US/docs/Web/CSS/color_value/oklch)
(OK Lightness Chroma Hue) is a perceptually uniform color space — equal
numeric steps produce equal perceived differences. Tailwind v4 and daisyUI 5
use oklch internally. Mixing in oklch gives more predictable, natural-looking
results than sRGB mixing.

#### Naming conventions

Token names follow [shadcn/ui](https://ui.shadcn.com/) — the de facto standard
in the Tailwind ecosystem:

- `--{role}` for backgrounds (e.g. `--muted`)
- `--{role}-foreground` for text on that background (e.g. `--muted-foreground`)

shadcn provides only 2 text levels (`--foreground` and `--muted-foreground`).
We extend with `--subtle-foreground` and `--faint-foreground` for a 4-level
hierarchy, following the pattern used by
[Radix Themes](https://www.radix-ui.com/themes) (`subtle` is an established
design token name).

| Our token | shadcn equivalent | Material 3 equivalent |
|-----------|-------------------|----------------------|
| `--foreground` | `--foreground` | `on-surface` |
| `--muted-foreground` | `--muted-foreground` | `on-surface-variant` |
| `--subtle-foreground` | _(extended)_ | — |
| `--faint-foreground` | _(extended)_ | — |
| `--muted` | `--muted` | `surface-variant` |
| `--accent-surface` | `--accent` | `surface-container-high` |
| `--border` | `--border` | `outline-variant` |
| `--input` | `--input` | — |
| `--ring` | `--ring` | — |

Note: `--foreground` is implicit — just use daisyUI's `text-base-content`.

#### Text Hierarchy

| Utility | CSS Variable | Role | Derivation |
|---------|-------------|------|------------|
| `text-base-content` | (daisyUI built-in) | Primary text — headings, body | `--color-base-content` |
| `text-muted-foreground` | `--muted-foreground` | Labels, table headers, form labels | base-content 55% + base-100 |
| `text-subtle-foreground` | `--subtle-foreground` | Hints, timestamps, descriptions | base-content 40% + base-100 |
| `text-faint-foreground` | `--faint-foreground` | Placeholders, disabled, tree indents | base-content 28% + base-100 |

#### Status Text on Base Backgrounds

For status-colored text on `base-100`/`base-200` backgrounds — not text on a
status-colored background (daisyUI's `*-content` tokens handle that case).

| Utility | CSS Variable | Replaces | Derivation |
|---------|-------------|----------|------------|
| `text-success-fg` | `--success-foreground` | `.text-success-dark` (#15803d) | success 60% + black |
| `text-error-fg` | `--error-foreground` | ad-hoc error darkening | error 60% + black |
| `text-warning-fg` | `--warning-foreground` | `.text-accent-dark` (#b45309) | warning 60% + black |
| `text-info-fg` | `--info-foreground` | ad-hoc info darkening | info 60% + black |
| `text-primary-on-base` | `--primary-foreground-on-base` | `.text-primary-dark` (#076b63) | primary 70% + black |

#### Surfaces

| Utility | CSS Variable | Role | Derivation |
|---------|-------------|------|------------|
| `bg-muted` | `--muted` | De-emphasized areas | base-content 4% + base-100 |
| `bg-accent-surface` | `--accent-surface` | Hover/highlight bg | base-content 8% + base-100 |
| `bg-accent-primary` | `--accent-primary` | Primary-tinted hover | primary 10% + base-100 |
| — | `--accent-primary-subtle` | Primary-tinted bg | primary 5% + base-100 |
| — | `--success-surface` | Success alert bg | success 10% + base-100 |
| — | `--error-surface` | Error alert bg | error 10% + base-100 |
| — | `--warning-surface` | Warning alert bg | warning 10% + base-100 |
| — | `--info-surface` | Info alert bg | info 10% + base-100 |

#### Borders

| Utility | CSS Variable | Role | Derivation |
|---------|-------------|------|------------|
| `border-default` | `--border` | Default dividers, cards | base-content 20% + base-100 |
| `border-strong` | `--border-strong` | Emphasis/hover borders | base-content 35% + base-100 |
| — | `--input` | Input field borders | base-content 28% + base-100 |
| — | `--ring` | Focus ring | primary 25% + base-100 |
| — | `--border-primary` | Primary-accent borders | primary 45% + base-100 |

#### Not tokenized

Some `color-mix()` calls remain as raw expressions in `admin.src.css`:

- **Box-shadows** — decorative depth effects, not contrast-sensitive
- **Focus ring shadows** — `box-shadow: 0 0 0 3px color-mix(primary 15%...)`
- **Elements on primary/neutral backgrounds** — popup headers, picker chrome,
  config modal buttons use `primary-content` or `neutral-content` mixed with
  `transparent` (correct since the bg is the status color, not `base-100`)
- **One-off decorative** — outline button borders at 50% status color

These are either not contrast-sensitive or operate on non-base backgrounds
where the token derivation formula doesn't apply.

#### Community Theme Compatibility

The key design decision: tokens are defined in a `[data-theme]` selector (not
`[data-theme="hubzero"]`), so they match **any** daisyUI 5 theme:

```css
[data-theme] {
  --muted-foreground: color-mix(in oklch,
    var(--color-base-content) 55%, var(--color-base-100));
  /* ... */
}
```

Any daisyUI 5 theme (built-in or community) sets `--color-base-content` and
`--color-base-100`, so our tokens auto-derive. No per-theme configuration
required.

For the Hubzero theme specifically, you can optionally override with hand-tuned
values in `[data-theme="hubzero"]` if the auto-derived percentages aren't ideal
after contrast testing.

#### Scope

The tokens are defined in both templates:

- **Admin**: `core/templates/hzadmin/css/admin.src.css`
- **Site**: `app/templates/hubzero/css/site.src.css`

Both use the same semantic token system — oklch `color-mix()` with solid
`base-100` backgrounds instead of transparent, `@utility` registrations, and
the full text/surface/border/status token set. Dark themes auto-derive from
standard daisyUI tokens, so no additional dark-mode work is needed.

## Custom Theme & Dark / Light Mode

Hubzero supports user-selectable dark/light mode with three-way preference:

1. **System** — follows `prefers-color-scheme` (default)
2. **Light** — always light
3. **Dark** — always dark

### Define Both Themes

```js
// tailwind.config.js
daisyui: {
  themes: [
    {
      hubzero: {
        'primary':          '#0074c7',
        'primary-content':  '#ffffff',
        'secondary':        '#6b7280',
        'accent':           '#36adf8',
        'neutral':          '#1f2937',
        'base-100':         '#ffffff',
        'base-200':         '#f9fafb',
        'base-300':         '#e5e7eb',
        'base-content':     '#1f2937',
        'info':             '#3b82f6',
        'success':          '#16a34a',
        'warning':          '#d97706',
        'error':            '#dc2626',
        '--rounded-box':    '0.5rem',
        '--rounded-btn':    '0.375rem',
        '--rounded-badge':  '999px',
      },
    },
    {
      'hubzero-dark': {
        'primary':          '#38bdf8',
        'primary-content':  '#002b3d',
        'secondary':        '#9ca3af',
        'accent':           '#7dd3fc',
        'neutral':          '#1e293b',
        'base-100':         '#0f172a',
        'base-200':         '#1e293b',
        'base-300':         '#334155',
        'base-content':     '#e2e8f0',
        'info':             '#60a5fa',
        'success':          '#4ade80',
        'warning':          '#fbbf24',
        'error':            '#f87171',
        '--rounded-box':    '0.5rem',
        '--rounded-btn':    '0.375rem',
        '--rounded-badge':  '999px',
      },
    },
  ],
},
```

### Theme Toggle in Navbar

Place the toggle in the site header. Use daisyUI's swap component for
animated sun/moon icons:

```html
<label class="swap swap-rotate btn btn-ghost btn-circle"
       aria-label="Toggle dark mode">
  <input type="checkbox" class="theme-controller" value="hubzero-dark"
         id="theme-toggle" />

  <!-- Sun icon (shown when light/checked=false) -->
  <svg class="swap-off h-6 w-6 fill-current" aria-hidden="true"
       xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
    <path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,
    1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,
    12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,
    1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,
    4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,
    5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,
    2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,
    0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,
    0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,
    9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
  </svg>

  <!-- Moon icon (shown when dark/checked=true) -->
  <svg class="swap-on h-6 w-6 fill-current" aria-hidden="true"
       xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
    <path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,
    8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,
    10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,
    7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,
    8.11,0,0,1,12.14,19.73Z" />
  </svg>
</label>
```

### JavaScript: Persist and Restore Theme

This script handles three-way preference (system, light, dark) and persists
the choice in `localStorage`:

```html
<script>
// Run immediately (in <head> or inline) to prevent flash of wrong theme
(function () {
  const STORAGE_KEY = 'hubzero-theme';
  const LIGHT = 'hubzero';
  const DARK = 'hubzero-dark';

  function getSystemTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches
      ? DARK : LIGHT;
  }

  function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    // Sync the toggle checkbox
    const toggle = document.getElementById('theme-toggle');
    if (toggle) {
      toggle.checked = (theme === DARK);
    }
  }

  // Restore saved preference or fall back to system
  const saved = localStorage.getItem(STORAGE_KEY);
  applyTheme(saved || getSystemTheme());

  // Listen for system preference changes (when no manual override)
  window.matchMedia('(prefers-color-scheme: dark)')
    .addEventListener('change', function (e) {
      if (!localStorage.getItem(STORAGE_KEY)) {
        applyTheme(e.matches ? DARK : LIGHT);
      }
    });

  // Wire up toggle after DOM is ready
  document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('theme-toggle');
    if (!toggle) return;

    // Sync initial state
    toggle.checked = (document.documentElement.getAttribute('data-theme') === DARK);

    toggle.addEventListener('change', function () {
      const theme = this.checked ? DARK : LIGHT;
      applyTheme(theme);
      localStorage.setItem(STORAGE_KEY, theme);
    });
  });
})();
</script>
```

### Three-Way Dropdown (System / Light / Dark)

For a more explicit control that includes "follow system":

```html
<div class="dropdown dropdown-end">
  <div tabindex="0" role="button" class="btn btn-ghost btn-circle"
       aria-label="Theme settings">
    <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24"
         stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.95l-.71.71M21
            12h-1M4 12H3m16.95 7.95l-.71-.71M4.05 4.05l-.71-.71M16 12a4
            4 0 11-8 0 4 4 0 018 0z" />
    </svg>
  </div>
  <ul tabindex="0" class="dropdown-content menu bg-base-200 rounded-box
      shadow-lg z-50 w-40 p-2" role="listbox" aria-label="Theme selection">
    <li>
      <button class="theme-option" data-theme-value="system"
              role="option" aria-selected="true">
        System
      </button>
    </li>
    <li>
      <button class="theme-option" data-theme-value="hubzero"
              role="option" aria-selected="false">
        Light
      </button>
    </li>
    <li>
      <button class="theme-option" data-theme-value="hubzero-dark"
              role="option" aria-selected="false">
        Dark
      </button>
    </li>
  </ul>
</div>
```

```js
document.querySelectorAll('.theme-option').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var value = this.dataset.themeValue;

    // Update aria-selected
    document.querySelectorAll('.theme-option')
      .forEach(function (b) { b.setAttribute('aria-selected', 'false'); });
    this.setAttribute('aria-selected', 'true');

    if (value === 'system') {
      localStorage.removeItem('hubzero-theme');
      applyTheme(getSystemTheme());
    } else {
      localStorage.setItem('hubzero-theme', value);
      applyTheme(value);
    }
  });
});
```

### Preventing Flash of Wrong Theme (FOUC)

Place the theme restoration script in `<head>` before any CSS loads, so the
`data-theme` attribute is set before the first paint:

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <script>
    (function () {
      var saved = localStorage.getItem('hubzero-theme');
      if (!saved) {
        saved = window.matchMedia('(prefers-color-scheme: dark)').matches
          ? 'hubzero-dark' : 'hubzero';
      }
      document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>
  <link rel="stylesheet" href="/css/app.css" />
</head>
```

### Accessibility Requirements for Theme Toggle

- Toggle must have `aria-label="Toggle dark mode"`
- Icons are decorative: `aria-hidden="true"`
- Focus ring must be visible in both light and dark themes
- All color pairs must pass WCAG AA contrast (4.5:1 for text, 3:1 for UI)
- See [Accessibility Guide](accessibility.md) for full contrast requirements

## Component CSS Overrides

Each page adds the active component name as a class on `<body>`, following the
same pattern used by WordPress, Drupal, and legacy Joomla/Hubzero:

```html
<body class="com_blog bg-base-300 text-base-content min-h-screen flex flex-col">
```

In PHP templates this is set dynamically:

```php
<body class="<?php echo $this->option; ?> bg-base-300 text-base-content min-h-screen flex flex-col">
```

### Targeting a Specific Component

Override any daisyUI or Tailwind style for a single component:

```css
/* Only affects the blog component */
body.com_blog .card-title {
  font-family: 'Georgia', serif;
}

body.com_blog .badge-success {
  --tw-bg-opacity: 0.8;
}
```

### Targeting Multiple Components

```css
body.com_blog .list-row,
body.com_resources .list-row {
  border-left: 3px solid oklch(var(--p));
}
```

### Component-Specific Stylesheet

Keep overrides in a dedicated file loaded only when that component renders:

```
core/components/com_blog/site/assets/css/blog.css
```

```css
/* blog.css — scoped via body class */
body.com_blog .prose img {
  border-radius: var(--rounded-box);
}
```

This pattern provides clean separation without inventing custom class names or
requiring build-time configuration per component.

## Migration from Legacy Classes

Mapping from existing Hubzero/Bootstrap-style classes to daisyUI:

| Legacy class | daisyUI equivalent |
|---|---|
| `.btn-success` | `.btn-primary` (or keep `.btn-success` — daisyUI doesn't have it, use `.btn .bg-success .text-success-content`) |
| `.btn-secondary` | `.btn-ghost` |
| `.btn-danger` | `.btn-error` |
| `.form-control` (Bootstrap input) | `.input .input-bordered` |
| `.form-group` | `.form-control` (daisyUI wrapper) |
| `.form-check` | `.label .cursor-pointer` with `.checkbox`/`.radio` |
| `.form-check-input` | `.checkbox` or `.radio` |
| `.form-check-label` | `.label-text` |
| `.error` (message) | `.alert .alert-error` |
| `.warning` (message) | `.alert .alert-warning` |
| `.container` (sidebar block) | `.card .card-body` |
| `.entry-meta dl/dd` | Tailwind flex utilities |
| `#content-header` | Tailwind flex header |
| `.entries-menu` | `.tabs .tab` |
| `.pagination` | `.join .join-item .btn` |

## Responsive Behavior

Tailwind breakpoints used throughout:

| Breakpoint | Width | Layout behavior |
|------------|-------|-----------------|
| Default | < 640px | Single column, sidebar stacks below content |
| `sm` | 640px | Dashboard: 2-col widget grid |
| `lg` | 1024px | Sidebar layout activates |

Key responsive rules:
- Sidebar collapses below main content on mobile (`lg:grid`)
- Dashboard stats stay horizontal (`.stats` scrolls on mobile)
- Wizard steps remain horizontal (`.steps` scrolls on mobile)
- Filter tabs scroll horizontally if they overflow
