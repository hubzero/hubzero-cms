<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/layouts
source-id: 3509
-->
# Page layouts

A layout is the outermost HTML of a page. The component has already produced
its markup by the time a layout runs; the layout wraps it, adds the masthead
and the footer, and marks the places where modules go. Layout files sit at the
top level of a template directory — never in a subdirectory — and the CMS
picks one per request by name.

## The layout files

| File | Rendered when |
|---|---|
| `index.php` | The default. Used whenever nothing else is asked for. **Required**: a template without it is ignored and the `system` template runs instead. |
| `component.php` | `?tmpl=component` — the convention for modal, popup, and print output. Most components link to it. |
| `offline.php` | The site is offline and the visitor lacks `core.login.offline`. |
| `error.php` | An uncaught exception reaches the error handler. |
| `cpanel.php` | The administrator control panel; `com_cpanel` sets `tmpl=cpanel`. |
| `login.php` | The administrator login screen; `com_login` sets `tmpl=login`. |
| `help.php` | The help viewer; `com_help` sets `tmpl=help`. |
| `group.php` | A super group page, and the super group error page. |
| `email.php` | The wrapper for HTML mail, through `Hubzero\Mail\Template`. |

Any other value of `tmpl` names a layout the same way, so `?tmpl=print` looks
for `print.php`. The name is filtered to `A-Z0-9_.-` before it is used.

Only `index.php` is required. Everything else falls back, file by file, to
`core/templates/system` — so a template that ships no `error.php` still gets a
usable error page, styled by the system template rather than by yours.

This is what the shipped templates provide:

| | index | component | error | offline | cpanel | login | group | help | email |
|---|---|---|---|---|---|---|---|---|---|
| `kimera` (site) | ✓ | ✓ | ✓ | ✓ | | | | | |
| `lucent` (site) | ✓ | ✓ | ✓ | | | | | | |
| `welcome` (site) | ✓ | | | | | | | | |
| `kameleon` (administrator) | ✓ | ✓ | ✓ | | ✓ | ✓ | | | |
| `system` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |

`core/templates/system/index.php` is one line: it includes `component.php`.
The system template is a safety net, not a design.

## How the CMS picks a layout

[`Bootstrap\Site\Providers\DocumentServiceProvider`](../../../core/bootstrap/Site/Providers/DocumentServiceProvider.php)
runs after the component has produced its output. It reads `tmpl` from the
request, defaults it to `index`, forces `offline` when the site is offline,
and hands
[`Hubzero\Document\Type\Html`](../../../core/libraries/Hubzero/Document/Type/Html.php)
four things: the template name, the file name, the templates directory, and
the template's saved parameters. The document includes that file, captures
the output, replaces the `jdoc` tags in it, and returns the page.

The templates directory is `app/templates` when
`app/templates/{name}` exists and `core/templates` otherwise, so a hub can
shadow a shipped template by putting a directory of the same name under
`app/templates`. Everything under `app/` is site-local; nothing there is part
of the distribution.

> **Note:** Error pages take a different route.
> [`Hubzero\Document\Type\Error`](../../../core/libraries/Hubzero/Document/Type/Error.php)
> includes `error.php` and returns its output **without parsing it**. A
> `jdoc:include` tag in `error.php` is printed literally. Error layouts get no
> modules, no message queue, and no `head` block; write the `<title>` and the
> stylesheet link yourself, as `kimera/error.php` does.

## Inside a layout

A layout is included by the document object, so `$this` is that document. It
runs in the global namespace, which is why the facades work with no `use`
statements.

| On `$this` | What it gives you |
|---|---|
| `$this->template` | The active template's name |
| `$this->baseurl` | URL prefix for the templates directory, `/app` or `/core` |
| `$this->params` | `Hubzero\Config\Registry` of the template style's saved parameters |
| `$this->language`, `$this->direction` | Language tag and `ltr`/`rtl`, for the `<html>` element |
| `$this->getTitle()`, `$this->setTitle()` | The document title, before `head` renders it |
| `$this->addStyleSheet()`, `$this->addScript()` | Queue a file into the `head` block |
| `$this->addStyleDeclaration()`, `$this->addScriptDeclaration()` | Queue inline CSS or JS |
| `$this->countModules($expr)` | How many modules a position holds |
| `$this->getBuffer($type, $name)` | Rendered output for a `jdoc` type, before it is substituted |

Every layout opens with the entry guard:

```php
defined('_HZEXEC_') or die();
```

`core/templates/system/component.php` is the smallest complete layout in the
repository:

<!--include: core/templates/system/component.php-->

`countModules()` takes a boolean expression over position names, which is how
a layout collapses empty columns:

```php
<?php if ($this->countModules('left or right')) : ?>
```

The expression is evaluated with `eval()`, so keep it to position names and
the operators `and`, `or`, `xor`, arithmetic, and comparisons.

