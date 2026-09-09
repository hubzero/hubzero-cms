# Client-Side Framework Detection

JavaScript running in the browser needs to know the active CSS framework and
view engine to branch rendering logic. The PHP negotiation API
([View Engine & CSS Framework Negotiation](../../view-engine-css-framework.md))
resolves these server-side, but client-side code can't call `Document::getCssFramework()`.

## Data Attributes on `<html>`

Every page shell (site and admin) sets two data attributes on the `<html>` element:

```html
<html lang="en" data-theme="hubzero"
      data-css-framework="daisyui" data-view-engine="blade">
```

| Attribute | Values | Purpose |
|-----------|--------|---------|
| `data-css-framework` | `"daisyui"` or `"classic"` | Which CSS class vocabulary to emit |
| `data-view-engine` | `"blade"` or `"php"` | Which template engine rendered the page |

These are the client-side equivalents of `Document::getCssFramework()` and
`Document::getViewEngine()`.

### Reading from JavaScript

```js
var isDaisyUi = document.documentElement.getAttribute('data-css-framework') === 'daisyui';
var isBlade   = document.documentElement.getAttribute('data-view-engine') === 'blade';
```

Declare detection variables at the top level (outside any IIFE) when they need
to be readable by code in other scopes — for example, jQuery plugins that run
inside `(function ($) { ... }(jQuery))` and initialization functions that run
at `DOMContentLoaded`.

### Page Shells That Set These

**Site template** (`app/templates/hubzero/`):
- `index.blade.php`
- `component.blade.php`

**Admin template** (`core/templates/hzadmin/`):
- `index.blade.php`
- `cpanel.blade.php`
- `login.blade.php`
- `component.blade.php`
- `help.blade.php`

Legacy page shells (`index.php`) do not set these attributes. If the attribute
is absent, JavaScript should assume `classic` / `php`.

---

## Behavior System (site.js / admin.js)

Both the site and admin templates include a `site.js` or `admin.js` that
provides behavior support. Behaviors are PHP-side features that inject
`<meta>` tags to signal the JS layer:

```php
// PHP (plugin or component)
Document::setMetaData('behavior-keepalive', '1');
Document::setMetaData('behavior-highlight', '1');
Document::setMetaData('behavior-math', '1');
```

```js
// JS (site.js or admin.js)
var meta = document.querySelector('meta[name="behavior-keepalive"]');
if (meta) {
    // Initialize keepalive ping
}
```

This replaces legacy `Html::behavior('*')` calls that injected inline
`<script>` tags (which violate strict CSP).

### Available Behaviors

| Meta Name | Purpose | Notes |
|-----------|---------|-------|
| `behavior-keepalive` | Periodic session ping | Interval from `content` attribute |
| `behavior-highlight` | Syntax highlighting (Prism) | Loads Prism CSS/JS |
| `behavior-math` | LaTeX math rendering (KaTeX) | Loads KaTeX CSS/JS |
| `behavior-tooltip` | Initialize tooltips | daisyUI tooltips, no JS needed |
| `behavior-modal` | Modal/popup support | Reads `data-picker-url` attributes |
| `behavior-caption` | Image captions | Wraps images in figure elements |

---

## Plugin CSS: Framework-Conditional Loading

Plugins that generate HTML need different CSS for daisyUI vs classic mode.
Rather than putting plugin-specific styles in the template's `site.src.css`,
each plugin provides its own framework-specific CSS file and selects which to
load based on `Document::getCssFramework()`.

### Pattern

```php
use Hubzero\Facades\Document;

$isDaisyUi = Document::getCssFramework() === 'daisyui';
$cssFile = $isDaisyUi ? 'myplugin.blade.css' : 'myplugin.css';

// Check for template override first
$templatecss = DS . 'templates' . DS . App::get('template')->template
    . DS . 'html' . DS . 'plg_hubzero_myplugin' . DS . $cssFile;
$plugincss = DS . 'plugins' . DS . 'hubzero' . DS . 'myplugin'
    . DS . 'assets' . DS . 'css' . DS . $cssFile;

if (file_exists(PATH_APP . $templatecss)) {
    Document::addStyleSheet('/app' . $templatecss);
} elseif (file_exists(PATH_CORE . $templatecss)) {
    Document::addStyleSheet('/core' . $templatecss);
} else {
    Document::addStyleSheet('/core' . $plugincss);
}
```

