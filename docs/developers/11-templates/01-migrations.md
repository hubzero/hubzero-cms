<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/migrations
source-id: 3504
modified: 2019-03-13
-->
# Migrations

Copying a template into place is not enough. The CMS only knows about a
template that has a row in `#__extensions` and a style in `#__template_styles`.
A migration puts those rows there.

Every extension type can carry a `migrations` directory, and templates are no
exception:

```
app/templates/mytemplate/
    css/
    html/
    img/
    js/
    language/
    migrations/
        Migration20260901102219TplMytemplate.php
    component.php
    error.php
    index.php
    templateDetails.xml
    template_thumbnail.png
    favicon.ico
```

[`Hubzero\Content\Migration`](../../../core/libraries/Hubzero/Content/Migration.php)
scans every directory under `core/templates` and `app/templates` for a
`migrations` subdirectory, alongside the component, module and plugin trees.

> **Note:** See the [migrations chapter](../06-database.md#migrations) for
> the file naming rules, the `up()`/`down()` contract and how to run them.

## Registering the template

A template usually needs one migration, and it usually does one thing: call
`addTemplateEntry()`. Here is the one `kimera` ships:

<!--include: core/templates/kimera/migrations/Migration20170831000000TplKimera.php-->

`up()` registers the template; `down()` removes it again. That is the whole
job. The macro writes the `#__extensions` row and, if `#__template_styles`
exists, the style row too.

## addTemplateEntry

```php
$this->addTemplateEntry($element, $name, $client, $enabled, $home, $styles, $protected);
```

| Argument | Default | Meaning |
|---|---|---|
| `$element` | — | The template's directory name. A leading `tpl_` is stripped. |
| `$name` | `null` | The style title shown in the admin. Defaults to a title-cased `$element`. |
| `$client` | `1` | `0` for the site, `1` for the administrator. |
| `$enabled` | `1` | Whether the extension row is enabled. |
| `$home` | `0` | Whether this becomes the client's default style. Setting it clears `home` on every other style for that client. |
| `$styles` | `null` | An array of style parameters, JSON-encoded into the style row. |
| `$protected` | `0` | Intended to mark the template as a core one. |

> **Warning:** The `$protected` argument is accepted but not used.
> [`AddTemplateEntry`](../../../core/libraries/Hubzero/Content/Migration/Macros/AddTemplateEntry.php)
> writes a literal `'protected' => 0` into the `#__extensions` row, so the
> shipped templates that pass `1` are registered unprotected. Templates the
> CMS treats as protected are read from `core/templates`; unprotected ones
> from `app/templates`, so this matters for a template that lives in `core/`.

Note the default client is the **administrator**. A site template must pass
`0` explicitly:

```php
// Site template, enabled, not the default style
$this->addTemplateEntry('mytemplate', 'My Template', 0, 1, 0);
```

`installTemplateEntry()` is the same macro with `$enabled` and `$home` both
forced to `1`:

```php
$this->installTemplateEntry($element, $name, $client, $styles, $protected);
```

## deleteTemplateEntry

```php
$this->deleteTemplateEntry($element, $client);
```

`$client` defaults to `1`, so a site template's `down()` must pass `0`. The
macro removes the extension row and every style row for that template, then —
if it has just removed the client's default style — promotes the most recently
added remaining style to `home` so the client is never left without one.

## Other macros

The macros live in
[`core/libraries/Hubzero/Content/Migration/Macros`](../../../core/libraries/Hubzero/Content/Migration/Macros)
and are reached through `__call()` on the migration base class, so any file in
that directory is callable as `$this->methodName()`.

> **Warning:** `Macros/EnableTemplate.php` declares a class named
> `EnableComponent`, not `EnableTemplate`, so `$this->enableTemplate()` throws
> `BadMethodCallException`. Enable a template with the `$enabled` argument to
> `addTemplateEntry()` instead.
