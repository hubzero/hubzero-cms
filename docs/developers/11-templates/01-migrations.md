<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/migrations
source-id: 3504
modified: 2019-03-13
-->
# Migrations

Copying a template into place is not enough. The CMS only knows about a
template that has a row in `#__extensions` and a style in `#__template_styles`.
A migration puts those rows there.

## Why this exists

Nothing scans the filesystem for templates. The administrator's template list
is a query against `#__extensions`, and
[`Hubzero\Template\Loader`](../../../core/libraries/Hubzero/Template/Loader.php)
resolves a request to a directory by joining `#__template_styles` to that same
table. A directory with no rows behind it is invisible: it does not appear in
the administrator, it cannot be picked by a menu item, and `?templateStyle=`
cannot reach it.

The migration is how those rows get written on every hub that installs your
template, in a form that can be reversed. Writing them by hand with SQL works
once, on your own machine, and is forgotten the first time the template is
deployed anywhere else.

Every extension type can carry a `migrations` directory, and templates are no
exception:

```
app/templates/northgate/
    css/
    html/
    img/
    js/
    language/
    migrations/
        Migration20260910102219TplNorthgate.php
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

## The smallest one that works

A template usually needs one migration, and it usually does one thing: call
`addTemplateEntry()`. This is the whole of `northgate`'s:

```php
<?php
/**
 * Migration script for the Northgate site template
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

class Migration20260910102219TplNorthgate extends Base
{
	public function up()
	{
		// element, title, client (0 = site), enabled, home
		$this->addTemplateEntry('northgate', 'Northgate University', 0, 1, 0);
	}

	public function down()
	{
		$this->deleteTemplateEntry('northgate', 0);
	}
}
```

Run it:

```bash
php core/bin/muse migration -f -e=tpl_northgate
```

The template now appears under **Extensions → Templates** and can be made the
site's default style. Compare the shipped `kimera` one, which differs only in
its arguments:

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

Note the default client is the **administrator**. A site template must pass
`0` explicitly. This is the mistake to expect:

```php
// Wrong: registers a site template as an administrator style
$this->addTemplateEntry('northgate', 'Northgate University');
```

It fails quietly. The rows are written, the migration reports success, and the
template never appears in the site template list — because
`getTemplate()` filters on `client_id` in both tables. Filter the administrator
template list by **Administrator** and there it is, alongside `kameleon`.

`installTemplateEntry()` is the same macro with `$enabled` and `$home` both
forced to `1`:

```php
$this->installTemplateEntry($element, $name, $client, $styles, $protected);
```

Use it only when the template genuinely should take over the hub the moment it
is installed. `northgate` uses `addTemplateEntry` with `$home = 0` so that
someone chooses the switch-over.

> **Warning:** The `$protected` argument is accepted but not used.
> [`AddTemplateEntry`](../../../core/libraries/Hubzero/Content/Migration/Macros/AddTemplateEntry.php)
> writes a literal `'protected' => 0` into the `#__extensions` row, so the
> shipped templates that pass `1` are registered unprotected. Templates the
> CMS treats as protected are read from `core/templates`; unprotected ones
> from `app/templates`, so this matters for a template that lives in `core/`.

## deleteTemplateEntry

```php
$this->deleteTemplateEntry($element, $client);
```

`$client` defaults to `1`, so a site template's `down()` must pass `0` — the
same trap in reverse, and this one leaves the rows behind instead of removing
them. The macro removes the extension row and every style row for that
template, then — if it has just removed the client's default style — promotes
the most recently added remaining style to `home` so the client is never left
without one.

## When the template does not appear

Four things go wrong here, in rough order of frequency.

**The migration never ran.** `muse migration` without `-f` lists what is
pending and changes nothing. Run it with `-f`.

**The file name and the class name disagree.** The runner takes the class name
from the file name and skips the file with a warning —
`Migration20260910102219TplNorthgate does not have a class of the same name` —
rather than failing. A template copied from `kimera` keeps
`Migration20170831000000TplKimera.php` and its class, so the file matches
itself and runs *kimera's* registration a second time, or fatals on the
duplicate class name if the shipped `kimera` is still installed. Rename both.

**The list is cached.** `Loader::getTemplate()` caches the whole template list
per client under `com_templates.templates{client_id}` for the hub's `cachetime`
— fifteen minutes by default. A freshly registered style can take that long to
become selectable. Clear the cache rather than re-running the migration.

**The row exists and the directory does not.** These are independent. The
install data ships `#__extensions` rows for `hubbasic`, `hubbasic2012`,
`hubbasic2013` and `hubbasicadmin`, none of which are directories in this
repository, and they are listed in the administrator all the same. A style
whose directory has no `index.php` silently falls back to `core/templates/system`
at render time. So a name in the template list is not evidence that a template
is there.

## Other macros

The macros live in
[`core/libraries/Hubzero/Content/Migration/Macros`](../../../core/libraries/Hubzero/Content/Migration/Macros)
and are reached through `__call()` on the migration base class, so any file in
that directory is callable as `$this->methodName()`.

> **Warning:** `Macros/EnableTemplate.php` declares a class named
> `EnableComponent`, not `EnableTemplate`, so `$this->enableTemplate()` throws
> `BadMethodCallException`. Enable a template with the `$enabled` argument to
> `addTemplateEntry()` instead.
