# Admin Views

Admin list and edit views for the Hubzero CMS backend. Uses daisyUI + Tailwind
inside the `hzadmin` template with admin-specific global Blade components.

## When to Use

- Admin list/display views (sortable tables, filters, bulk actions)
- Admin edit/create forms (two-column layouts with sidebar metadata)

## Dual-Shell Backward Compatibility

The `hzadmin` template ships with **two page shells**:

| File | Used when | Look |
|------|-----------|------|
| `index.php` | Component view has only legacy `.php` | Kameleon (copy) |
| `index.blade.php` | Component view has `.blade.php` | daisyUI |

The render engine auto-selects per request. Legacy views fall back to
Kameleon with zero breakage. As views are converted to Blade, they
automatically get the new daisyUI shell.

## Login Page Shell (login.blade.php)

`tmpl=login` requests use a **separate, minimal shell** — no sidebar,
no toolbar, just the centered login card on a dark backdrop.

| File | Purpose |
|------|---------|
| `core/templates/hzadmin/login.blade.php` | Full-page login shell |
| `core/modules/mod_adminlogin/tmpl/default.blade.php` | Login card module |
| `core/components/com_login/admin/views/login/tmpl/default.blade.php` | Component entry point (renders module) |

### Layout

```
html.login-page (dark --color-neutral background)
└── body.login-page (centered flex column, padding-top: 15vh)
    ├── .login-wrap (max-width: 20rem — optional flash alerts)
    └── main#main-content.login-wrap
        └── {!! $content !!}  ← rendered mod_adminlogin output
```

### CSS Classes (in admin.src.css)

| Class | Purpose |
|-------|---------|
| `.login-page` | Full-height flex column, dark background, upper-third positioning |
| `.login-wrap` | 20rem max-width constraint |
| `.login-card-header` | Primary-colored card header with rounded top corners |
| `.login-inputs` | Flex column with `0.375rem` gap between stacked inputs |

All login styles live in `admin.src.css` under the **Login Page Shell**
section — **no inline `<style>` blocks** in the template (CSP-safe).

### Module template (mod_adminlogin)

The login card supports:
- **OAuth/SSO providers** — iterates `$authenticators`, calls
  `onRenderOption` if available, falls back to a plain `<a>` button
- **Divider** — shown when both OAuth and password login are enabled
- **Password form** — standard username/password with `sr-only` labels,
  visible placeholders, and `autocomplete` attributes

Variables passed by `mod_adminlogin.php` via `renderLayout()`:

| Variable | Type | Description |
|----------|------|-------------|
| `$return` | string | base64-encoded post-login redirect |
| `$freturn` | string | base64-encoded factors redirect |
| `$returnQueryString` | string | `&return=…` appended to OAuth URLs |
| `$authenticators` | array | OAuth plugins: `[name => [name, display]]` |
| `$site_display` | string | Site name for the "Sign in with…" divider |
| `$basic` | bool | True when hubzero password plugin is enabled |

## Template Layout (index.blade.php)

```
drawer (daisyUI)
├── sidebar (always visible on lg:, toggle on mobile)
│   ├── Site name
│   ├── Admin menu (mod_adminmenu)
│   └── Version info
└── content area
    ├── Top bar (hamburger, title, toolbar, user menu)
    ├── Submenu tabs (component sub-navigation)
    ├── Messages (flash/system alerts)
    └── Main content ({!! $content !!})
        ├── [optional] Sub-submenu (nav.sub.sub-navigation)
        ├── Component view HTML
        └── Footer
```

## Sub-submenu (Second-Level Navigation)

Some components group related controllers under a single main submenu tab
and need a second-level nav inside the view to switch between them.

### Pattern

Add a `<nav class="sub sub-navigation">` at the top of the component view,
before `<x-admin-form>` or `<x-admin-edit>`. The CSS breaks it out of the
`<main class="p-6">` padding so it sits flush under the main submenu bar.

```blade
<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a class="{{ $controller === 'manage' ? 'active' : '' }}"
         href="{!! Route::url('index.php?option=' . $option . '&controller=manage') !!}">
        {{ Lang::txt('COM_MYCOMPONENT_SUBMENU_OVERVIEW') }}
      </a>
    </li>
    <li>
      <a class="{{ $controller === 'migrations' ? 'active' : '' }}"
         href="{!! Route::url('index.php?option=' . $option . '&controller=migrations') !!}">
        {{ Lang::txt('COM_MYCOMPONENT_SUBMENU_MIGRATIONS') }}
      </a>
    </li>
  </ul>
</nav>
```

### Active state

Set `class="active"` on the link matching the current controller.
The active pill uses `background: var(--color-primary)` with white text —
visually distinct from the main submenu's underline indicator.

### Grouping in `addSubmenu()`

The main submenu helper (`helpers/installer.php` pattern) marks the parent
tab active for all controllers in the group:

```php
Submenu::addEntry(
    Lang::txt('COM_INSTALLER_SUBMENU_CORE'),
    Route::url('index.php?option=com_installer&controller=manage'),
    ($vName == 'manage' || $vName == 'migrations')
);
```

### URL encoding

Use `{!! Route::url(...) !!}` (not `{{ }}`). The `{{ }}` syntax
escapes `&` to `&amp;` in href attributes.

### Existing example

`com_installer` uses this pattern:
- Main tab "Hubzero Core" → sub-tabs: **Core Extensions** | Core Migrations
- Main tab "Packages" → sub-tabs: **Packages** | Repositories

---

## Admin Global Components

### `<x-admin-toolbar>` — Declarative toolbar

Replaces the PHP `Toolbar::*` block at the top of every admin view.
Emits no HTML — calls Toolbar facade methods.

```blade
<x-admin-toolbar
    title="Knowledge Base: Articles"
    icon="kb"
    :canDo="$canDo"
    option="{{ $option }}"
/>
```

For edit views, add `:edit="true"` to get save/apply/cancel buttons
instead of list-mode buttons.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `''` | Page title |
| `icon` | string | `''` | Toolbar icon |
| `canDo` | object | `null` | Permissions from `Permissions::getActions()` |
| `option` | string | `''` | Component option (for preferences link) |
| `edit` | bool | `false` | Edit mode (save/apply/cancel) |

### `<x-admin-form>` — List view wrapper

Wraps the table, filter bar, and hidden fields.

```blade
<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <input type="text" name="search" class="input input-bordered input-sm w-64" />
        <button type="submit" class="btn btn-sm btn-primary">Go</button>
      @endslot
      <select name="category" class="select select-bordered select-sm"
              data-submit-on-change>...</select>
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>...</thead>
      <tbody>...</tbody>
    </table>
  </div>
</x-admin-form>
```

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `option` | string | `''` | Component option |
| `controller` | string | `''` | Controller name |
| `sort` | string | `''` | Current sort column |
| `sortDir` | string | `'asc'` | Sort direction |

### `<x-admin-edit>` — Edit form wrapper

Two-column grid with form validation and hidden fields.

```blade
<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Left column: main fieldsets --}}
  <div class="admin-fieldset">
    <h3 class="admin-fieldset-heading">Details</h3>
    <div class="admin-fieldset-body space-y-4">
      <div class="admin-field">
        <label class="label">Title <span class="text-error">*</span></label>
        <input type="text" class="input input-bordered w-full" required />
      </div>
    </div>
  </div>

  @slot('sidebar')
    {{-- Right column: metadata + publishing --}}
    <div class="admin-fieldset">
      <h3 class="admin-fieldset-heading">Details</h3>
      <div class="admin-fieldset-body">
        <table class="admin-meta">
          <tr><td>ID</td><td>42</td></tr>
        </table>
      </div>
    </div>
  @endslot
</x-admin-edit>
```

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `option` | string | `''` | Component option |
| `controller` | string | `''` | Controller name |

