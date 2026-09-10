<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/javascript
source-id: 3511
-->
# JavaScript

What the CMS puts in the page before your template runs, how a template adds
scripts of its own, and how a component, module or plugin pushes a script to
the document.

## What is already there

### jQuery

jQuery is not loaded by the template. The `System - jQuery` plugin loads it, on
`onAfterRoute`, for the site client only:

<!--include: core/plugins/system/jquery/jquery.php:21-50-->

So:

- Nothing is added when `activateSite` is off, or when the request asks for
  `format=pdf`.
- `jqueryui` adds jQuery UI on top; `jqueryfb` adds fancybox through
  `Html::behavior('modal')`; `noconflictSite` appends
  `jquery.noconflict.js`, which releases `$`.
- The **administrator** never goes through this plugin. Admin templates get
  jQuery because something in the request chain calls `Html::behavior()`.

[`Behavior::framework()`](../../../core/libraries/Hubzero/Html/Builder/Behavior.php)
pushes `/core/assets/js/jquery.js` (and `jquery.ui.js` for the extras) to
*position zero* of the document's script list, ahead of anything a component
has already added, with a `?v={filemtime}` cache buster. In the administrator
it also pushes `core.js`.

Both files live in [`core/assets/js`](../../../core/assets/js), which also holds
the several dozen jQuery plugins the components use — fancybox, fullcalendar,
tablesorter, fileupload, datetimepicker and the rest. They are minified for
production; a few ship an `-uncompressed.js` sibling.

### core.js

`core/assets/js/core.js` defines the `Hubzero` global. It is the CMS's own
script layer, and it is the thing to reach for before writing your own:

| | |
|---|---|
| `Hubzero.submitform(task, form)` | Set `form.task` and submit, firing the submit event first |
| `Hubzero.submitbutton(task)` | `submitform` with the default admin form |
| `Hubzero.Lang.load(strings)` / `Hubzero.Lang.txt(key, fallback)` | Translations pushed from PHP |
| `Hubzero.checkAll(box, tag)` | Toggle a grid's checkboxes and update `boxchecked` |
| `Hubzero.renderMessages(messages)` / `removeMessages()` | Write into `#system-message-container` |
| `Hubzero.popupWindow(url, name, w, h, scroll)` | Centred `window.open` |
| `Hubzero.tableOrdering(order, dir, task, form)` | Set the sort fields and submit |
| `Hubzero.listItemTask(id, task)` | Check one row and submit a task for it |
| `Hubzero.saveOrder(rows, task)` | Check every row and submit the reorder task |
| `Hubzero.hasClass` / `addClass` / `removeClass` | Class helpers with an `IE` fallback |

Every one of them is also aliased onto a second, legacy global that older
third-party code expects, so that code keeps working. Write against
`Hubzero`.

`core/assets/js/hubzero.js` is a separate, smaller file with `Hubzero.root()`
and `Hubzero.initApi()`; `Html::behavior('core')` is what adds it.

### The data-attribute hooks

The bottom of `core.js` binds handlers on `DOMContentLoaded` by class name and
reads the details out of `data-` attributes. This is how the admin toolbar,
grids, filters and pagination work, and it is why none of them need inline
script:

| Class | Event | Attributes read |
|---|---|---|
| `.toolbar` with `.toolbar-submit` | click | `data-task`, plus `data-message` with `.toolbar-list` when nothing is checked |
| `.toolbar` with `.toolbar-confirm` | click | `data-confirm`, `data-task`, `data-message` |
| `.toolbar` with `.toolbar-popup` | click | `href`, `data-message`, `data-width` (700), `data-height` (500) |
| `.checkbox-toggle` | click | `.toggle-all` checks the whole grid; anything else updates the count |
| `.filter-submit` | change | — submits the form |
| `.filter-clear` | click | — resets every `.filter` field and submits |
| `.grid-order` | click | `data-order`, `data-direction`, `data-task` |
| `.grid-order-save` | click | `data-rows`, `data-task` |
| `.grid-action` | click | `data-id`, `data-task` |
| `.pagination a` | click | `data-prefix`, `data-start` |

Prefer this pattern in your own code: put the values on the element, bind by
class, keep the script in a file. It survives the
[`System - CSP`](../../../core/plugins/system/csp/csp.php) plugin being switched
on, which inline `<script>` blocks do not — that plugin ships disabled, and its
default `script-src` still allows `'unsafe-inline'`, but neither is something
to rely on.

## Adding a template's own scripts

Put them in `js/` and add them from `index.php`. `kimera` does exactly one
thing:

<!--include: core/templates/kimera/index.php:13-14-->

`$this` in a template layout is the document, so `addScript()`,
`addScriptDeclaration()`, `addStyleSheet()` and `addStyleDeclaration()` are all
available. The `filemtime()` query string is the convention for busting caches
after a deploy; copy it.

What ships in the templates' `js/` directories is thinner than the old
documentation suggested:

| File | What it is |
|---|---|
| `kimera/js/hub.js` | The jQuery `growl` notification plugin, nothing more |
| `kimera/js/html5.js`, `kameleon/js/html5.js` | The HTML5 shiv, loaded in a conditional comment for old IE |
| `lucent/js/hub.js`, `system/js/hub.js` | The legacy `HUB` namespace and its page setup |
| `lucent/js/core.js` | Lucent's own navigation and layout behaviour |
| `kameleon/js/index.js`, `component.js`, `login.js` | One script per admin layout |
| `system/js/group.js` | Super group page behaviour |

