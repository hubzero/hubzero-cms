# Behavior System

The `Hubzero\Html\Builder\Behavior` class provides static methods that load
JavaScript behaviors into a page. Each method handles asset loading, deduplication,
and initialization — call it from PHP and the behavior just works.

```php
use Hubzero\Html\Builder\Behavior;

Behavior::colorpicker();   // loads colpick CSS/JS, initializes .input-colorpicker elements
Behavior::flatpickr();     // loads flatpickr CSS/JS, initializes [data-flatpickr] elements
Behavior::modal();         // loads FancyBox, initializes a.modal elements
```

---

## How It Works

Every behavior has two modes, selected automatically by the CSS framework:

### Legacy mode (classic)

The behavior injects inline JavaScript via `addScriptDeclaration()` to
initialize jQuery plugins on specific selectors. This is the traditional
approach and works with legacy PHP templates.

### CSP mode (daisyui)

Inline JS is forbidden (`addScriptDeclaration()` throws `RuntimeException`
in blade mode). Instead, behaviors:

1. **Load external assets** — CSS/JS files via `Asset::script()` and
   `Asset::stylesheet()`. This is CSP-safe.
2. **Mark elements with data attributes** — target elements already have
   identifying classes (`.input-colorpicker`, `.hasTip`) or get data
   attributes added by the caller (`data-flatpickr`, `data-picker-url`).
3. **admin.js scans and initializes** — `initBehaviors()` in
   `core/templates/hzadmin/js/admin.js` runs at DOMContentLoaded, scanning
   for each behavior's target elements and running the init code.

This means the same `Behavior::colorpicker()` call works in both legacy
PHP views and modern Blade views — the behavior handles the branching
internally.

---

## Complete Method Reference

### Asset-loading behaviors (CSP-safe, no branching needed)

| Method | What it loads |
|--------|-------------|
| `framework($extras)` | jQuery core; pass `true` for jQuery UI |
| `core()` | `hubzero.js` core utilities |
| `formvalidation()` | `validate.js` form validation |
| `htmx($version)` | htmx library |
| `alpinejs($version)` | Alpine.js library |
| `htmxalpine()` | htmx + Alpine.js together |
| `bootstrap($version)` | Bootstrap 5 CSS/JS |
| `chart($type)` | flot chart library |
| `combobox()` | Combobox UI widget |
| `calendar()` | jQuery datepicker/timepicker (legacy) |
| `inertia()` | Debug timeline/panel assets |

### Interactive behaviors (dual-mode: legacy inline JS / CSP data-attribute)

| Method | Target selector | Data attribute | admin.js function |
|--------|----------------|----------------|-------------------|
| `tooltip($selector)` | `.hasTip` | — | `initBehaviorTooltip()` |
| `modal($selector, $params)` | `a.modal` | `data-modal-options` | `initBehaviorModal()` |
| `caption($selector)` | `img.caption` | — | `initBehaviorCaption()` |
| `colorpicker()` | `.input-colorpicker` | — | `initBehaviorColorpicker()` |
| `switcher($toggler)` | `#toggler-id` | `data-switcher` | `initBehaviorSwitcher()` |
| `multiselect($id)` | `#form-id` | `data-multiselect` | `initBehaviorMultiselect()` |
| `flatpickr()` | `[data-flatpickr]` | `data-flatpickr="date\|datetime"` | `initFlatpickr()` |
| `picker()` | `[data-picker-url]` | `data-picker-url`, `data-value-field`, `data-display-field` | `initPickerButtons()` |
| `keepalive()` | — | `<meta name="behavior-keepalive">` | `initBehaviorKeepalive()` |
| `highlighter($terms)` | — | `<meta name="behavior-highlight">` | `initBehaviorHighlighter()` |
| `math()` | — | `<meta name="behavior-math">` | `initBehaviorMath()` |
| `noframes()` | — | — | Sets `X-Frame-Options` header only |

### No-ops

| Method | Notes |
|--------|-------|
| `uploader()` | Does nothing (legacy stub) |
| `tree()` | All code commented out |

---

## Usage Examples

### Date picker (flatpickr)

```php
// In a controller or view:
Behavior::flatpickr();

// In the template, add the data attribute:
<input type="text" name="publish_up" data-flatpickr="datetime" />
```

admin.js scans for `[data-flatpickr]` and initializes flatpickr with the
appropriate options (`date` or `datetime` mode).

Flatpickr assets are loaded on demand — `Behavior::flatpickr()` registers
the CSS and JS with the Document system, which renders them in the page
shell. The `Input::calendar()` helper calls `Behavior::flatpickr()`
automatically in daisyui mode.

### Color picker

```php
Behavior::colorpicker();
```

```html
<input type="text" name="color" class="input-colorpicker" value="#ff0000" />
```

admin.js scans for `.input-colorpicker` and initializes colpick.

### Picker modal (user, article, media selection)

```php
Behavior::picker();
```

```html
<button type="button"
    data-picker-url="index.php?option=com_members&layout=modal&tmpl=component"
    data-picker-width="800"
    data-picker-height="500"
    data-value-field="user_id"
    data-display-field="user_name">
    Select User
</button>
```

admin.js opens an iframe modal. Inside the modal, elements with
`data-article-select` post a `picker-select` message to the parent,
which populates the value and display fields.

#### Legacy callback bridge

Legacy picker templates (e.g. the member picker modal) call
`window.parent.jSelectUser_FIELDID(id, title)` via the
`data-parent-callback` pattern. In blade mode there's no inline JS to
define that function. The picker popup solves this with
`data-picker-callback`:

```html
<button type="button"
    data-picker-url="index.php?option=com_members&layout=modal&tmpl=component&field=jform_user_id"
    data-picker-callback="jSelectUser_jform_user_id"
    data-value-field="jform_user_id_id"
    data-display-field="jform_user_id_name">
    Change User
</button>
```

When `openPickerPopup()` sees `data-picker-callback`, it temporarily
registers that function on `window`. The legacy iframe template calls
`window.parent.jSelectUser_*(id, title)`, which updates the value/display
fields and closes the popup. The function is cleaned up on close.

This means existing picker modal templates work without modification in
blade mode — no need to convert them to `postMessage`.

### Session keepalive

```php
Behavior::keepalive();
```

In daisyui mode, emits a `<meta name="behavior-keepalive">` tag with
the computed refresh interval. admin.js reads it and starts an AJAX
polling loop. In legacy mode, injects the polling script inline.

### Tooltips

In daisyui mode, most tooltips are pure CSS via the `.tooltip` class and
`data-tip` attribute — no JS needed. For jQuery UI tooltip compatibility,
`Behavior::tooltip()` still works; admin.js initializes `.hasTip` elements.

---

## Layout Helpers: Sliders and Tabs

`Hubzero\Html\Builder\Sliders` and `Hubzero\Html\Builder\Tabs` are
higher-level helpers that emit structured markup for accordions and tabs.
Like behaviors, they render mode-appropriate markup automatically.

### Sliders (accordions)

```php
use Hubzero\Html\Builder\Sliders;

echo Sliders::start('my-accordion');
echo Sliders::panel('Section One', 'section-1');
// ... section one content ...
echo Sliders::panel('Section Two', 'section-2');
// ... section two content ...
echo Sliders::end();
```

| Mode | Markup |
|------|--------|
| Legacy | `<div class="pane-sliders">` + jQuery UI accordion init via inline JS |
| daisyUI | `<div class="join join-vertical">` + `<details class="collapse collapse-arrow">` elements (pure CSS, no JS) |

In daisyui mode the first panel starts `open` by default.

### Tabs

```php
use Hubzero\Html\Builder\Tabs;

echo Tabs::start('my-tabs');
echo Tabs::panel('Tab One', 'one');
// ... tab one content ...
echo Tabs::panel('Tab Two', 'two');
// ... tab two content ...
echo Tabs::end();
```

| Mode | Markup |
|------|--------|
| Legacy | `<dl class="tabs">` + `<dt>`/`<dd>` elements + jQuery tabs init via inline JS |
| daisyUI | `<div role="tablist" class="tabs tabs-bordered">` + radio-input tabs (pure CSS, no JS) |

In daisyui mode the first tab is `checked` by default.

### When to use vs. direct markup

These helpers are most valuable when:

- **Converting a legacy PHP view** that already calls the API — the same
  `Sliders::start()`/`panel()`/`end()` calls render correctly in both modes
- **Simple sequential panels** without custom styling

For blade views that need more control — custom classes, dynamic
`@foreach` generation, nested structures, or panels positioned elsewhere
in the DOM — write daisyUI markup directly. The helpers emit fixed class
patterns that may not match every design need.

Existing blade views use these patterns directly:

- **Accordions**: `<details class="collapse collapse-arrow">` with
  `<summary>` and `<div class="collapse-content">`
- **Tabs**: `data-tab-target` buttons with separate panel `<div>`s
  (admin.js toggles `hidden` class)

Both approaches are CSP-safe. The choice is about convenience vs. flexibility.

---

## Adding a New Behavior

1. Add a static method to `Behavior.php` following the pattern:

```php
public static function mywidget()
{
    if (isset(self::$loaded[__METHOD__])) {
        return;
    }

    // Load external assets (works in both modes)
    Asset::stylesheet('assets/mywidget.css', array('media' => 'all'), true);
    Asset::script('assets/mywidget.js', false, true);

    if (Document::getCssFramework() !== 'daisyui') {
        // Legacy: inline JS initialization
        App::get('document')->addScriptDeclaration(
            "jQuery(document).ready(function($){"
            . "$('.my-widget').myWidget();"
            . "});"
        );
    }
    // DaisyUI: admin.js handles init via selector scanning

    self::$loaded[__METHOD__] = true;
}
```

2. Add an init function to `admin.js`:

```javascript
function initBehaviorMywidget() {
    if (typeof jQuery === 'undefined' || !jQuery.fn.myWidget) return;
    document.querySelectorAll('.my-widget:not([data-init])').forEach(function(el) {
        el.setAttribute('data-init', 'mywidget');
        jQuery(el).myWidget();
    });
}
```

3. Call `initBehaviorMywidget()` from `initBehaviors()` in `initAll()`.

---

## Key Files

| File | Role |
|------|------|
| `core/libraries/Hubzero/Html/Builder/Behavior.php` | Behavior methods (PHP) |
| `core/libraries/Hubzero/Html/Builder/Sliders.php` | Accordion layout helper (PHP) |
| `core/libraries/Hubzero/Html/Builder/Tabs.php` | Tab layout helper (PHP) |
| `core/templates/hzadmin/js/admin.js` | Behavior init scanning (JS) |
| `core/libraries/Hubzero/Html/Builder/Asset.php` | Asset loading helpers |
| `core/libraries/Hubzero/Html/Builder/Input.php` | Form input helpers (`calendar()` calls `Behavior::flatpickr()`) |
| `core/libraries/Hubzero/Document/Base.php` | CSP enforcement (`addScriptDeclaration` throws in blade) |
