# Semantic CSS Classes

Master vocabulary of custom semantic classes used by component and module Blade
views. These classes describe **structure**, not appearance — the template CSS
(`site.src.css`) controls the visual styling.

> **Rule of thumb**: Use [global blade components](global-components.md) first
> — they emit the correct semantic classes automatically. Use daisyUI classes
> for standard UI (buttons, inputs, cards, badges). Only use raw semantic
> classes when no global component covers the pattern. Avoid raw Tailwind
> utilities in component views — those belong in the template.

## Page Structure

Use `<x-page-container>` ([docs](global-components.md#x-page-container)) instead
of raw HTML. It renders these semantic classes automatically:

| Class | Element | Purpose |
|-------|---------|---------|
| `.page-header` | `<header>` | Title bar with primary background color |
| `.page-header-content` | `<div>` | Title text container |
| `.page-header-actions` | `<div>` | Button group aligned right in page header |
| `.page-body` | `<section>` | Padded content area below page header |
| `.page-layout` | `<div>` | Main + sidebar grid (collapses on mobile) |
| `.page-main` | `<div>` | Primary content column (prevents overflow) |
| `.page-sidebar` | `<aside>` | Sidebar column with stacked cards |

**Defined in**: [blade-page-shell.md](../guides/blade-page-shell.md),
template CSS `site.src.css`

---

## Forms

Use `<x-form-section>` and `<x-form-field>` ([docs](global-components.md#forms))
instead of raw HTML. They emit these classes automatically:

| Class | Element | Purpose |
|-------|---------|---------|
| `.form-section` | `<fieldset>` | Card grouping related fields |
| `.form-section-heading` | `<legend>` | Section title bar |
| `.form-section-body` | `<div>` | Padded field area inside section |
| `.form-field` | `<div>` | Label + input + hint wrapper |
| `.form-field-label` | `<label>` | Label above a field |
| `.form-field-hint` | `<p>` | Helper or error text below a field |
| `.checkbox-label` | `<label>` | Bordered interactive row for checkbox/radio |
| `.form-actions` | `<div>` | Save/cancel button row (top border only) |

Use `<x-media-upload>` ([docs](global-components.md#x-media-upload)) for file
uploads. It emits:

| Class | Element | Purpose |
|-------|---------|---------|
| `.file-manager-card` | `<div>` | Sidebar file manager panel |
| `.file-manager-header` | `<div>` | File manager panel header |
| `.file-manager-dropzone` | `<label>` | Drop target area |
| `.file-manager-dropzone-icon` | `<svg>` | Upload icon |
| `.file-manager-dropzone-text` | `<span>` | "Click to upload" text |
| `.file-manager-dropzone-hint` | `<span>` | Max file size hint |
| `.file-manager-actions` | `<div>` | Upload button row |
| `.file-manager-list` | `<div>` | Existing file list |

**Defined in**: [edit-form.md](../patterns/edit-form.md)

---

## Entry Detail (Single Item)

Used by blog entries, resources, publications — any full item display.
Use `<x-author-card>` and `<x-tag-cloud>` global components where applicable.

| Class | Element | Purpose |
|-------|---------|---------|
| `.entry-meta` | `<div>` | Date, author, comment count row below title |
| `.entry-actions` | `<div>` | Edit/delete icon buttons (pushed right in meta) |
| `.entry-footer` | `<footer>` | Tags + actions below article body |

**Defined in**: [single-item.md](../patterns/single-item.md)

---

## Comments

Threaded comment system used by blog and other commentable content.

| Class | Element | Purpose |
|-------|---------|---------|
| `.comment-list` | `<div>` | Container for top-level comments |
| `.comment` | `<article>` | Single comment (avatar + body flex row) |
| `.comment-reply` | modifier | Added to `.comment` when depth > 0 (smaller avatar) |
| `.comment-meta` | `<div>` | Author name + timestamp row |
| `.comment-time` | `<a>` | Permalink timestamp |
| `.comment-body` | `<div>` | Comment content text |
| `.comment-actions` | `<div>` | Reply/edit/report/delete buttons |
| `.comment-replies` | `<div>` | Nested reply container (indented, left border) |
| `.comment-form` | `<form>` | Comment submission form |
| `.comment-form-body` | `<div>` | Textarea + footer bordered container |
| `.comment-textarea` | `<textarea>` | Comment text input |
| `.comment-form-footer` | `<div>` | Anonymous checkbox + submit bar |
| `.comment-form-anon` | `<label>` | Anonymous posting checkbox label |
| `.login-to-comment` | `<p>` | "Log in to post" info box for guests |
| `.entry-comments` | `<section>` | Comments section wrapper (top border + spacing) |

**Defined in**: [single-item.md](../patterns/single-item.md)

---

## Delete Confirmation

Centered card for destructive action gates.

| Class | Element | Purpose |
|-------|---------|---------|
| `.confirm-delete` | `<section>` | Centered container with padding |
| `.confirm-card` | `<div>` | White card with shadow |
| `.confirm-icon` | `<div>` | Warning icon container |
| `.confirm-title` | `<h2>` | "Delete X?" heading |
| `.confirm-desc` | `<p>` | Consequences description |
| `.confirm-impact` | `<div>` | Red-tinted box listing what will be removed |
| `.confirm-actions` | `<div>` | Delete/cancel button row |

**Defined in**: [delete-confirmation.md](../patterns/delete-confirmation.md)

---

## Button Variants

Beyond daisyUI's standard `btn-primary`, `btn-ghost`, `btn-error`, etc.:

| Class | Purpose |
|-------|---------|
| `.btn-danger` | Destructive action button (red background, white text). Used for delete confirmations and other irreversible actions. Defined globally — not scoped to a single page type. |

---

## Login

Authentication pages (login, logout, registration).

| Class | Element | Purpose |
|-------|---------|---------|
| `.login-wrapper` | `<div>` | Centered narrow container |
| `.login-card` | `.card` | Card with subtle border |
| `.login-icon` | `<div>` | Lock icon above heading |
| `.login-heading` | `<h2>` | "Sign in to..." title |
| `.login-providers` | `<div>` | Third-party auth button stack |
| `.login-divider` | `<div>` | "or" separator line |
| `.login-form` | `<form>` | Local credentials form |
| `.login-footer` | `<div>` | Forgot password/username links |
| `.login-register` | `<div>` | Create account prompt below card |
| `.login-register-text` | `<span>` | "Don't have an account?" text |

---

## Author Card

Use `<x-author-card>` ([docs](global-components.md#x-author-card)).

| Class | Element | Purpose |
|-------|---------|---------|
| `.author-card` | `<div>` | Author info sidebar card |
| `.author-card-inner` | `<div>` | Avatar + info flex row |
| `.author-card-avatar` | `<div>` | Circular avatar container |
| `.author-card-info` | `<div>` | Name + affiliation text |
| `.author-card-name` | `<h3>` | Author name |
| `.author-card-affiliation` | `<p>` | Organization / department |
| `.author-card-bio` | `<p>` | Short biography |
| `.author-card-extra` | `<div>` | Extra content slot |

---

## Tags

Use `<x-tag-cloud>` ([docs](global-components.md#x-tag-cloud)).

| Class | Element | Purpose |
|-------|---------|---------|
| `.tags` | `<ol>` | Tag list container |
| `.tag` | `<a>` | Individual tag pill |

---

## Empty State

Use `<x-empty-state>` ([docs](global-components.md#x-empty-state)).

| Class | Element | Purpose |
|-------|---------|---------|
| `.empty-state` | `<div>` | Centered placeholder container |
| `.empty-state-icon` | `<div>` | Icon container |
| `.empty-state-title` | `<h3>` | Heading text |
| `.empty-state-message` | `<p>` | Description text |
| `.empty-state-action` | `<div>` | Action button slot |

---

## Vote Widget

Use `<x-vote-widget>` ([docs](global-components.md#x-vote-widget)).

| Class | Element | Purpose |
|-------|---------|---------|
| `.vote-widget` | `<span>` | Widget container |
| `.vote-btn` | `<a>/<span>` | Vote button |
| `.vote-icon` | `<svg>` | Thumb icon |
| `.vote-active` | modifier | Highlighted when user voted |
| `.vote-disabled` | modifier | Grayed out when disabled |

---

## Stat Card

Use `<x-stat-card>` ([docs](global-components.md#x-stat-card)).

| Class | Element | Purpose |
|-------|---------|---------|
| `.stat-card` | `<div>` | Metric card container |
| `.stat-card-label` | `<div>` | Metric label |
| `.stat-card-value` | `<div>` | Metric value |
| `.stat-card-desc` | `<div>` | Extra context |
| `.stat-trend-up` | modifier | Green color for positive trend |
| `.stat-trend-down` | modifier | Red color for negative trend |

---

## Step Navigation

Use `<x-step-nav>` ([docs](global-components.md#x-step-nav)).

| Class | Element | Purpose |
|-------|---------|---------|
| `.step-nav` | `<ul>` | Step list with CSS counters |
| `.step-nav-item` | `<li>` | Individual step |
| `.step-completed` | modifier | Checkmark instead of number |
| `.step-current` | modifier | Highlighted current step |

---

## Card Grid

Use `<x-card-grid>` ([docs](global-components.md#x-card-grid)).

| Class | Element | Purpose |
|-------|---------|---------|
| `.card-grid` | `<div>` | Responsive grid container |
| `.card-grid-3` | modifier | 3-column layout |
| `.card-grid-4` | modifier | 4-column layout |

---

## Poll

Component-specific classes used by `com_poll` blade views.

| Class | Element | Purpose |
|-------|---------|---------|
| `.poll-card` | `<div>` | Card for one poll in grid |
| `.poll-card-body` | `<div>/<fieldset>` | Card content area |
| `.poll-card-title` | `<h3>/<legend>` | Poll question heading |
| `.poll-card-actions` | `<div>` | Vote/results buttons |
| `.poll-card-footer` | `<div>` | Vote count + status |
| `.poll-options` | `<ul>` | Radio button option list |
| `.poll-option-label` | `<label>` | Option radio + text |
| `.poll-results-table` | `<div>` | Results bar graph table |
| `.poll-results-header` | `<div>` | Table column headers |
| `.poll-results-row` | `<div>` | Single result row |
| `.poll-results-option` | `<span>` | Option text cell |
| `.poll-results-bar` | `<span>` | Bar + percentage cell |
| `.poll-results-hits` | `<span>` | Vote count cell |
| `.poll-bar-track` | `<div>` | Bar background track |
| `.poll-bar-fill` | `<div>` | Filled portion of bar |
| `.poll-bar-label` | `<span>` | Percentage text |
| `.poll-sidebar-card` | `<div>` | Sidebar card |
| `.poll-sidebar-heading` | `<h3>` | Sidebar card heading |
| `.poll-switcher` | `<ul>` | Poll selection list |
| `.poll-switcher-link` | `<a>` | Poll link |
| `.poll-switcher-active` | modifier | Currently selected poll |
| `.poll-featured` | `<div>` | Featured poll container |
| `.poll-featured-title` | `<h2>` | Featured poll heading |
| `.poll-featured-actions` | `<div>` | Vote + results buttons |
| `.poll-status` | `<span>` | Open/closed badge |
| `.poll-vote-count` | `<span>` | Total votes label |

---

## Media Manager

File browser embedded in edit forms via iframe.

| Class | Element | Purpose |
|-------|---------|---------|
| `.file-row` | `<li>` | Clickable file list item |

---

## Admin

Admin-specific classes used by `hzadmin` Blade views. Defined in
`core/templates/hzadmin/css/admin.src.css`.

### Color Token Utilities

Semantic color utilities that auto-derive from any daisyUI 5 theme. Use these
instead of opacity modifiers like `text-base-content/70`. See
[Tailwind Theming — Semantic Color Tokens](../guides/tailwind-theming.md#semantic-color-tokens-admin)
for full derivation details.

| Utility | Purpose |
|---------|---------|
| `text-muted-foreground` | Labels, table headers, form labels |
| `text-subtle-foreground` | Hints, timestamps, descriptions |
| `text-faint-foreground` | Placeholders, disabled text, tree indent marks |
| `text-success-fg` | Success-colored text on base backgrounds |
| `text-error-fg` | Error-colored text on base backgrounds |
| `text-warning-fg` | Warning-colored text on base backgrounds |
| `text-info-fg` | Info-colored text on base backgrounds |
| `text-primary-on-base` | Primary-colored text on base backgrounds |
| `bg-muted` | De-emphasized background areas |
| `bg-accent-surface` | Hover/highlight background |
| `bg-accent-primary` | Primary-tinted hover background |
| `border-default` | Default divider/card borders |
| `border-strong` | Emphasis/hover borders |

> **Do not use** `text-base-content/70` or other opacity modifiers in admin
> views. They produce background-dependent contrast. The token utilities mix
> with the target background for deterministic WCAG AA compliance.

### Layout & Structure

| Class | Element | Purpose |
|-------|---------|---------|
| `.admin-table` | `<table>` | Sortable data table |
| `.admin-fieldset` | `<div>` | Card grouping related fields |
| `.admin-fieldset-heading` | `<h3>` | Section title bar |
| `.admin-fieldset-body` | `<div>` | Padded field area |
| `.admin-field` | `<div>` | Label + input spacing |
| `.admin-meta` | `<table>` | Key-value metadata table |
| `.admin-filter-bar` | `<div>` | Search + filter controls container |
| `.admin-pagination` | `<div>` | Centered pagination wrapper |
| `.admin-title` | `<div>` | Page title module wrapper |
| `.admin-toolbar` | `<div>` | Toolbar buttons wrapper |
| `.admin-submenu` | `<div>` | Submenu tabs wrapper |
| `.column-check` | `<th>/<td>` | Checkbox column (fixed width) |
| `.column-status` | `<th>/<td>` | Status column (fixed width) |

### Batch Processing (com_categories)

| Class | Element | Purpose |
|-------|---------|---------|
| `.batch-details` | `<details>` | Collapsible batch operations wrapper |
| `.batch-summary` | `<summary>` | Click target with chevron indicator |
| `.batch-chevron` | `<svg>` | Rotates 180° on open |
| `.batch-content` | `<div>` | Batch form fields container |
| `.batch-tip` | `<p>` | Explanatory text with muted color |
| `.batch-actions` | `<div>` | Right-aligned Process / Cancel buttons |
| `.batch-grid` | `<div>` | Two-column responsive grid for batch fields |
| `.batch-field` | `<div>` | Label + select/radio wrapper |
| `.batch-radios` | `<div>` | Horizontal radio group with gap |

### Ordering UI

| Class | Element | Purpose |
|-------|---------|---------|
| `.order-group` | `<div>` | Wraps up/down buttons + value input |
| `.order-btn` | `<button>` | Ordering up/down arrow button |
| `.order-num` | `<input>` | Inline ordering value input |
| `.order-group-header` | `<th>` | Column header for ordering group |
| `.order-group-label` | `<span>` | "Order" text label (hidden on narrow) |

### Permissions UI

Rendered by `Hubzero\Form\Fields\Rules::getInputBlade()` when the render
engine is `blade`. Uses `<details>/<summary>` (no JS needed).

| Class | Element | Purpose |
|-------|---------|---------|
| `.config-permissions` | `<div>` | Outer wrapper for all user groups |
| `.config-perm-group` | `<details>` | Collapsible row for one user group |
| `.config-perm-summary` | `<summary>` | Clickable group header with chevron |
| `.config-perm-indent` | `<span>` | Hierarchy dash indent (repeated per level) |
| `.config-perm-body` | `<div>` | Panel that opens/closes |
| `.config-perm-table` | `<table>` | Action × setting × calculated table |
| `.config-perm-select` | `<select>` | Inherited/Allowed/Denied dropdown |
| `.config-perm-badge` | `<span>` | Calculated setting pill |
| `.config-perm-badge-success` | modifier | Green — allowed |
| `.config-perm-badge-error` | modifier | Red — denied |
| `.config-perm-badge-neutral` | modifier | Gray — not set |
| `.config-perm-conflict` | `<span>` | Conflict warning text |
| `.config-perm-notes` | `<p>` | Explanatory notes below the accordion |

### Toolbar

| Class | Element | Purpose |
|-------|---------|---------|
| `.admin-topbar` | `<header>` | Sticky top bar container |
| `.admin-topbar-start` | `<div>` | Hamburger + title |
| `.admin-topbar-end` | `<div>` | Toolbar buttons + user menu |
| `.admin-tb-group` | `<div>` | Button group container |
| `.admin-tb-btn` | `<a>` | Toolbar button (icon-only, tooltip on hover) |
| `.admin-tb-icon` | `<svg>` | Button icon |
| `.admin-tb-sep` | `<div>` | Vertical separator between groups |
| `.admin-tb-outline-primary` | modifier | Primary outline (save, new) |
| `.admin-tb-outline-success` | modifier | Success outline (publish) |
| `.admin-tb-outline-warning` | modifier | Warning outline (unpublish) |
| `.admin-tb-outline-error` | modifier | Error outline (delete, trash) |
| `.admin-tb-neutral` | modifier | Subtle border (edit) |
| `.admin-tb-ghost` | modifier | Text-only (cancel, help, options) |

### Popup / Picker Modal

Used by `admin.js` for toolbar popups and form field picker overlays.

| Class | Element | Purpose |
|-------|---------|---------|
| `.admin-popup-backdrop` | `<div>` | Fixed full-screen overlay (fades in) |
| `.admin-popup-open` | modifier on backdrop | Triggers fade-in and dialog scale animation |
| `.admin-popup-dialog` | `<div>` | Centered modal card (flex column) |
| `.admin-popup-header` | `<div>` | Teal header bar (toolbar popups with chrome) |
| `.admin-popup-title` | `<span>` | Title text in the header |
| `.admin-popup-close` | `<button>` | × close button in the header |
| `.admin-popup-iframe` | `<iframe>` | Fills remaining height in the dialog |

### Picker iframe (tmpl=component views with their own header)

Used when a `data-picker-no-header="true"` picker view supplies its own
header row instead of relying on the chrome header above.

| Class | Element | Purpose |
|-------|---------|---------|
| `.admin-picker-header` | `<div>` | Sticky teal header bar inside the iframe |
| `.admin-picker-search` | `<input>` | Search input inside the picker header |
| `.admin-picker-close` | `<button>` | × close button (primary-content color) |
| `.admin-picker-card` | `<details>` | Accordion card with primary left border |
| `.admin-picker-grid` | `<div>` | 3-column responsive grid container |

### CSP-Safe Data Attributes

These attributes replace inline `onclick`/`onchange` handlers. All are
handled by event delegation in `admin.js`.

| Attribute | Element | Behavior |
|-----------|---------|----------|
| `data-check-all` | `<input type="checkbox">` | Toggle all `data-check-item` checkboxes |
| `data-check-item` | `<input type="checkbox">` | Update `boxchecked` hidden field count |
| `data-submit-on-change` | `<select>` | Submit parent form on value change |
| `data-clear-search="id"` | `<button>` | Clear input by ID, then submit form |
| `data-order-btn` | `<button>` | Check row (`data-cb`) + submit task (`data-task`) |
| `data-list-item-task` | `<a>` | State toggle: check row + submit task |
| `data-tab` | `<a>` | Tab switching (com_config modal) |

---

## Adding New Classes

When building a new component view:

1. Check [global blade components](global-components.md) first — use a
   component if one exists for your pattern
2. Check this list — reuse existing semantic classes where possible
3. Use daisyUI classes for standard components (buttons, inputs, cards, etc.)
4. If you need a new semantic class, add it here and define the CSS in
   `app/templates/hubzero/css/site.src.css`
5. Name classes by structure/purpose, not appearance
   (`entry-meta` not `gray-text-row`)
6. Rebuild CSS: `cd app/templates/hubzero && npm run build:css`
