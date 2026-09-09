# Accessibility — WCAG 2.2 AA Compliance

Hubzero targets [WCAG 2.2 Level AA](https://www.w3.org/TR/WCAG22/) conformance.
This document defines the accessibility requirements that all page types and
components must follow.

**Why AA?** Section 508 (US federal), the European Accessibility Act (EAA, June
2025), and ADA case law all require Level AA. Universities receiving federal
funding must comply.

## Page Structure

### Skip Navigation

Every page must have a skip link as the first focusable element:

```html
<body>
  <a href="#main-content" class="skip-link">
    Skip to main content
  </a>

  <header><!-- navbar --></header>
  <nav aria-label="Breadcrumb"><!-- breadcrumbs --></nav>

  <main id="main-content" tabindex="-1">
    <!-- page content (all page types render here) -->
  </main>

  <footer><!-- site footer --></footer>
</body>
```

### Landmark Regions

Use semantic HTML elements that carry implicit ARIA landmark roles:

| Element | Implicit Role | Usage |
|---------|---------------|-------|
| `<header>` (top-level) | `banner` | Site header/navbar — one per page |
| `<nav>` | `navigation` | Navigation blocks — label each with `aria-label` |
| `<main>` | `main` | Primary page content — one per page |
| `<aside>` | `complementary` | Sidebar content |
| `<footer>` (top-level) | `contentinfo` | Site footer — one per page |
| `<section>` | `region` (if labelled) | Use `aria-label` or `aria-labelledby` |
| `<form>` | `form` (if labelled) | Use `aria-label` or `aria-labelledby` |

```html
<header class="navbar bg-base-100"><!-- site header --></header>

<nav aria-label="Breadcrumb" class="breadcrumbs text-sm">
  <ul>
    <li><a href="/">Home</a></li>
    <li><a href="/blog">Blog</a></li>
    <li>Current Page</li>
  </ul>
</nav>

<nav aria-label="Main menu" class="menu"><!-- primary nav --></nav>

<main id="main-content" tabindex="-1">
  <!-- page type content -->
</main>

<footer class="footer bg-base-200"><!-- site footer --></footer>
```

Multiple `<nav>` elements are allowed but each **must** have a unique
`aria-label` (e.g., "Main menu", "Breadcrumb", "Content filters",
"Page navigation").

## Heading Hierarchy

Every page must have exactly one `<h1>` (the page title). The site name in
the navbar is **not** an `<h1>`. Component content starts at `<h2>`. Never
skip heading levels.

```
h1  — Page title (inside .page-header)
├── h2  — Section headings, sidebar widget headings
│   └── h3  — Individual item titles
```

## Keyboard Navigation

### Focus Visibility (WCAG 2.4.7, 2.4.11, 2.4.13)

All interactive elements must have a visible focus indicator. daisyUI provides
default focus styles. Ensure they are not overridden:

```css
/* Global focus ring — do not remove */
:focus-visible {
  outline: 2px solid oklch(var(--p));  /* daisyUI primary color */
  outline-offset: 2px;
}

/* Ensure focus is never hidden behind sticky headers or overlays */
/* WCAG 2.4.11: Focus Not Obscured */
[tabindex]:focus-visible {
  scroll-margin-top: 5rem;
}
```

### Tab Order

- Tab order must follow visual reading order (left-to-right, top-to-bottom)
- Never use `tabindex` values greater than 0
- Use `tabindex="-1"` only for programmatic focus targets (e.g., `<main>`)
- Use `tabindex="0"` only to make non-interactive elements focusable when needed

### Interactive Target Size (WCAG 2.5.8)

All clickable/tappable targets must be at least **24x24 CSS pixels**:

```css
/* Ensure minimum target size */
.btn, .tab, .menu a, .link,
input[type="checkbox"], input[type="radio"] {
  min-height: 24px;
  min-width: 24px;
}
```

daisyUI's default button/input sizes already meet this. Watch for small icon-only
buttons — always use at least `.btn-sm` (never smaller).

## Color and Contrast

### Contrast Ratios (WCAG 1.4.3, 1.4.6)

| Element | Minimum Ratio (AA) |
|---------|---------------------|
| Normal text (< 18px) | 4.5:1 |
| Large text (≥ 18px bold or ≥ 24px) | 3:1 |
| UI components and graphical objects | 3:1 |
| Disabled elements | Exempt |

daisyUI's built-in themes are contrast-tested. When creating custom themes,
verify all color pairs.

> **Admin views**: Use the semantic color token utilities (`text-muted-foreground`,
> `text-subtle-foreground`, `text-faint-foreground`) instead of opacity modifiers
> like `text-base-content/70`. The tokens mix with `base-100` using `color-mix()`
> in oklch, producing solid colors with deterministic WCAG AA contrast on any
> daisyUI 5 theme. See [Tailwind Theming — Semantic Color Tokens](tailwind-theming.md#semantic-color-tokens-admin).

Custom theme color pairs to verify:

```js
// tailwind.config.js — Hubzero theme
{
  hubzero: {
    'primary':         '#0074c7',   // ✓ 4.6:1 on white
    'primary-content': '#ffffff',
    'base-100':        '#ffffff',
    'base-content':    '#1f2937',   // ✓ 14.7:1 on white
    // ...
  }
}
```

Tools: [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/),
[Colour Contrast Analyser](https://www.tpgi.com/color-contrast-checker/).

### Color Not Sole Indicator (WCAG 1.4.1)

Never rely on color alone to convey information. Always pair color with text,
icons, or patterns:

```html
<!-- Bad: color is the only indicator -->
<span class="text-error">●</span>

<!-- Good: text + color -->
<span class="badge badge-error">Error</span>

<!-- Good: icon + color + text -->
<span class="badge badge-success">
  <svg aria-hidden="true"><!-- checkmark --></svg>
  Published
</span>
```

## Forms

### Labels (WCAG 1.3.1, 3.3.2)

Every form input must have a visible `<label>` associated via `for`/`id`.
Use the `<x-form-field>` global component (preferred) or the equivalent
semantic markup:

```blade
<x-form-field name="field-title" label="Title" required
              hint="A descriptive title for your entry."
              error="{{ $errors->first('title') }}">
    <input type="text" id="field-title" name="title"
           class="input input-bordered w-full" required
           aria-describedby="field-title-hint field-title-error"
           aria-invalid="false" />
</x-form-field>
```

The component renders a `.form-field` wrapper with a `<label>` linked via
`for`/`id`, a hint via `aria-describedby`, and an error message with
`role="alert"`. See [Global Components](../reference/global-components.md#x-form-field).

### Error Handling (WCAG 3.3.1, 3.3.3)

- Show errors inline next to the field (not just at the top of the form)
- Use `aria-invalid="true"` on the invalid field
- Use `aria-describedby` to link the error message to the field
- Use `role="alert"` on the error container so screen readers announce it
- Describe how to fix the error, not just what went wrong

```js
// On validation failure:
field.setAttribute('aria-invalid', 'true');
field.classList.add('input-error');
errorEl.classList.remove('hidden');
errorEl.setAttribute('role', 'alert');
```

### Required Fields (WCAG 3.3.2)

Mark required fields with both:
- `required` HTML attribute (browser + screen reader support)
- Visible `*` marker with `aria-label="required"`

At the top of the form, explain the convention:

```html
<p class="text-sm text-base-content/60 mb-4">
  Fields marked with <span class="text-error" aria-hidden="true">*</span>
  are required.
</p>
```

### Autocomplete (WCAG 1.3.5)

Use `autocomplete` attributes on fields that collect personal data:

```html
<input type="text" name="name" autocomplete="name" />
<input type="email" name="email" autocomplete="email" />
<input type="tel" name="phone" autocomplete="tel" />
```

## Images and Media

### Alt Text (WCAG 1.1.1)

| Image Type | Alt Text |
|------------|----------|
| Informative | Describe the content: `alt="Chart showing 40% increase"` |
| Decorative | Empty alt: `alt=""` |
| Functional (link/button) | Describe the action: `alt="Download PDF"` |
| Complex (chart/graph) | Brief alt + link to long description |
| Avatar next to name | Empty alt: `alt=""` (name is in adjacent text) |

```html
<!-- Informative -->
<img src="chart.png" alt="Bar chart showing publication counts by year" />

<!-- Decorative -->
<img src="divider.png" alt="" />

<!-- Avatar (name visible) -->
<div class="avatar">
  <img src="photo.jpg" alt="" />
</div>
<span>John Doe</span>
```

### Video and Audio (WCAG 1.2.1–1.2.5)

- Provide captions for all video content
- Provide transcripts for audio-only content
- Provide audio descriptions for video when visual content isn't described by the audio track

## Dynamic Content

### Live Regions (WCAG 4.1.3)

Announce dynamic content changes to screen readers:

```html
<!-- Polite: doesn't interrupt (search results, counters) -->
<p aria-live="polite">Showing 1–10 of 42 entries</p>

<!-- Assertive: interrupts (errors, critical alerts) -->
<div class="alert alert-error" role="alert">
  Your session has expired.
</div>

<!-- Status: for status messages (save confirmations) -->
<div role="status" aria-live="polite">
  Entry saved successfully.
</div>
```

### Loading States

Use `aria-busy` and visible indicators:

```html
<div aria-busy="true" aria-label="Loading entries">
  <span class="loading loading-spinner loading-lg"></span>
  <span class="sr-only">Loading...</span>
</div>
```

After loading completes:

```html
<div aria-busy="false">
  <ul class="list" aria-label="Blog entries"><!-- loaded content --></ul>
</div>
```

## Motion and Animation

### Reduced Motion (WCAG 2.3.3)

Respect `prefers-reduced-motion` for all animations:

```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

Tailwind provides `motion-safe:` and `motion-reduce:` variants:

```html
<div class="motion-safe:animate-fade-in motion-reduce:opacity-100">
  Content
</div>
```

## Dark Mode

### User Preference

Support both system preference and manual toggle:

1. Default to `prefers-color-scheme` media query
2. Allow manual override via toggle
3. Persist choice in `localStorage`
4. Ensure all contrast ratios remain valid in both themes

See [Tailwind + daisyUI Theming Guide](tailwind-theming.md) for implementation.

### Contrast in Dark Mode

Dark mode has its own contrast challenges. Verify:
- Text on dark backgrounds meets 4.5:1
- Borders and UI elements on dark backgrounds meet 3:1
- Focus indicators are visible on dark backgrounds
- Status colors (error, warning, success) remain distinguishable

## Tables

### Data Tables (WCAG 1.3.1)

```html
<div class="overflow-x-auto">
  <table class="table" role="grid">
    <caption class="sr-only">Team members and their roles</caption>
    <thead>
      <tr>
        <th scope="col">Member</th>
        <th scope="col">Role</th>
        <th scope="col"><span class="sr-only">Actions</span></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>John Doe</td>
        <td>Manager</td>
        <td><button class="btn btn-sm" aria-label="Remove John Doe">Remove</button></td>
      </tr>
    </tbody>
  </table>
</div>
```

- Use `<th scope="col">` for column headers, `<th scope="row">` for row headers
- Add `<caption>` (visible or `.sr-only`) describing the table's purpose
- Action columns with icon-only buttons need `<span class="sr-only">` headers

### Sortable Tables

```html
<th scope="col">
  <a href="?sort=name&dir=asc" aria-sort="none">
    Name
    <span aria-hidden="true">↕</span>
  </a>
</th>

<!-- When sorted ascending -->
<th scope="col">
  <a href="?sort=name&dir=desc" aria-sort="ascending">
    Name
    <span aria-hidden="true">↑</span>
  </a>
</th>
```

## Pagination

```html
<nav aria-label="Page navigation" class="flex justify-center mt-8">
  <div class="join">
    <a class="join-item btn btn-sm" href="..." aria-label="Previous page">&laquo;</a>
    <a class="join-item btn btn-sm" href="...">1</a>
    <a class="join-item btn btn-sm btn-active" aria-current="page">2</a>
    <a class="join-item btn btn-sm" href="...">3</a>
    <a class="join-item btn btn-sm" href="..." aria-label="Next page">&raquo;</a>
  </div>
</nav>
```

- Wrap in `<nav>` with `aria-label="Page navigation"`
- Mark the current page with `aria-current="page"`
- Use `aria-label` for previous/next (don't rely on `«` and `»` characters)

## Modals and Dialogs

```html
<dialog class="modal" id="confirm-modal" aria-labelledby="modal-title"
        aria-describedby="modal-desc">
  <div class="modal-box">
    <h3 class="text-lg font-bold" id="modal-title">Confirm Action</h3>
    <p id="modal-desc">Are you sure you want to proceed?</p>
    <div class="modal-action">
      <button class="btn btn-primary" autofocus>Confirm</button>
      <form method="dialog">
        <button class="btn btn-ghost">Cancel</button>
      </form>
    </div>
  </div>
  <form method="dialog" class="modal-backdrop">
    <button aria-label="Close">close</button>
  </form>
</dialog>
```

- Use native `<dialog>` element (handles focus trapping automatically)
- `aria-labelledby` and `aria-describedby` link to title and description
- First focusable element should be the primary action (`autofocus`)
- Escape key closes the dialog (native `<dialog>` behavior)
- Background click closes via `modal-backdrop` form

## WCAG 2.2 Checklist Summary

### Level A (must have)

| Criterion | Requirement | Implementation |
|-----------|-------------|----------------|
| 1.1.1 | Non-text content has alt text | `alt` on all `<img>` |
| 1.3.1 | Info and relationships conveyed by structure | Semantic HTML, landmarks, headings |
| 1.3.5 | Input purpose identified | `autocomplete` attributes |
| 1.4.1 | Color not sole means of conveying info | Text + color for status |
| 2.1.1 | All functionality via keyboard | Tab, Enter, Space, Escape |
| 2.4.1 | Skip navigation mechanism | Skip link to `#main-content` |
| 2.4.2 | Pages have descriptive titles | `<title>` element |
| 2.4.4 | Link purpose clear from text | Descriptive link text |
| 2.5.8 | Target size ≥ 24×24px | `.btn-sm` minimum |
| 3.1.1 | Page language declared | `<html lang="en">` |
| 3.3.1 | Errors identified in text | Inline error messages |
| 3.3.2 | Labels and instructions | `<label>` on all inputs |
| 4.1.2 | Name, role, value for controls | ARIA attributes where needed |

### Level AA (required by law)

| Criterion | Requirement | Implementation |
|-----------|-------------|----------------|
| 1.4.3 | Contrast ≥ 4.5:1 (normal text) | daisyUI theme colors |
| 1.4.4 | Text resizable to 200% | Relative units, no overflow |
| 1.4.11 | Non-text contrast ≥ 3:1 | UI component borders/icons |
| 2.4.7 | Focus visible | `:focus-visible` outline |
| 2.4.11 | Focus not obscured | `scroll-margin-top` |
| 3.3.3 | Error suggestions | Describe how to fix errors |
| 3.3.4 | Error prevention (legal/financial) | Confirmation dialogs |
| 4.1.3 | Status messages | `aria-live`, `role="status"` |

## Content Security Policy (CSP)

CSP remains the target standard for all Blade work. Admin Blade views already
follow it consistently; site Blade views should be written the same way, even
though the current site shell and a few early site templates still have
remaining cleanup before strict site-wide CSP can be enforced.

CSP prevents XSS attacks by blocking inline scripts and styles.

### Required CSP compatibility

| CSP Directive | Policy | What it blocks |
|---------------|--------|----------------|
| `script-src` | No `'unsafe-inline'` | `onclick`, `onchange`, `<script>` blocks |
| `style-src` | No `'unsafe-inline'` | `style="..."`, `<style>` blocks |

### Allowed

- External `.js` files loaded via `<script src="...">`
- External `.css` files loaded via `<link rel="stylesheet">`
- `<script type="application/json">` (data containers, not executable)
- Tailwind utility classes and custom CSS classes

### How to comply

1. **No inline event handlers** — Use `data-*` attributes with event
   delegation in the template's JS file (e.g., `data-submit-on-change`
   instead of `onchange="this.form.submit()"`)
2. **No inline styles** — Use Tailwind classes, `@class` directives, or
   custom CSS classes
3. **No `<script>` blocks** — Move logic to external `.js` files loaded
   via `$__view->js()` or the template's main JS bundle
4. **No `<style>` blocks** — Move CSS to the template's `.src.css` file

See [Admin Views — CSP Compliance](admin.md#csp-content-security-policy-compliance)
for the complete data-attribute reference.

## Testing

### Automated

- [axe DevTools](https://www.deque.com/axe/) — browser extension, CI integration
- [Lighthouse](https://developer.chrome.com/docs/lighthouse/) — built into Chrome DevTools
- [pa11y](https://pa11y.org/) — CLI for CI pipelines

### Manual

- **Keyboard only**: Navigate the entire page with Tab, Shift+Tab, Enter, Space, Escape
- **Screen reader**: Test with NVDA (Windows), VoiceOver (macOS), or Orca (Linux)
- **Zoom**: Verify layout at 200% and 400% zoom
- **Color**: Check with simulated color blindness (Chrome DevTools > Rendering)
- **Motion**: Enable "Reduce motion" in OS settings and verify

### CI Integration

```bash
# Run axe on a URL
npx @axe-core/cli https://localhost:9443 --tags wcag2aa
```

```yaml
# GitHub Actions
- name: Accessibility check
  run: npx @axe-core/cli ${{ env.APP_URL }} --tags wcag2aa --exit
```