## jdoc includes

`jdoc:include` marks a hole in the layout. The document finds every tag, asks
a renderer for the corresponding markup, and substitutes it in.

| `type` | Renders |
|---|---|
| `component` | The component's output. A layout without this shows nothing. |
| `head` | `<base>`, meta tags, `<title>`, `<link>`s, and `<script>`s queued by anything on the page. |
| `message` | The queued system messages, as `<p class="passed">`, `.warning`, `.error`, `.info`. |
| `modules` | Every published module in the position named by `name`. |
| `module` | One module instance, looked up by name. |

Module and modules tags are rendered before every other type, so a module may
push a stylesheet or a script and still have it appear in the `head` block.

The tag is matched by a regular expression, not by an XML parser, and the
expression is strict:

- `type` must be the first attribute, and its value must be in double quotes;
- a single space must follow the closing quote of `type`;
- the tag must be self-closing.

`<jdoc:include type="component" />` works. `<jdoc:include type="component"/>`
does not — it is left in the page as literal text. If a hole in your layout
renders as nothing at all, check the spacing first.

## Module positions

A position is just a string. A layout asks for one by name, and the loader
returns whatever the administrator has assigned to it. Kimera's content region
shows the usual shape — an optional left column, the component, an optional
right column, and a footer:

<!--include: core/templates/kimera/index.php:164-206-->

The `<positions>` block in `templateDetails.xml` does **not** restrict
anything. It is read only by `com_modules` when it builds the position picker
on the module edit screen, so a position that is used in `index.php` but not
declared still renders; it simply does not appear in that list. Kimera is an
example of the drift: it uses `breadcrumbs` and `endpage` without declaring
them, and declares `banner` and `introblock` without using them.

| Template | Positions used in its layouts |
|---|---|
| `kimera` | `helppane`, `notices`, `search`, `user3`, `breadcrumbs`, `welcome`, `left`, `right`, `footer`, `endpage` |
| `lucent` | `html-head`, `notices`, `helppane`, `user3`, `left`, `right`, `search`, `endpage` |
| `kameleon` | `notices`, `menu`, `title`, `toolbar`, `submenu` |
| `system` | `notices`, `helppane`, `endpage` (in `group.php` only) |
| `welcome` | none |

See [Modules: loading](../08-modules/09-loading.md) for what the loader does
with a position, and [Overrides](09-overrides.md) for module chrome — the
`style` attribute on a `jdoc` tag and the `modChrome_*` functions a template
can add.

## The head block

`<jdoc:include type="head" />` emits everything the request has queued: the
`<base>` tag, meta tags, the description and generator, `<title>`, link
relations, stylesheets, style declarations, scripts, script declarations, and
the `strings` object that `Lang::script()` fills for JavaScript translation.

Order matters, and the head block is late in the sequence, so anything a
layout must load *before* extension assets goes in the markup above it. Kimera
links its own stylesheet first and lets the head block follow:

<!--include: core/templates/kimera/index.php:50-66-->

Anything the layout adds with `$this->addStyleSheet()` or `$this->addScript()`
instead appears *inside* the head block, after whatever the component queued.
Kameleon does that for its own `index.css`, which is why its rules need enough
specificity to win.

## Error layouts

`error.php` is handed an `Error` document with two extra members:

- `$this->error` — the `Exception`. `getCode()` and `getMessage()` are what
  you want; the code is not guaranteed to be an HTTP status, so test it before
  displaying it, as `kimera/error.php` does.
- `$this->debug` — true when the site's **Debug System** setting is on.
  `$this->renderBacktrace()` returns the stack trace as a table.

Never show `getMessage()` unless `$this->debug` is true. Kimera maps 403, 404
and everything else onto three translated strings and shows the raw message
only when debugging.

> **Warning:** Debugging exposes file paths, queries, and stack traces to
> every visitor. Leave it off in production.

## Parameters

A template declares parameters in the `<config>` block of its
`templateDetails.xml`, and an administrator sets them per template style.
`$this->params` reads them back. Kimera uses them for a header variant, a
background pattern, and two accent colours, then builds a style declaration
from the result:

```php
$bground = $this->params->get('backgroundImage', $this->params->get('background', 'delauney'));
$styles  = include_once __DIR__ . '/css/theme.php';
if ($styles)
{
    $this->addStyleDeclaration($styles);
}
```

Kameleon does the same with `css/themes/custom.php` for its custom colour
theme. Both files return a CSS string rather than printing one.

> **Note:** Kimera and Kameleon both declare their parameters with
> `<config><fields name="params">`. Lucent's manifest still uses the older
> `<install>` root element with an empty `<params>` block, so it has no
> settable parameters at all. Follow Kimera.

Because parameters belong to a template *style*, one template can be installed
several times with different settings, and a menu item can select a style
through its `template_style_id`. `?templateStyle={id}` overrides that for one
request.