> **Note:** The `HUB` namespace — `HUB.Base`, `HUB.Components`, `HUB.Modules`,
> `HUB.Plugins` — still exists in `lucent/js/hub.js` and `system/js/hub.js`,
> and older extension scripts hang off it. New code should use the `Hubzero`
> namespace from `core.js` instead. Do not copy `HUB` into a new template.

## Pushing a script from an extension

### The view helpers

Inside a view layout, `$this->js()` is the short way. It resolves the file,
checks for a template override, and adds it to the document:

```php
<?php
// Push {extension}/assets/js/{name of this component}.js
$this->js()
     ->js('another')            // .js is optional
     ->js('tags', 'com_tags');  // A file belonging to another component
?>
```

The three arguments are the asset name, the extension it belongs to, and — for
plugins only — the plugin element:

```php
$this->js('test', 'members', 'dashboard');  // plg_members_dashboard
```

With no arguments the helper works out the extension from the view: the
component `option` for a component view, `plg_{folder}_{element}` for a plugin
view. `css()` takes the same arguments, and `img()` returns a path rather than
pushing anything.

Both helpers also accept a *declaration*. If the string you pass contains a
`(` or a `;` it is treated as code, not a filename, and goes to
`addScriptDeclaration()`:

```php
$this->js('jQuery(document).ready(function($){ /* … */ });');
```

That is a real feature and a lot of shipped code uses it, but see the note
about data attributes above before reaching for it.

> **Warning:** The same-named `css()`/`js()` methods on controllers, modules
> and plugins come from
> [`Hubzero\Base\Traits\AssetAware`](../../../core/libraries/Hubzero/Base/Traits/AssetAware.php),
> and their **third argument is an attributes array**, not a plugin element:
> `$this->js('thing', 'com_example', ['defer' => true])`. Pass the full
> `plg_{folder}_{element}` as the extension there.

### The static methods

Outside a view, use
[`Hubzero\Document\Assets`](../../../core/libraries/Hubzero/Document/Assets.php):

```php
use Hubzero\Document\Assets;

Assets::addComponentScript('com_example');           // com_example/assets/js/example.js
Assets::addComponentScript('com_example', 'other');  // …/js/other.js
Assets::addModuleScript('mod_example');
Assets::addPluginScript('examples', 'test');         // plugins/examples/test/assets/js/test.js
Assets::addSystemScript('jquery.fancybox');          // core/assets/js/…
```

Each has an `add…Stylesheet` twin, and a fourth `$dir` argument for assets that
are not in `js/`.

### Where the file is looked for

[`Hubzero\Document\Asset\File`](../../../core/libraries/Hubzero/Document/Asset/File.php)
builds a candidate list and takes the first that exists:

```
app/{type}/{extension}/assets/js/{name}.js
app/{type}/{extension}/js/{name}.js
core/{type}/{extension}/assets/js/{name}.js
core/{type}/{extension}/js/{name}.js
```

`{type}` is `components/{com_x}/{client}`, `modules/{mod_x}` or
`plugins/{folder}/{element}`. So `app/` wins over `core/`, and `assets/js/` —
the modern layout — wins over a bare `js/`.

The **template override** is checked separately and beats all of them:

```
{active template}/html/{extension name}/{name}.js
```

That is the same directory as the layout overrides, so
`app/templates/mytemplate/html/com_example/example.js` replaces the component's
script without touching the component. System assets nest one level deeper —
`html/system/js/{name}.js`. See [Output overrides](09-overrides.md).

A name beginning `http`, `//` or `://` is treated as external and passed
through untouched. A name beginning `./` or `/` is resolved from the web root
instead of the extension.

## Behaviors

`Html::behavior()` is the front door for the shared libraries, so you do not
have to know where each one lives or add it twice — every behavior is loaded at
most once per request.

| Call | Adds |
|---|---|
| `Html::behavior('framework')` | jQuery, at the front of the queue |
| `Html::behavior('framework', true)` | jQuery UI as well |
| `Html::behavior('core')` | `hubzero.js` |
| `Html::behavior('modal')` | fancybox, bound to a selector |
| `Html::behavior('tooltip')` | jQuery UI tooltips |
| `Html::behavior('formvalidation')` | `validate.js` |
| `Html::behavior('bootstrap')`, `('htmx')`, `('alpinejs')`, `('inertia')`, `('htmxalpine')` | The named library, at a pinned version |
| `Html::behavior('calendar')`, `('colorpicker')`, `('combobox')`, `('multiselect')`, `('uploader')`, `('switcher')`, `('tree')` | The matching widget |
| `Html::behavior('math')` | MathJax |
| `Html::behavior('chart')` | flot |
| `Html::behavior('caption')`, `('highlighter')`, `('keepalive')`, `('noframes')` | Small page behaviours |

The full list, with each one's arguments, is in
[`Behavior.php`](../../../core/libraries/Hubzero/Html/Builder/Behavior.php).

## Icons without a font

`Html::asset('icon', 'edit')` inlines an SVG from
[`core/assets/icons`](../../../core/assets/icons) — 325 of them — wrapped in
`<span class="icn icn-edit" aria-hidden="true">`. A template overrides any of
them with `html/icons/{symbol}.svg`. No script and no webfont is involved. See
[Fontcons](12-fontcons.md).