### Naming Convention

| File | Mode | Contains |
|------|------|----------|
| `myplugin.css` | Classic | Legacy styles (fixed colors, floats, px units) |
| `myplugin.blade.css` | daisyUI | Semantic tokens (`--color-primary`, `--color-border`), flexbox |

The `.blade.css` suffix signals "daisyUI-aware" — it's not a Blade template,
just a CSS file that uses daisyUI's design tokens.

### Template Override

Both variants can be overridden by templates. A template that wants to
customize the autocompleter's daisyUI styles places a file at:

```
app/templates/hubzero/html/plg_hubzero_autocompleter/autocompleter.blade.css
```

The lookup order is:
1. `app/templates/{name}/html/plg_{group}_{element}/{cssFile}`
2. `core/templates/{name}/html/plg_{group}_{element}/{cssFile}`
3. `core/plugins/{group}/{element}/assets/css/{cssFile}`

This is the same override pattern used for component view templates.

### Example: Autocompleter Plugin

The autocompleter plugin (`plg_hubzero_autocompleter`) demonstrates this
pattern:

**Classic mode** (`autocompleter.css`):
- Float-based token layout
- Fixed colors (`#FFF6D3`, `#DEE7F8`, `#deecd4`)
- Different colors per token type (tags=yellow, groups=green, members=blue)
- Hardcoded fonts (Verdana 11px/12px)

**daisyUI mode** (`autocompleter.blade.css`):
- Flexbox token layout with `gap`
- Semantic tokens (`var(--color-primary)`, `var(--accent-primary)`)
- Consistent styling across token types
- Relative units, `border-radius: 2rem` pill shape
- Focus-within ring on the list container

**Token rendering** (`autocompleter.js`):
```js
// Global detection (outside IIFE)
var IS_DAISYUI = document.documentElement.getAttribute('data-css-framework') === 'daisyui';

// Inside tokenFormatter callback
tokenFormatter: function(item) {
    if (IS_DAISYUI) {
        return '<li class="badge badge-soft badge-primary gap-1"><p>'
            + item[this.propertyToSearch] + '</p></li>';
    }
    return "<li><p>" + item[this.propertyToSearch] + "</p></li>";
},
```

In daisyUI mode, tokens render as `badge badge-soft badge-primary` — matching
the tag cloud display (which uses `badge badge-soft badge-primary` in
`_cloud.blade.php`). This keeps tags visually consistent between edit
(autocompleter tokens) and display (tag cloud badges).

---

## Design Decisions

### Why data attributes on `<html>`, not `<meta>` tags?

Behaviors use `<meta>` tags because they are boolean signals (present or absent)
injected dynamically by PHP code that runs after the `<html>` tag is already
rendered. Framework detection is different:

- It's set once per page, at render time, in the page shell
- It needs to be readable before `DOMContentLoaded` (for CSS injection)
- `document.documentElement.getAttribute()` is synchronous and available
  immediately — no DOM query needed
- Keeps framework identity with the root element, alongside `data-theme`

### Why not put plugin CSS in the template?

Plugin-specific styles belong with the plugin, not the template:

- **Encapsulation**: The plugin knows its own DOM structure and class names
- **Override support**: Templates can still override via the `html/` directory
- **No leakage**: Legacy CSS never loads in daisyUI mode (and vice versa)
- **Independent deployment**: Plugin CSS changes don't require template rebuilds

### Why a single JS file with branching, not two files?

A single `autocompleter.js` with `IS_DAISYUI` branching is simpler than
maintaining `autocompleter.js` + `autocompleter.blade.js`:

- One file to load, cache, and maintain
- The difference is typically one `if` branch in a formatter callback
- No risk of the two files diverging in behavior
- PHP doesn't need to conditionally select the JS file