### `<x-admin-filters>` — Filter bar layout

Search controls on the left, filter dropdowns on the right.

| Slot | Description |
|------|-------------|
| `search` | Search input + go/clear buttons |
| default | Filter dropdowns |

### Common Pitfalls

#### Slot syntax

Admin components use `@slot('name')...@endslot` syntax, **not**
`<x-slot:name>`. The latter causes silent failures or variable
shadowing when the slot name matches a PHP variable in scope:

```blade
{{-- WRONG — <x-slot:search> may not work correctly --}}
<x-admin-filters>
  <x-slot:search>
    <input type="text" ... />
  </x-slot:search>
</x-admin-filters>

{{-- CORRECT --}}
<x-admin-filters>
  @slot('search')
    <input type="text" ... />
  @endslot
</x-admin-filters>
```

The `filters` slot on `<x-admin-form>` is especially sensitive — if
omitted, a controller variable named `$filters` (very common) leaks
through and the component calls `$filters->isNotEmpty()` on the array,
causing "Method [blank] does not exist."

#### Undeclared props

Only pass props declared in the component's `@props()` block.
`<x-admin-toolbar>` accepts: `title`, `icon`, `canDo`, `option`, `edit`.
Passing undeclared props like `:add` or `:delete` causes errors.
The toolbar already reads permissions from `$canDo` internally — just
pass `:canDo="$canDo"` and the component handles add/edit/delete buttons.

#### Hidden fields from `<x-admin-form>`

The component already outputs these hidden fields — do **not** duplicate
them in the view:

- `option`, `controller`, `task`, `boxchecked`
- `filter_order`, `filter_order_Dir` (from `sort`/`sortDir` props)
- CSRF token (`Html::input('token')`)

Only add view-specific hidden fields (e.g., `pageid`, `page_id`).

## Structure Reference

### List View

| Element | Class/Component | Purpose |
|---------|----------------|---------|
| Toolbar | `<x-admin-toolbar>` | Page title + action buttons |
| Form wrapper | `<x-admin-form>` | Form, filters, hidden fields |
| Filter bar | `<x-admin-filters>` | Search + dropdowns |
| Table wrapper | `.bg-base-100 .rounded-box .border` | Card container |
| Table | `.admin-table` | Sortable data table |
| Column headers | `Html::grid('sort', ...)` | Sortable headers |
| Checkbox | `.checkbox .checkbox-sm` | Row selection |
| Status badge | `.badge .badge-success/warning/ghost` | State indicator |
| Pagination | `.admin-pagination` | Page nav container |

### Edit View

| Element | Class/Component | Purpose |
|---------|----------------|---------|
| Toolbar | `<x-admin-toolbar :edit="true">` | Save/apply/cancel |
| Form wrapper | `<x-admin-edit>` | Two-column grid + hidden fields |
| Section card | `.admin-fieldset` | Grouped fields |
| Section heading | `.admin-fieldset-heading` | Section title bar |
| Section body | `.admin-fieldset-body` | Padded field area |
| Field wrapper | `.admin-field` | Label + input spacing |
| Meta table | `.admin-meta` | Key-value metadata |
| Text input | `.input .input-bordered .w-full` | daisyUI input |
| Select | `.select .select-bordered .w-full` | daisyUI select |
| Textarea | `.textarea .textarea-bordered .w-full` | daisyUI textarea |

## Toolbar Usage

Toolbars are still set up via PHP `Toolbar::*` facade calls. The
`<x-admin-toolbar>` component wraps these calls declaratively, but
you can also call them manually for custom buttons:

```blade
<x-admin-toolbar title="My Page" icon="myicon" :canDo="$canDo">
  @php
    // Custom toolbar buttons beyond the defaults
    Toolbar::custom('export', 'export', '', 'Export', false);
  @endphp
</x-admin-toolbar>
```

## Permissions

Admin views gate actions with the permissions object:

```blade
@php
  $canDo = \Components\MyComponent\Admin\Helpers\Permissions::getActions('entry');
@endphp

@if($canDo->get('core.edit'))
  <a href="...">Edit</a>
@endif
```

Common permission keys: `core.admin`, `core.create`, `core.edit`,
`core.edit.state`, `core.delete`.

### ACL Rules UI

Components with `access.xml` can include an ACL rules editor in edit
views via `$form->getInput('rules')`. The `Rules` form field
(`Hubzero\Form\Fields\Rules`) is render-engine-aware:

- **Blade shell**: Outputs `<details>/<summary>` accordion with
  `config-perm-*` CSS classes (no JavaScript required). Uses daisyUI-style
  badges for calculated permissions.
- **Legacy shell**: Outputs jQuery UI accordion with `pane-sliders` /
  `group-rules` classes.

In Blade views, omit `$form->getLabel('rules')` — the `admin-fieldset-heading`
already provides the "Permissions" label. Only call `$form->getInput('rules')`:

```blade
@if($canDo->get('core.admin') && isset($form))
  <div class="admin-fieldset">
    <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_FORUM_FIELDSET_RULES') }}</h3>
    <div class="admin-fieldset-body">
      {!! $form->getInput('rules') !!}
    </div>
  </div>
@endif
```

