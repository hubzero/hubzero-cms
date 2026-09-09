# View Engine & CSS Framework Negotiation

The system uses two independent axes to control how views render:

- **View engine** (`'blade'` | `'php'`) — which template file to load
- **CSS framework** (`'daisyui'` | `'classic'`) — which markup and classes to emit

These resolve independently through a negotiation between the active
**template** (which declares preferences) and the current **component**
(which declares what it accepts).

---

## How It Works

### 1. Templates declare preferences

Templates list their preferred view engines and CSS frameworks as
priority-ordered, comma-separated values in `templateDetails.xml`:

```xml
<config>
  <fields name="params">
    <field name="viewEngines" type="hidden" default="blade,php" />
    <field name="cssFrameworks" type="hidden" default="daisyui,classic" />
  </fields>
</config>
```

The first value is the template's top preference. These values are also
stored in the `#__template_styles.params` JSON column in the database.

Templates that omit these fields default to `"php"` and `"classic"`.

### 2. Controllers declare acceptance

Controller classes declare what they support via properties:

```php
class ArticlesController extends AdminController
{
    protected $viewEngines = ['blade', 'php'];
    protected $cssFrameworks = ['daisyui', 'classic'];
}
```

The base classes set defaults:

| Base class         | `$viewEngines`     | `$cssFrameworks`        |
|--------------------|--------------------|-------------------------|
| `SiteController`   | `['php']`          | `['classic']`           |
| `AdminController`  | `['blade', 'php']` | `['daisyui', 'classic']`|

During `execute()`, `SiteController` reads these properties and calls
`Document::acceptViewEngine()` / `Document::acceptCssFramework()` for
each value before creating the view.

### 3. Negotiation resolves the match

On the first call to `Document::getViewEngine()` or
`Document::getCssFramework()`, the system walks the template's preference
list and picks the first value the component also accepts.

```
Template prefers:  blade, php
Component accepts: blade, php
Result:            blade
```

```
Template prefers:  blade, php
Component accepts: php
Result:            php
```

If no match exists, a `RuntimeException` is thrown with a message naming
the template, the component, and what each supports.

### 4. Resolution is lazy and cached

- `getViewEngine()` / `getCssFramework()` trigger resolution on first call
- The result is cached for the rest of the request
- Calling `acceptViewEngine()` or `acceptCssFramework()` invalidates both
  cached values so the next `get*()` call re-negotiates

---

## API Reference

All methods live on `Hubzero\Document\Base` and are accessible via the
`Document` facade.

### Accept methods (called by controllers)

```php
Document::acceptViewEngine(string $engine): void    // 'blade' or 'php'
Document::acceptCssFramework(string $framework): void // 'daisyui' or 'classic'
```

Multiple calls accumulate. Each call invalidates cached resolutions.

### Resolution methods (called by views, form fields, builders)

```php
Document::getViewEngine(): string      // returns 'blade' or 'php'
Document::getCssFramework(): string    // returns 'daisyui' or 'classic'
```

Both throw `RuntimeException` on negotiation failure.

---

## Usage Patterns

### Checking the CSS framework in a form field or builder

```php
use Hubzero\Facades\Document;

if (Document::getCssFramework() === 'daisyui') {
    // Emit daisyUI markup
} else {
    // Emit classic/Kameleon markup
}
```

Do not wrap these calls in try/catch — negotiation errors should
propagate so misconfigurations are visible.

### Checking the view engine for template file selection

```php
if (Document::getViewEngine() === 'blade') {
    // Load .blade.php template
} else {
    // Load .php template
}
```

This is handled automatically by `View::loadTemplate()` and
`Module\Loader::findTemplate()`. You rarely need to check this directly.

### Task-level override

A controller task can narrow acceptance before display. This is useful
when a specific task only has a legacy template:

```php
public function editTask()
{
    $doc = App::get('document');
    $doc->acceptViewEngine('php');
    $doc->acceptCssFramework('classic');

    $this->view->setLayout('edit')->display();
}
```

Note that `accept*()` calls accumulate rather than replace. If the
controller already declared `['blade', 'php']`, calling
`acceptViewEngine('php')` again is a no-op — both are still accepted.
To truly narrow, set the property before `execute()` runs, or override
in a subclass.

### View fallback behavior

When the negotiated engine is `blade` but a specific view layout has no
`.blade.php` file, `View::loadTemplate()` silently falls back to the
`.php` template. This allows incremental migration — components can
declare blade support while individual views are converted one at a time.

---

## Template Configuration

### hzadmin (modern admin template)

```xml
<field name="viewEngines" type="hidden" default="blade,php" />
<field name="cssFrameworks" type="hidden" default="daisyui,classic" />
```

Prefers blade and daisyUI, falls back to php and classic.

### kameleon (legacy admin template)

```xml
<field name="viewEngines" type="hidden" default="php" />
<field name="cssFrameworks" type="hidden" default="classic" />
```

Only supports php and classic.

---

## Client-Side Detection

The negotiation results are exposed to JavaScript via data attributes on the
`<html>` element. See
[Client-Side Framework Detection](docs/ux/client-side-framework-detection.md)
for the full guide, including the behavior meta tag system and plugin CSS
conditional loading pattern.

```html
<html data-css-framework="daisyui" data-view-engine="blade">
```

```js
var isDaisyUi = document.documentElement.getAttribute('data-css-framework') === 'daisyui';
```

---

## Key Files

| File | Role |
|------|------|
| `core/libraries/Hubzero/Document/Base.php` | Negotiation API |
| `core/libraries/Hubzero/Component/SiteController.php` | Reads controller properties, calls accept methods |
| `core/libraries/Hubzero/Component/AdminController.php` | Sets blade+daisyui defaults for admin |
| `core/libraries/Hubzero/View/View.php` | Uses `getViewEngine()` for template file selection |
| `core/libraries/Hubzero/Module/Loader.php` | Uses `getViewEngine()` for module layout selection |
| `core/libraries/Hubzero/Document/Type/Html.php` | Uses `getViewEngine()` for page shell selection |
