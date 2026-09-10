<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/extensions/extreqs
source-id: 3467
imported: 2026-09-09
source-state: unpublished
-->
# Requirements

What an extension has to have before a hub will load it. Getting the code
onto a hub is a separate question, covered in
[Deploying extensions](04-deployext.md).

## The platform

Hubzero 2.4 requires **PHP 8.2 or later**
([`Hubzero\System\Requirements`](../../../core/libraries/Hubzero/System/Requirements.php)
holds the minimum and the list of required PHP extensions) and MariaDB or
MySQL. Write extensions against that, not against the PHP 5.4 constraint
that most shipped `composer.json` files still carry — those constraints are
stale, and nothing enforces them.

## Naming

The name is not decoration. It determines the directory, the entry file, the
manifest, the language files, the class namespace and the `#__extensions`
row, and all of them have to agree.

| Kind | Directory | Element name |
|---|---|---|
| Component | `components/com_example` | `com_example` |
| Module | `modules/mod_example` | `mod_example` |
| Plugin | `plugins/{group}/example` | `example`, with `folder = '{group}'` |
| Template | `templates/example` | `example` |

Use lowercase and, where you need a separator, an underscore. A plugin's
package directory is conventionally `plg_{group}_{name}`, but what is
installed is `plugins/{group}/{name}` — the group is where the package
lands, not a directory inside it.

## The layout

Each kind has its own chapter on structure —
[components](../09-components/02-structure.md),
[modules](../08-modules/02-structure.md), [plugins](../10-plugins/02-structure.md),
[templates](../11-templates/03-structure.md) — but the shape is the same
everywhere: an entry point, an XML manifest named after the extension, a
`composer.json`, language files under `language/{tag}/`, and a `migrations/`
directory.

A component is the one with more than one face:

```
com_example/
    admin/          the administrator interface
        example.php
        controllers/  views/  language/en-GB/  config.xml
    api/            the REST controllers
    site/           what hub visitors see
        example.php
        router.php  controllers/  views/  language/en-GB/
    config/config.xml
    migrations/
    models/
    composer.json
    example.xml
```

### The entry point

`Hubzero\Component\Loader` looks, in order, for a
`Components\Example\Site\Bootstrap` class, then for
`site/example.php`, then for a React application under `site/assets/react/`,
and only then falls back to its default dispatcher. Most components ship
`site/example.php` and `admin/example.php`, four lines that hand off to the
controller:

<!--include: core/components/com_blog/site/blog.php:1-20-->

A module's entry point is `mod_example.php`, and on this release it is a stub
that requires `helper.php`; `Hubzero\Module\Loader` `include`s the file.
A plugin's entry point is `example.php`, holding a class whose public methods
are named for the events it answers.

### The XML manifest

Every extension carries an XML manifest named after it — `example.xml` — and
the manifest is not listed in its own `<files>` block. The CMS reads the
display name, the description and the parameter form from it, and caches it
in the extension row's `manifest_cache` column. **Refresh Cache** in the
[Extension Manager](../../managers/10-extensions/04-extension-manager.md) re-reads
it after an edit.

<!--include: core/plugins/system/debug/debug.xml:1-17-->

| Element | Meaning |
|---|---|
| `type` | `component`, `module`, `plugin` or `template`. Required. |
| `group` | The plugin group. Plugins only, and required for them. |
| `client` | `site` or `administrator`. Modules and templates. |
| `version` | The manifest format version, not the extension's. |
| `<name>` | Shown in the administrator's lists. A language key or a readable string. |
| `<description>` | A language key, resolved from the `.sys.ini` file. |
| `<files>` | What to install. `<folder>` for a whole directory; the `plugin=` or `module=` attribute on a `<filename>` marks the entry point. |
| `<languages>` | Translation files. |
| `<config>` | The parameter form. See [Parameters](02-parameters.md). |
| `<positions>` | Template positions. Templates only. |

> **Note:** The `<files>` list matters only to a packager that copies files
> one at a time. Nothing in this release installs an extension that way — the
> code arrives as a whole directory, by `git` or by hand — so a `<files>`
> block that has drifted from what is on disk breaks nothing. Do not read it
> as a description of the extension.

### The Composer manifest

```json
{
	"name": "myorg/com_example",
	"description": "Example component for the Hubzero CMS",
	"type": "hubzero-component",
	"keywords": ["hubzero"],
	"license": "MIT",
	"require": {
		"php": "^8.2"
	}
}
```

The recognised types are `hubzero-component`, `hubzero-module`,
`hubzero-plugin` and `hubzero-template`. A plugin must also declare where it
installs, because its group cannot be recovered from a name like
`plg_system_example`:

```json
	"extra": {
		"install-directory": "/plugins/system/example/"
	}
```

### Language files

One directory per language tag, one file named for the tag and the
extension:

```
language/en-GB/en-GB.mod_example.ini
language/en-GB/en-GB.mod_example.sys.ini
```

See [Languages](03-languages.md).

### The migration

The row in `#__extensions` is what makes an extension exist as far as the
platform is concerned, and a
[migration](../06-database.md#migrations) is how an extension creates it. The
migration also creates the extension's tables and drops them again on the
way down.

```php
use Hubzero\Content\Migration\Base;

defined('_HZEXEC_') or die();

class Migration20260101000000ComExample extends Base
{
	public function up()
	{
		$this->addComponentEntry('example');
	}

	public function down()
	{
		$this->deleteComponentEntry('example');
	}
}
```

`addComponentEntry` is a *macro* — one of the helpers `Base` resolves out of
[`Hubzero\Content\Migration\Macros`](../../../core/libraries/Hubzero/Content/Migration/Macros).
There is one per kind — `addComponentEntry`, `addModuleEntry`,
`addPluginEntry`, `addTemplateEntry` — with a matching `delete...` for each,
and `enable...` / `disable...` alongside. Use them rather than writing the
`INSERT` yourself: they set the columns the loaders read, and re-running them
is safe.

### `index.html`

Every directory carries an empty `index.html`, so that a misconfigured web
server cannot list it. Copy the ones already in the tree.

## The entry guard

Any file that produces output — a view template, an entry point, anything
outside a class — opens with:

```php
defined('_HZEXEC_') or die();
```

`_HZEXEC_` is defined by [`core/bootstrap/app.php`](../../../core/bootstrap/app.php)
and by nothing else, so the guard stops the file dead if it is requested
directly over HTTP. A file that only declares a namespaced class does not
need it. See [Constants](../03-foundation/02-constants.md).

## Facade imports

The platform's short names — `Route`, `Lang`, `User`, `Config`, `Request`,
`Component` — are aliases in the **root** namespace. An extension class is
namespaced, so an unqualified `Route::url()` inside it resolves to
`Components\Example\Site\Controllers\Route` and fatals when that line runs.
The file parses; nothing fails until the branch executes.

Import every facade you use:

```php
namespace Components\Example\Site\Controllers;

use Hubzero\Component\SiteController;
use Request;
use Route;
use Lang;
```

`php tools/lint/missing-facade-imports.php` finds the ones you missed, and
runs on every push. See [Facades](../03-foundation/04-facades.md).