The `config-perm-*` CSS classes are defined in `admin.src.css`. See
[Semantic Classes — Admin](../reference/semantic-classes.md#permissions-ui) for the
full class list.

## CSP (Content Security Policy) Compliance

All admin Blade views must be CSP-safe — no inline JavaScript or inline
CSS. The security officer enforces `script-src` and `style-src` without
`'unsafe-inline'`, so the following are **prohibited**:

| Prohibited | Replacement |
|------------|-------------|
| `onclick="..."` | `data-*` attribute + event delegation in `admin.js` |
| `onchange="..."` | `data-submit-on-change` attribute |
| `<script>` blocks | External `.js` file loaded via `$__view->js()` |
| `<style>` blocks | Classes in `admin.src.css` |
| `style="..."` attributes | Tailwind utility class or custom class |

The one exception is `<script type="application/json">`, which is a data
container (not executable) and is allowed by CSP.

### Data attributes for event delegation

Instead of inline handlers, Blade views use `data-*` attributes. The
`admin.js` file handles all of these via `document.addEventListener()`:

| Attribute | Element | Behavior |
|-----------|---------|----------|
| `data-check-all` | `<input type="checkbox">` | Toggle all row checkboxes |
| `data-check-item` | `<input type="checkbox">` | Update boxchecked count |
| `data-submit-on-change` | `<select>` | Submit the parent form on change |
| `data-clear-search="id"` | `<button>` | Clear the input with that ID, then submit |
| `data-order-btn` | `<button>` | Order up/down (requires `data-cb`, `data-task`) |
| `data-list-item-task` | `<a>` | State toggle link (requires `data-cb`, `data-task`) |
| `data-tab` | `<a>` | Tab switching (used by com_config modal) |
| `data-picker-url` | `<button>` | Open picker popup iframe (see [Picker Popup Pattern](#picker-popup-pattern)) |
| `data-menutype` | `<a>` | Menu type payload (handled by `menutypes.js`) |
| `data-picker-close` | `<button>` | Close the containing popup iframe (handled by component JS) |

Example — filter dropdown with auto-submit:

```blade
<select name="state"
        class="select select-sm select-bordered"
        data-submit-on-change>
  <option value="-1">All States</option>
  <option value="1">Published</option>
  <option value="0">Unpublished</option>
</select>
```

Example — state toggle badge:

```blade
<a href="#"
   data-list-item-task
   data-cb="cb{{ $i }}"
   data-task="{{ $stateTask }}">
  <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
</a>
```

### Hubzero JS namespace

`admin.js` defines the `Hubzero` namespace as the primary JS API:

| Function | Purpose |
|----------|---------|
| `Hubzero.submitbutton(task)` | Set task on adminForm and submit |
| `Hubzero.submitform(task, form)` | Submit a specific form with a task |
| `Hubzero.checkAll(checkbox)` | Toggle all row checkboxes |
| `Hubzero.isChecked(isChecked)` | Update boxchecked count |
| `Hubzero.tableOrdering(order, dir, task)` | Sort by column |
| `Hubzero.listItemTask(cbId, task)` | Check a row and submit a task |

Internal functions (not on the `Hubzero` namespace) used by specific patterns:

| Function | Purpose |
|----------|---------|
| `openPickerPopup(url, width, height, noHeader)` | Open a picker popup iframe |
| `stubFancyboxClose(closeFn)` | Stub `jQuery.fancybox.close` so iframe pickers close the overlay |

Legacy aliases (`window.Joomla.submitbutton`, etc.) are maintained for
non-Blade PHP views that still reference the old namespace. These will be
removed once all views are converted to Blade.

### Component-specific JS

When a view needs JS beyond the standard delegation handlers, create an
external file in the component's assets directory:

```
core/components/com_config/admin/assets/js/config-modal.js
```

Load it in the Blade view with:

```blade
@php
  $__view->js('config-modal.js');
@endphp
```

This registers the file via the Document service, which the hzadmin shell
renders via `getHeadData()`.

## Picker Popup Pattern

The picker popup pattern opens a `tmpl=component` iframe in a styled overlay
without navigating away from the current page. It replaces the legacy
fancybox `$.fn.fancybox()` modal pattern for Blade admin views.

### Form field button (Blade mode)

Form field classes that render a picker use `self::isBlade()` to branch
between the Blade button pattern and the legacy fancybox pattern:

```php
if (self::isBlade()) {
    Html::behavior('framework');
    $html[] = '<button type="button" class="button"'
        . ' data-picker-url="' . htmlspecialchars($menuUrl, ENT_QUOTES) . '"'
        . ' data-picker-width="1400"'
        . ' data-picker-height="900"'
        . ' data-picker-no-header="true">'
        . Lang::txt('JSELECT') . '</button>';
} else {
    // Legacy fancybox binding via inline script
}
```

`self::isBlade()` is a static helper on `Hubzero\Form\Field` that returns
`true` when the current render engine is `blade`. It is available to all
custom form field classes that extend `Field`.

### Data attributes

| Attribute | Value | Purpose |
|-----------|-------|---------|
| `data-picker-url` | URL string | URL of the `tmpl=component` picker view |
| `data-picker-width` | integer | Popup width in pixels (default: 800) |
| `data-picker-height` | integer | Popup height in pixels (default: 500) |
| `data-picker-no-header` | `"true"` | Skip the chrome header — use when the picker view has its own header with a close button |

`admin.js` delegates all `[data-picker-url]` clicks via `initPickerButtons()`
which calls `openPickerPopup(url, width, height, noHeader)`.

### Legacy picker close compatibility

Legacy picker templates (and Blade picker views) call
`window.parent.$.fancybox.close()` to dismiss the overlay.
`admin.js` temporarily replaces `jQuery.fancybox.close` with a function
that closes the overlay — no changes are needed to the picker templates.

### `tmpl=component` iframe views

When a Blade view is rendered in `tmpl=component` context it gets the
**`component.blade.php`** shell (parallel to the `index.blade.php` main shell).
It loads `admin.css` automatically — no manual stylesheet registration needed.

The shell also renders the full `headData` — external stylesheets
(`headData['styleSheets']`), inline style blocks (`headData['style']`),
external scripts (`headData['scripts']`), and inline script blocks
(`headData['script']`). This means component-specific assets registered
via `$__view->js()`, `$__view->css()`, or `Document::addScript()` are
all output correctly.

**jQuery is NOT loaded automatically** in the component shell (unlike
`index.blade.php`). Iframe views that depend on jQuery must explicitly
load it via `Html::behavior('framework')`:

```blade
@php
  Html::behavior('framework');
  $__view->js('managers.js');
@endphp
```

**Legacy JS compatibility:** When a component has legacy JS files that
call jQuery plugins not available in Blade mode (e.g. `$.datetimepicker`),
create a `*.blade.js` shim file loaded before the legacy JS:

```blade
@php
  $__view->js('courses.blade.js');  // shim — loaded first
  $__view->js();                     // legacy courses.js — loaded second
@endphp
```

The shim provides no-op stubs for missing jQuery plugins and initializes
Blade-native alternatives (e.g. flatpickr). This preserves dual-view
mode: legacy views use the original jQuery plugins, Blade views use the
shim. See `com_courses/admin/assets/js/courses.blade.js` for an example.

To load a component-specific picker JS file in a `tmpl=component` view:

```blade
@php
  App::get('document')->addScript(
      App::get('request')->root() . 'components/com_mycomp/admin/assets/js/picker.js'
  );
@endphp
```

Component picker JS should live in the component's `admin/assets/js/`
directory and follow CSP rules: no inline `<script>` blocks, use
`data-*` attributes for event delegation (see example: `menutypes.js`).

### CSS classes

Picker views that supply their own header use these classes (defined in
`admin.src.css`):

| Class | Element | Purpose |
|-------|---------|---------|
| `.admin-picker-header` | `<div>` | Sticky teal header bar |
| `.admin-picker-search` | `<input>` | Search input inside the header |
| `.admin-picker-close` | `<button>` | Close button (white × icon on teal) |
| `.admin-picker-card` | `<details>` | Accordion card with primary left border |
| `.admin-picker-grid` | `<div>` | 3-column responsive grid for cards |

### Avoiding inline styles

Replace `style="..."` attributes with Tailwind utility classes or custom
CSS classes in `admin.src.css`:

| Instead of | Use |
|------------|-----|
| `style="min-height: 300px;"` | `class="min-h-[300px]"` |
| `style="display:none"` | `class="hidden"` |
| `style="display:{{ $var }}"` | `@class(['hidden' => !$isActive])` |

For conditional visibility driven by PHP, use Blade's `@class` directive:

```blade
<div @class(['admin-fieldset', 'hidden' => !$isActive])>
```

## CSS Build

```bash
cd core/templates/hzadmin
node_modules/.bin/tailwindcss -i css/admin.src.css -o css/admin.css --minify
```

The `@source` directives in `admin.src.css` scan all admin Blade views
across all components, so new classes are picked up automatically.

### Semantic color tokens

`admin.src.css` defines semantic color tokens (`--muted-foreground`,
`--subtle-foreground`, `--faint-foreground`, etc.) that auto-derive from any
daisyUI 5 theme via `color-mix()` in oklch. Use the corresponding Tailwind
utilities (`text-muted-foreground`, `text-subtle-foreground`,
`text-faint-foreground`) instead of opacity modifiers like
`text-base-content/70`. See
[Tailwind Theming — Semantic Color Tokens](tailwind-theming.md#semantic-color-tokens-admin)
for the full token reference.

After modifying `admin.src.css`, also clear compiled Blade views:

```bash
rm -f app/cache/views/*.php
```

### Hardlinked files

`admin.css`, `admin.src.css`, and `admin.js` are hardlinked between
`app/templates/hzadmin/` and `core/templates/hzadmin/`. Edit either copy —
the change is reflected in both. Verify with `ls -li` (same inode).

### Scanning PHP form field classes

Tailwind only scans files listed in `@source` directives. Form field PHP
classes (e.g. `Radio.php`, `Calendar.php`) emit Tailwind utility class names
at runtime — but they are not Blade files, so they need an explicit source
entry:

```css
/* admin.src.css */
@source "../../../../core/libraries/Hubzero/Form/Fields/*.php";
```

This is already present. Add similar entries if you create custom form field
classes outside `Hubzero/Form/Fields/`.

## Date Picker (Flatpickr)

The legacy jQuery UI datepicker has been replaced with
[Flatpickr](https://flatpickr.js.org/) in Blade admin views.

### How it works

`Hubzero\Html\Builder\Input::calendar()` detects Blade mode via
`\Hubzero\Facades\Document::getRenderEngine() === 'blade'` and:

1. Loads `core/assets/js/flatpickr.min.js` and
   `core/assets/css/flatpickr.min.css` (once per page, via static flag).
2. Emits an inline `document.addEventListener('DOMContentLoaded', ...)` script
   that calls `flatpickr(el, options)` on the field's `id`.

Date-only fields use `{ dateFormat: 'Y-m-d', allowInput: true }`.
Datetime fields use `{ enableTime: true, time_24hr: true, dateFormat: 'Y-m-d H:i:S', allowInput: true }`.

### Asset path note

`Asset::script()` and `Asset::stylesheet()` automatically prepend the `js/`
or `css/` folder — do **not** include it in the path:

```php
// ✅ Correct
Asset::script('assets/flatpickr.min.js', false, true);
Asset::stylesheet('assets/flatpickr.min.css', ['media' => 'all'], true);

// ❌ Wrong — resolves to core/assets/js/js/flatpickr.min.js
Asset::script('assets/js/flatpickr.min.js', false, true);
```

### Flatpickr theme overrides

Flatpickr's default colors are overridden in `admin.src.css` using daisyUI
CSS variables so the calendar matches the active admin theme automatically:

```css
.flatpickr-calendar { --fp-primary: var(--color-primary); ... }
```

### `Calendar` form field (Blade classes)

`Hubzero\Form\Fields\Calendar` adds daisyUI input classes in Blade mode so
the text input renders consistently before Flatpickr activates:

```php
if (self::isBlade()) {
    $attributes['class'] = trim('input input-bordered input-sm w-full' . ...);
}
```

The `Input::calendar()` method then appends `calendar-field` to whatever
class is already set (fixed concatenation — was previously overwriting).

## Radio / Checkbox Labels in `.admin-field`

### The problem

`.admin-field label` has `display: block` to give field labels their own
line. But radio/checkbox labels generated by `Radio.php` in Blade mode use
`class="flex items-center gap-2 cursor-pointer"` — and `display: block`
overrides `flex`, breaking the side-by-side radio+text layout.

### The fix

The rule uses `:not(.flex)` to exclude flex labels:

```css
/* admin.src.css */
.admin-field label:not(.flex) {
  display: block;
  ...
}
```

This means any `<label class="flex ...">` inside `.admin-field` (radio or
checkbox labels from PHP form field classes) renders as flex, while standard
field labels (`<label>` with no flex class) remain `display: block`.

### Radio label gap

`Radio.php` uses `gap-2` (8px) between the radio circle and its text in Blade
mode. Do not reduce this — `gap-1.5` (6px) is too tight and was the source of
repeated user complaints. The fieldset uses `gap-x-6 gap-y-2` to space
multiple radio options.

## CSS-Only Tabs (daisyUI)

For grouped content where JavaScript tab-switching is undesirable (e.g. menu
assignment in com_modules), use daisyUI's CSS-only radio-tab pattern:

```blade
<div class="tabs tabs-border" role="tablist">
  @foreach ($items as $i => $item)
    <input type="radio"
           name="my_tab_group"
           role="tab"
           class="tab text-sm"
           aria-label="{{ e($item->title) }}"
           @if ($i === 0) checked @endif />
    <div role="tabpanel" class="tab-content pt-3">
      {{-- tab content here --}}
    </div>
  @endforeach
</div>
```

- One `<input type="radio">` + one `<div role="tabpanel">` per tab, alternating.
- All radios share the same `name` — only one tab is open at a time.
- No JavaScript needed; CSS `:checked` sibling selectors handle visibility.
- `aria-label` on the radio provides the visible tab label.
- The `checked` attribute on the first radio opens it by default.

> **Note**: These radio inputs are purely for UI tab-switching — they are
> **not** submitted with the form. Give them a `name` that differs from any
> real form field.

## Responsive Toolbar

Toolbar buttons are always icon-only — the text label is hidden
unconditionally (not via media query).

### How it works

- `.admin-tb-btn span` is hidden unconditionally — no media query needed.
  Admin toolbar buttons always display as icon-only.
- Icons use `.admin-tb-icon` class with `1.25rem` size.
- Each button carries a `data-title` attribute. A CSS `::after` tooltip
  shows the label on hover when text is hidden.

### Per-variant sizing in icon-only mode

Circled icons (publish/unpublish) have extra detail inside, so they need
special treatment to look proportional:

| Variant | Padding | Icon size | Why |
|---------|---------|-----------|-----|
| Default | `0.5rem` | `1.25rem` | Standard icon-only button |
| `outline-success` / `outline-warning` | `0.25rem` | `1.75rem` | Less padding + bigger SVG keeps button same size but fills it |
| `ghost` (help/options) | `0.5rem` | `1.5rem` | No border frame, so icon must be larger to match visual weight |

The profile icon (`.admin-topbar-user-icon`) follows the same logic as
ghost buttons — `1.25rem` base, `1.5rem` in responsive mode.

### Button variants

All action buttons use outline style for consistency:

| Action | Variant class | Color | Icon |
|--------|--------------|-------|------|
| Save (apply) | `.admin-tb-outline-primary` | Primary | Plain checkmark |
| Save & Close | `.admin-tb-outline-primary` | Primary | Checkmark in circle |
| New | `.admin-tb-outline-primary` | Primary | Plus |
| Publish | `.admin-tb-outline-success` | Success (green) | Checkmark in circle |
| Unpublish | `.admin-tb-outline-warning` | Warning (amber) | X in circle |
| Delete, Trash | `.admin-tb-outline-error` | Error (red) | Trash can |
| Edit | `.admin-tb-neutral` | Transparent + border | Pencil |
| Cancel, Back | `.admin-tb-ghost` | Text only | X / Back arrow |
| Options, Help | `.admin-tb-ghost` | Text only | Gear / Question mark |

"Save" and "Save & Close" use different icons so they are visually
distinguishable in icon-only mode. Save is a plain checkmark (stay on page);
Save & Close is a circled checkmark (action complete, returning to list).

Outline borders use 50% opacity; neutral borders use 25%.

### Separators

Vertical separators (`.admin-tb-sep`) between button groups:
- `1px` wide, `1.5rem` tall (shrinks to `1rem` in responsive mode)
- Color: `25%` of `base-content`
- Margin: `0.25rem` horizontal

## Topbar Layout

The topbar uses a custom flex layout, **not** daisyUI's `navbar` component.
daisyUI `navbar-start`/`navbar-end` splits 50/50, which constrains toolbar
button space. The custom layout lets toolbar buttons expand as needed.

```
.admin-topbar           flex, align-center, gap, padding
├── .admin-topbar-start    title area, flex-shrink: 0
│   ├── hamburger label    (visible below lg:)
│   └── .admin-topbar-title
└── .admin-topbar-end      margin-left: auto (pushed right)
    ├── toolbar buttons    (from mod_toolbar)
    └── user dropdown
```

User name (`.admin-user-name`) is hidden by default and only shown at
`min-width: 1536px` to conserve toolbar space.

## Sidebar / Drawer Notes

### daisyUI drawer quirks

- **Scrollbar layout shift**: daisyUI's `drawer-open` sets `overflow-y: auto`
  on `.drawer-side`, causing a scrollbar to appear/disappear and shift the
  layout by ~15px. Fix: override with `overflow: hidden !important` since
  the flyout menu eliminates the need for sidebar scrolling.

- **Flash on load**: The drawer-side transitions from hidden to visible on
  page load (`opacity .2s, visibility .3s`). This causes a brief flash of
  the sidebar expanding. This is a minor cosmetic issue on hard refresh
  only — not worth fixing since removing the transition also breaks the
  mobile drawer slide-in animation.

- **Sidebar height**: Use `h-screen sticky top-0` instead of `min-h-full`
  on the `<aside>`. With flyout menus, the sidebar doesn't need to stretch
  beyond the viewport.

### Tailwind class scanning

Tailwind v4 `@source` directives scan Blade files for class names, but
classes constructed in PHP string concatenation (e.g., `'<svg class="w-5 h-5"'`)
may not be detected. Workarounds:

- Use custom CSS classes (e.g., `.admin-tb-icon`) instead of Tailwind
  utilities in PHP-generated HTML.
- Add custom classes in `admin.src.css` for one-off sizing (inline `style`
  attributes are blocked by CSP).
- Add explicit `@media` queries in `admin.src.css` rather than relying
  on responsive Tailwind utilities in PHP strings.

## Framework-Level Blade Views

Some framework views (Pagination, Parameter) live outside component
directories and also need Blade variants.

### How it works

The `View::loadTemplate()` method automatically looks for
`{layout}.blade.php` alongside `{layout}.php` when the engine is `blade`.
No manual `Document::getRenderEngine()` check is needed in the template
itself — just create the Blade file and the View class handles routing.

| Framework view | Legacy file | Blade file |
|----------------|-------------|------------|
| Main admin shell | `index.php` | `index.blade.php` |
| Component/iframe shell | `component.php` | `component.blade.php` |
| Login shell | `login.php` (Kameleon) | `login.blade.php` |
| Pagination | `Pagination/Views/paginator.php` | `Pagination/Views/paginator.blade.php` |
| Parameter | Uses inline engine check | `Html/Parameter.php` (inline) |

### Variable access

`renderBlade()` extracts the View's public properties as local variables.
Legacy templates use `$this->foo`; Blade templates use `$foo` directly.
The view instance is available as `$__view` for method calls.

Example — Paginator variables:

| Legacy (`$this->`) | Blade (local) | Description |
|---------------------|--------------|-------------|
| `$this->start` | `$start` | Current offset |
| `$this->limit` | `$limit` | Items per page |
| `$this->total` | `$total` | Total items |
| `$this->pages` | `$pages` | Page data object |
| `$this->prefix` | `$prefix` | Form field prefix |

### Pagination JS

The hzadmin template does **not** load the legacy `core.js` (which has
`Hubzero.paginate()`). Pagination click handlers live in `admin.js`'s
`initPagination()` instead. It targets:

- `.pagination a` — reads `data-prefix` / `data-start`, sets the hidden
  `limitstart` field, submits the form
- `.pagination select` — submits the form on change

The Blade paginator `<nav>` must include class `pagination` (in addition
to `admin-pagination`) so the JS selectors match. When there's only one
page (`$pages->total <= 1`), page buttons are hidden entirely — only the
results counter and per-page select are shown.

### Custom toolbar blocks

Most views use `<x-admin-toolbar>` for standard buttons. When a component
needs non-standard toolbar buttons (e.g., `deleteList` with a confirm
message, or no publish/unpublish), use a manual `@php Toolbar::* @endphp`
block instead:

```blade
@php
  Toolbar::title('Blog: Comments', 'blog');
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_BLOG_CONFIRM_DELETE');
  }
  Toolbar::editList();
  Toolbar::addNew();
@endphp
```

## Component Asset Loading (headData)

The legacy Kameleon template uses `<jdoc:include type="head" />` to render
component-registered stylesheets and scripts. The Blade shell
(`index.blade.php`) must replicate this using `Document::getHeadData()`:

```blade
{{-- In <head> --}}
@php
  $headData = Document::getHeadData();
  foreach ($headData['styleSheets'] ?? [] as $src => $attribs) { ... }
  foreach ($headData['style'] ?? [] as $type => $css) { ... }
@endphp

{{-- Before </body>, BEFORE admin.js so jQuery/Flot load first --}}
@php
  foreach ($headData['scripts'] ?? [] as $src => $attribs) { ... }
  foreach ($headData['script'] ?? [] as $type => $js) { ... }
@endphp
```

This is critical for components that use `Html::behavior('chart')`,
`$this->css()`, `$this->js()`, or other Document-level asset registration.
Without it, component views appear blank (no JS/CSS loads).

## Component Conversion Patterns

### Variable naming by component

Each component names its form fields differently. The Blade view must match
the controller's `Request::getArray()` call:

| Component | Field prefix | Example |
|-----------|-------------|---------|
| com_kb | `fields[*]` | `fields[title]` |
| com_blog | `fields[*]` | `fields[title]` |
| com_poll | `fields[*]` + `polloption[*]` | `fields[title]`, `polloption[3]` |
| com_answers | `question[*]` / `answer[*]` | `question[subject]` |
| com_billboards | `billboard[*]` | `billboard[name]` |
| com_citations | `citation[*]` / `type[*]` / `sponsor[*]` | `citation[title]` |
| com_wiki | `page[*]` / `revision[*]` / `fields[*]` | `page[title]`, `revision[summary]` |
| com_tags | `fields[*]` | `fields[raw_tag]` |
| com_services | `fields[*]` | `fields[title]` |

Always read the controller's `saveTask()` to confirm field names.

### Chart/visualization views

Some components (com_activity) display charts instead of tables. Wrap chart
containers in a decorative card for visual consistency:

```blade
<div class="bg-base-100 rounded-box border border-base-300 p-6">
  <h3 class="text-lg font-semibold mb-1">{{ $title }}</h3>
  <p class="text-sm text-subtle-foreground mb-4">{{ $subtitle }}</p>
  <div id="chart-container" class="min-h-[300px]"
       data-datasets="{{ $option }}-data"></div>
</div>
```

### Views with non-paginated data

Some views receive raw arrays instead of paginated collections:

| View | Data variable | Pagination |
|------|--------------|------------|
| com_citations sponsors | `$sponsors` (array) | None |
| com_citations types | `$types` (array) | None |
| com_citations stats | `$stats` (array) | None |
| com_blog comments | `$rows` (array from `treeRecurse`) | Manual via `$__view->pagination()` |

For array data, skip `{!! $rows->pagination !!}` and iterate directly.

### Checked-out records

Components with checkout protection (com_billboards, com_poll) disable
editing for checked-out records:

```blade
@if($checkedOut)
  {{-- Show checkedout indicator instead of checkbox --}}
  {!! Html::grid('checkedout', $row, $name, $time) !!}
  {{-- Show plain text instead of edit link --}}
  {{ e($row->name) }}
@else
  {!! Html::grid('id', $i, $row->id, false, 'cid') !!}
  <a href="{{ $editUrl }}">{{ e($row->name) }}</a>
@endif
```

### State toggle badges

For published/unpublished state toggles, use `data-list-item-task` for
CSP-safe form submission:

```blade
@php
  $badge = $row->published ? 'badge-success' : 'badge-error';
  $alt   = $row->published ? Lang::txt('JPUBLISHED') : Lang::txt('JUNPUBLISHED');
  $task  = $row->published ? 'unpublish' : 'publish';
@endphp
<a href="#"
   data-list-item-task
   data-cb="cb{{ $i }}"
   data-task="{{ $task }}">
  <span class="badge badge-sm {{ $badge }}">{{ $alt }}</span>
</a>
```

This checks the row's checkbox, sets the task, and submits the form — all
via event delegation in `admin.js`. No inline `onclick` needed.

For components where state toggles use direct URL links instead (e.g.,
com_citations with session token in the URL), a plain `<a href>` is fine
since no JavaScript is involved:

```blade
<a href="{{ Route::url('...&task=' . $task . '&id=' . $row->id . '&' . Session::getFormToken() . '=1', false) }}">
  <span class="badge badge-sm {{ $badge }}">{{ $alt }}</span>
</a>
```

### Ordering columns

Components with manual ordering (com_billboards) render an inline input:

```blade
<input type="text" name="order[]" size="5"
       value="{{ $row->ordering }}"
       class="input input-bordered input-xs w-16 text-center" />
```

The order save icon comes from `Html::grid('order', $rows->toArray())` in
the column header.

### Large edit forms

Components like com_citations have 30+ fields. Organize with:

1. **Grid columns** for short paired fields (year/month, volume/issue)
2. **Single column** for full-width fields (title, abstract, URL)
3. **Multiple `admin-fieldset` sections** to group related fields
4. **Sidebar fieldsets** for metadata, associations, options
5. **WYSIWYG editors** via `$__view->editor(...)` for rich text fields

### Single-controller components

Components with only one controller (com_poll, com_activity) pass
`controller=""` to the admin components since the route doesn't need it:

```blade
<x-admin-edit option="{{ $option }}" controller="">
```

### File upload forms

Components with file uploads (com_billboards background images) use
`enctype="multipart/form-data"` which `<x-admin-edit>` handles. Use
daisyUI's file input class:

```blade
<input type="file" name="billboard-image" id="billboard-image"
       class="file-input file-input-bordered w-full" />
```

### Access level toggles

Components like com_collections have clickable access columns that cycle
through Public → Registered → Private:

```blade
@php
  $accessCls  = ['public' => 'badge-success', 'registered' => 'badge-warning', 'private' => 'badge-error'];
  $accessTask = ['public' => 'accessregistered', 'registered' => 'accessprivate', 'private' => 'accesspublic'];
  $accessKey  = [0 => 'public', 1 => 'registered', 4 => 'private'];
  $key = $accessKey[$row->get('access')] ?? 'public';
@endphp
<a class="badge {{ $accessCls[$key] }}" href="{{ $toggleUrl }}">
  {{ Lang::txt('COM_COLLECTIONS_ACCESS_' . strtoupper($key)) }}
</a>
```

### Linked count columns

List views often show a count that links to a filtered sub-list (e.g., a
collection's post count linking to posts filtered by that collection):

```blade
<td>
  <a href="{{ Route::url('index.php?option=' . $option . '&controller=posts&collection_id=' . $row->get('id'), false) }}">
    {{ $row->posts()->total() }}
  </a>
</td>
```

### Conditional column display

When a list is filtered by a parent (e.g., posts filtered by collection_id),
hide the parent column since it's redundant:

```blade
@if(!$filters['collection_id'])
  <th scope="col">{{ Lang::txt('COLLECTION') }}</th>
@endif
{{-- ... in tbody ... --}}
@if(!$filters['collection_id'])
  <td>{{ $row->collection()->get('title') }}</td>
@endif
```

### Alternative layouts for embedded views

Some views have a compact `display_alt` layout used when loaded in an iframe
(e.g., from an edit form's tab). The controller switches layout based on
`tmpl=component`:

```blade
{{-- posts/tmpl/display_alt.blade.php --}}
{{-- No checkboxes, no bulk actions — just a simple table with per-row delete links --}}
```

### Asset/media sub-views

Components with file uploads (com_collections) use partial templates for
AJAX-loaded asset lists. The partial renders individual asset rows that can
be added/removed dynamically:

```blade
{{-- media/tmpl/_asset.blade.php --}}
<p class="item-asset">
  <span class="asset-handle">&#8942;</span>
  <span class="asset-file">{{ e($asset->get('filename')) }}</span>
  <a class="delete" href="{{ $deleteUrl }}" data-id="{{ $asset->get('id') }}">
    {{ Lang::txt('JACTION_DELETE') }}
  </a>
  <input type="hidden" name="assets[{{ $i }}][id]" value="{{ $asset->get('id') }}" />
  <input type="hidden" name="assets[{{ $i }}][filename]" value="{{ $asset->get('filename') }}" />
</p>
```

### Media Manager (com_media)

The Media Manager uses the legacy `qq.FileUploader` library for file uploads.
Key CSS fixes live in `media.css` (not a separate Blade-only file):

**Breadcrumb alignment** — `.media-breadcrumbs-block` uses `display: flex;
align-items: center` to vertically align the folder icon, chevron separator,
and crumb links.

**Drag-and-drop overlay** — `.qq-upload-drop-area` needs `z-index: 10` and
`background: rgba(0, 0, 0, 0.7)` to stack above the file thumbnail grid.
Without the z-index, the overlay renders behind the thumbnails and the
instruction text is unreadable. The `span` inside uses absolute centering
(`top: 50%; left: 50%; transform: translate(-50%, -50%)`) to center the
upload icon and text.

**File picker** — The `qq.FileUploader` library creates a transparent
`<input type="file">` overlaid on the upload button using the "giant font
trick" (`font-size: 118px; opacity: 0`). Do **not** hide this input with
`display: none` or the visually-hidden pattern — the native overlay approach
works in all browsers and allows the OS file dialog to open on click.

**Error messages** — The upload error for incorrect file types uses
`Lang::txt('COM_MEDIA_ERROR_INCORRECT_FILE_TYPE', $ext)` with a `%s`
placeholder. Do not concatenate the extension directly — pass it as a
`Lang::txt()` argument so the message reads naturally
(e.g., "Incorrect file type: .css is not allowed.").

### @include paths for component partials

The Blade view renderer automatically registers a namespace for the current
component (e.g., `com_newsletter`) pointing to its base directory. Use this
namespace with `@include` to reference partials within the same component:

```blade
@include('com_newsletter::admin.views.newsletters.tmpl._dependency')
```

The namespace is derived from the template path — `components/com_xxx/` in the
filesystem becomes the `com_xxx::` Blade namespace. The path after `::` uses
dot-notation relative to the component root, mapping to subdirectories and files:

```
com_newsletter::admin.views.newsletters.tmpl._dependency
→ components/com_newsletter/admin/views/newsletters/tmpl/_dependency.blade.php
```

**Do not** use bare dot-notation like `@include('newsletters._dependency')` —
Blade cannot resolve paths relative to the current template's directory. The
full `com_xxx::` namespaced path is required.

Template-level partials use the `hubzero::` or `hzadmin::` namespace instead
(see [blade-page-shell.md](blade-page-shell.md)).

### Plugin-generated admin content

Some admin views delegate rendering to plugins via event triggers. The view
echoes whatever HTML the plugins return (com_search basic view):

```blade
@php
  $html = Event::trigger('search.onSearchAdministrate');
@endphp
@foreach ($html as $output)
  {!! $output !!}
@endforeach
```

The plugin itself should use `$this->view()` to render a Blade template rather
than building HTML inline. Convert static plugin methods to instance methods
so `$this->view()` is available (see com_search sitemap plugin).

### Merge/pierce operations (two-panel select-and-target)

Tags and similar components have bulk operations that take selected items and
merge/copy them to a target. Use a two-column grid with a warning alert:

```blade
<div role="alert" class="alert alert-warning mb-4">
  {{ Lang::txt('COM_TAGS_MERGED_EXPLANATION') }}
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div class="admin-fieldset">
    <h3 class="admin-fieldset-heading">Source items</h3>
    <div class="admin-fieldset-body">
      <ul class="list-disc pl-5 space-y-1">
        @foreach ($items as $item)
          <li>{{ e($item->get('name')) }} ({{ $item->objects()->total() }})</li>
        @endforeach
      </ul>
    </div>
  </div>
  <div class="admin-fieldset">
    <h3 class="admin-fieldset-heading">Target</h3>
    <div class="admin-fieldset-body">
      {{-- Tag autocomplete or text input --}}
    </div>
  </div>
</div>
```

### Conditional action panels

Subscription/order management views show different action sets based on record
status (com_services subscriptions). Use radio buttons to select an action,
with conditional form sections:

```blade
@if ($record->status == 2)
  {{-- Cancelled: show refund options --}}
  <div class="admin-field">
    <label class="label cursor-pointer justify-start gap-2">
      <input type="radio" name="action" value="refund" class="radio radio-sm" />
      <span>Process Refund</span>
    </label>
  </div>
@else
  {{-- Active: show activate/cancel options --}}
  <div class="admin-field">
    <label class="label cursor-pointer justify-start gap-2">
      <input type="radio" name="action" value="activate" class="radio radio-sm" />
      <span>Activate</span>
    </label>
  </div>
@endif
```

### Component JS/CSS preservation

Views with D3 graphs, jVectorMap, AJAX preview, or other component-specific
JS must keep the asset loading calls. Use `$__view` instead of `$this`:

```blade
@php
  $__view->css('tag_graph.css');
  $__view->js('d3.js', 'system')
      ->js('tag_graph.js');
@endphp
```

The hzadmin shell renders these via `Document::getHeadData()`. Do **not**
replace these with inline `<script>` or `<link>` tags.

### Static overview / info pages

Components with no data-driven admin (com_saml, com_oaipmh) use card grids
to present configuration info and links:

```blade
<x-admin-toolbar title="SAML Authentication" icon="saml" option="{{ $option }}" />

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div class="admin-fieldset">
    <h3 class="admin-fieldset-heading">Overview</h3>
    <div class="admin-fieldset-body prose prose-sm max-w-none">
      <p>Description text...</p>
    </div>
  </div>
  {{-- More cards --}}
</div>
```

### Placeholder admin views

Components with no admin capabilities (com_usage) show a warning alert
directing users to preferences:

```blade
<x-admin-toolbar title="Usage" icon="usage" option="{{ $option }}" />
<div role="alert" class="alert alert-warning">
  {{ Lang::txt('COM_USAGE_WARNING') }}
</div>
```

### Consolidating legacy partials

When legacy views have many small partials (com_search boosts had 10), merge
them into fewer clean Blade files. Map the consolidation:

| Legacy partials | Blade file |
|-----------------|------------|
| `_boosts_list.php`, `_boosts_list_header.php`, `_boost_item.php`, `_tag_search_notice.php` | `list.blade.php` |
| `_boost_form.php`, `_boost_document_type_select.php` | `new.blade.php` |
| `_boost_form_edit.php`, `_boost_details_fieldset.php`, `_boost_metadata_table.php` | `edit.blade.php` |

### Language strings with HTML

Some `Lang::txt()` values contain HTML (e.g., `<p>` tags in explanatory
text). Use `{!! !!}` to render them, not `{{ }}` which escapes the HTML:

```blade
{{-- BAD: renders <p> tags as visible text --}}
{{ Lang::txt('COM_TAGS_GROUP_EXPLANATION') }}

{{-- GOOD: renders HTML properly --}}
{!! Lang::txt('COM_TAGS_GROUP_EXPLANATION') !!}
```

Only use `{!! !!}` when you know the string is a trusted language constant.
Never use it with user-supplied data.

### Dynamic form groups (add/remove)

Focus area management (com_tags) uses JavaScript to add/remove fieldset
groups. Wrap each group in an identifiable container and provide add/delete
buttons:

```blade
<div id="fas" class="space-y-4">
  @foreach ($groups as $i => $group)
    <div class="admin-fieldset" id="group-{{ $i }}">
      {{-- Group fields --}}
      <button class="btn btn-sm btn-error delete-group"
              id="delete-{{ $i }}" rel="group-{{ $i }}">
        Delete Group
      </button>
    </div>
  @endforeach
</div>
<button id="add_group" class="btn btn-sm btn-primary">Add Group</button>
```

Keep the component's JS file (`$__view->js('tag_graph.js')`) which handles
the DOM manipulation for add/remove.

## Facade Namespace Collisions

Component model files sometimes `use Hubzero\Facades\Log` (or other
facades) that shadow a same-named model class in the component's own
namespace. Example: `Components\Wiki\Models\Page` imported
`Hubzero\Facades\Log`, which shadowed `Components\Wiki\Models\Log`.
Calling `Log::blank()` then hit the logging facade (no `blank` method)
instead of the wiki Log model.

**Fix**: Remove the facade import when a same-named class exists in the
component namespace. PHP resolves the unqualified name to the local
namespace first.

Common collision names: `Log`, `Event`, `Cache`, `Route`.

## Text Encoding

### Route::url() double-encoding

`Route::url($url, $xhtml)` defaults to `$xhtml = true`, which runs
`htmlspecialchars()` on the URL. Blade's `{{ }}` then double-encodes
ampersands to `&amp;amp;`.

**Pattern A — Variable assignment in `@php` block** (preferred):
Pass `false` as second arg so `{{ }}` handles the single encoding:

```blade
@php
  $editUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller,
      false
  );
@endphp
<a href="{{ $editUrl }}">Edit</a>
```

**Pattern B — Inline output** (for simple one-off URLs):
Use `{!! !!}` since Route::url() already returns HTML-safe output:

```blade
<a href="{!! Route::url('index.php?option=' . $option) !!}">Link</a>
```

### Language string encoding

Language INI files store values with HTML entities (e.g., `Save &amp; Close`).
Do **not** wrap `Lang::txt()` output in `e()` or `{{ }}` when outputting
as raw HTML (e.g., inside button text), or the entities will be
double-encoded. Use `{!! !!}` or omit `e()` in PHP string building.

## Accessibility Notes

- Table has sortable column headers with `Html::grid('sort', ...)`
- Checkbox column uses `aria-label` for screen readers
- Filter controls have associated `<label>` elements
- Status badges use semantic text, not just color
- Form fields use `required` attribute for native validation
- Submenu uses `<nav>` with `aria-label`

---

## Form Field Backward Compatibility (`isBlade()`)

Form field classes (e.g. `Hubzero\Form\Fields\*`) render output in both
legacy PHP views and Blade views. Gate any Tailwind/daisyUI markup behind
`self::isBlade()` so legacy rendering is unaffected.

```php
protected function getInput()
{
    $isBlade = self::isBlade();

    if ($isBlade) {
        // daisyUI/Tailwind markup
        $html[] = '<input class="input input-bordered input-sm w-full" ... />';
    } else {
        // Legacy markup with legacy classes
        $html[] = '<input class="orcid" ... />';
    }
    return implode($html);
}
```

Key locations:
- `core/libraries/Hubzero/Form/Fields/*.php` — core field types
- `core/components/com_members/models/fields/*.php` — member-specific fields
  (`address.php`, `orcid.php`)

### Html Builder Backward Compatibility (`isBlade()`)

Html builder classes in `core/libraries/Hubzero/Html/Builder/` follow the same
dual-output pattern as form fields. Each method that generates styled HTML checks
the render engine and branches between daisyUI markup (blade) and legacy markup.

The `isBlade()` / `esc()` helpers are defined on each builder class:

```php
protected static function isBlade(): bool
{
    try {
        return App::get('document')->getRenderEngine() === 'blade';
    } catch (\Exception $e) {
        return false;
    }
}

protected static function esc($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8', true);
}
```

#### `Html::access()` — Access & Permission Widgets

`Hubzero\Html\Builder\Access` renders permission selects and checkbox trees.
All five public methods are blade-aware:

| Method | Blade output | Legacy output |
|--------|-------------|---------------|
| `level($name, $selected)` | `<select class="select select-sm select-bordered w-full">` | `Select::genericlist(...)` |
| `usergroup($name, $selected)` | `<select class="select select-sm select-bordered w-full">` | `Select::genericlist(...)` |
| `assetgrouplist($name, $selected)` | `<select class="select select-sm select-bordered w-full">` | `Select::genericlist(...)` |
| `usergroups($name, $selected)` | `<ul class="space-y-1">` with daisyUI checkboxes | `<ul class="checklist usergroups">` |
| `actions($name, $selected, $component)` | `<ul class="space-y-1">` with daisyUI checkboxes | `<ul class="checklist access-actions">` |

**Checkbox list pattern (blade)**:

```html
<ul class="space-y-1">
  <li class="flex items-center gap-2">
    <input type="checkbox" class="checkbox checkbox-sm"
           name="jform[groups][]" value="2" id="1group_2" />
    <label for="1group_2" class="label label-text cursor-pointer text-sm">
      Registered
    </label>
  </li>
  <li class="flex items-center gap-2">
    <input type="checkbox" class="checkbox checkbox-sm"
           name="jform[groups][]" value="8" id="1group_8" />
    <label for="1group_8" class="label label-text cursor-pointer text-sm">
      <span class="text-faint-foreground select-none">|&mdash;</span>Author
    </label>
  </li>
</ul>
```

Hierarchy indentation uses `<span class="text-faint-foreground select-none">|&mdash;</span>`
repeated `$item->level` times (replaces the legacy `<span class="gi">|&mdash;</span>`).

In blade mode, `data-parent="Ngroup_M"` replaces the legacy `rel="Ngroup_M"` attribute
on child checkboxes (valid HTML5 data attribute instead of the legacy `rel` hack).

#### `Html::batch()` — Batch Processing Widgets

`Hubzero\Html\Builder\Batch` was the reference implementation for this pattern.
All four methods (`access`, `item`, `language`, `user`) are blade-aware:

| Method | Blade output |
|--------|-------------|
| `access()` | `<div>` with label + `<select class="select select-sm select-bordered w-full">` |
| `item($extension)` | Category select + move/copy radio buttons (daisyUI `radio radio-sm`) |
| `language()` | Label + language select |
| `user($noUser)` | Label + user select |

### Icon alignment in `.btn` (Tailwind v4 preflight)

Tailwind v4 preflight sets `img { display: block }` and `svg { display: block }`.
Inside a daisyUI `.btn` (which is `inline-flex`), this causes icon elements
to stack vertically instead of sitting inline with text.

**Fix**: Wrap icon + text in `<span class="flex items-center gap-1.5">` inside
the `<a class="btn">`:

```php
$html[] = '<a href="..." class="btn btn-sm btn-ghost border border-base-300 font-normal">'
    . '<span class="flex items-center gap-1.5">'
    . $icon . Lang::txt('COM_MEMBERS_PROFILE_ORCID_CREATE_OR_CONNECT')
    . '</span></a>';
```

### Tailwind scan paths for form field PHP files

Field PHP files in `core/components/*/models/fields/` are not in the default
Tailwind scan. Add them explicitly in `admin.src.css`:

```css
@source "../../../../core/libraries/Hubzero/Form/Fields/*.php";
@source "../../../../core/components/com_members/models/fields/*.php";
```

---

## Multi-Entry Fields (Address Pattern)

When a profile field supports multiple entries (e.g. addresses), the blade
output wraps each entry in a card and adds JS-driven add/remove:

```html
<!-- Each address card -->
<div class="address-field-wrap bg-base-200 border border-base-300 rounded-lg p-3 space-y-2">
  <!-- fields -->
  <div class="flex justify-end mt-2">
    <a class="address-remove btn btn-xs btn-ghost text-error" href="#">&minus; Remove</a>
  </div>
</div>

<!-- Add another -->
<div class="flex justify-end mt-2">
  <a class="btn btn-sm btn-ghost border border-base-300 font-normal" href="#">+ Add another address</a>
</div>
```

**DOM traversal note**: When a Remove button is wrapped in a `<div>`, use
`.closest('.address-field-wrap')` not `.parent()` to remove the entire card:

```javascript
$(this).closest('.address-field-wrap').remove();
```

---

## Media Upload (tmpl=component iframe)

Some admin edit views embed a file upload tool in a `<iframe>` using
`tmpl=component`. The `component.blade.php` shell loads `admin.css`
automatically, so Tailwind classes work inside the iframe.

### Pattern

In the parent edit view (`edit_profile.blade.php`):
```blade
<iframe class="w-full border-0 rounded"
        height="420"
        name="filer"
        id="filer"
        src="{!! $iframeSrc !!}"></iframe>
```

In the iframe view (`media/tmpl/display.blade.php`):
```blade
{{-- Two circular previews --}}
<div class="flex gap-6 items-end">
  <div class="flex flex-col items-center gap-1">
    <div class="w-32 h-32 rounded-full bg-base-200 border-2 border-base-300 overflow-hidden">
      <img src="{{ $profile->picture(0, false) }}" class="w-full h-full object-cover" id="conimage" />
    </div>
    <span class="text-xs text-faint-foreground">200 &times; 200</span>
  </div>
  <!-- 50×50 circle similarly -->
</div>

{{-- Styled file chooser (hidden input + label) --}}
<input type="file" name="upload" id="upload" class="hidden" accept=".png,.jpg,.jpeg,.gif" />
<label for="upload" class="btn btn-sm btn-ghost border border-base-300 font-normal cursor-pointer">
  Choose photo&hellip;
</label>
<button type="submit" class="btn btn-sm btn-primary">Upload</button>

{{-- Destructive delete link --}}
<a href="{!! $removeUrl !!}" onclick="return confirm('...')"
   class="btn btn-sm btn-ghost text-error font-normal">
  &minus; Delete
</a>
```

Key points:
- Use `<label for="upload">` + `<input class="hidden">` instead of native file
  picker to get consistent button styling
- JS `change` listener on the hidden input shows the selected filename
- Both sizes are shown as circular avatar previews (`rounded-full`) with
  `object-cover` for correct cropping
- `onclick="return confirm(...)"` on the delete link prevents accidental deletion
- Drawer sidebar uses daisyUI's accessible toggle pattern
