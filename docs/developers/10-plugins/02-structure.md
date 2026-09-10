<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/plugins/structure
-->
# Structure

A plugin is identified by two names: the group it belongs to and its own. Both
are directory names, and together they determine the class name, the language
file name, the asset paths, and the `folder` and `element` columns of its row
in `#__extensions`.

## Directory layout

Plugins are never at the top level of `plugins`; they are always one level
down, inside a group directory:

```
app/plugins/
    {group}/
        {name}/
            {name}.php
            {name}.xml
```

So a `system` plugin called `example`:

```
app/plugins/system/example/
    assets/css/example.css
    language/en-GB/en-GB.plg_system_example.ini
    language/en-GB/en-GB.plg_system_example.sys.ini
    migrations/Migration20260101000000PlgSystemExample.php
    views/display/tmpl/default.php
    composer.json
    example.php
    example.xml
    index.html
```

Only `example.php` and `example.xml` are required. Because the group is part
of the path, the same plugin name can be reused in different groups —
`core/plugins/content/formatwiki` and `core/plugins/wiki/…` coexist without
conflict.

`Hubzero\Plugin\Loader` strips anything outside letters, digits, `_`, `.`,
and `-` from both the group and the name before building a class name or a
path, so those are the characters available. Stick to lowercase alphanumerics
and underscores unless you have a reason not to.

## Where the loader looks

[`Hubzero\Plugin\Loader::path()`](../../../core/libraries/Hubzero/Plugin/Loader.php)
resolves a group and name to a directory by checking `PATH_APP` first and
`PATH_CORE` second:

1. `app/plugins/{group}/{name}`
2. `core/plugins/{group}/{name}`

The entry file is `{name}.php` inside whichever directory exists. A hub
overrides a shipped plugin by copying the whole directory into `app/plugins`;
the extension row does not change, because it records only the group and the
element.

## The class

The entry file must define a class the loader can find. Two names are
accepted, checked in this order:

| Form | Example |
|---|---|
| `plg{Group}{Name}` | `plgMembersBlog`, `plgContentFormatwiki`, `plgGroupsForum` |
| `Plugins\{Group}\{Name}` | `Plugins\Members\Blog` |

Both are `ucfirst()` of the group and of the name — only the first letter is
capitalised, so `formatwiki` gives `plgContentFormatwiki`, not
`plgContentFormatWiki`. Every plugin shipped in `core/plugins` uses the first
form, in the global namespace. The namespaced form is supported by the loader
but unused; supporting classes under a plugin — models, helpers, macros — do
use `Plugins\Group\Name\…` namespaces, and are loaded by the framework's
class loader.

> **Note:** The `editors-xtd` group is the exception that proves the rule. Its
> classes are named `plgButtonImage`, `plgButtonReadmore`, and so on, which
> matches neither form, so `Hubzero\Plugin\Loader` cannot construct them.
> `Hubzero\Html\Editor` imports that group with autocreation switched off
> (`Plugin::import('editors-xtd', $name, false)`) and instantiates the class
> itself. Do not copy the pattern in a new plugin.

```php
<?php
// No direct access
defined('_HZEXEC_') or die();

class plgSystemExample extends \Hubzero\Plugin\Plugin
{
    public function onAfterInitialise()
    {
        // ...
    }
}
```

> **Note:** Because core plugin classes are in the global namespace, they call
> `Lang::txt()` and `Route::url()` with no imports. A *namespaced* plugin file
> must `use` each facade it calls, or the unqualified name resolves inside the
> plugin's own namespace and fatals. The same applies to any namespaced helper
> class you add beside the plugin. See
> [Facades](../03-foundation/06-facades.md).

## The entry guard

Plugin files begin with

```php
defined('_HZEXEC_') or die();
```

before any executable code. Plugin directories are inside the document root on
a default install, so this line is the only thing preventing a direct HTTP
request from running the file outside the application. See
[Constants](../03-foundation/02-constants.md).

## Subdirectories

Everything else is optional and follows fixed names, because the framework
looks for them by convention: `views/{name}/tmpl/{layout}.php` for
[views](05-views.md), `assets/{css,js,img}` for [assets](06-assets.md),
`language/{tag}` for [languages](04-languages.md), `migrations` for
[migrations](01-migrations.md). Models and helpers have no enforced location;
`views` and `helpers` beside the entry file is the common arrangement.
