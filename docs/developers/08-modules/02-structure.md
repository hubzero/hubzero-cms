<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/modules/structure
-->
# Structure

Everything about a module hangs off its directory name. Pick the name once —
`mod_example` — and the entry file, the manifest, the language files, the CSS,
the extension row, and the namespace all follow from it.

## Directory layout

```
app/modules/mod_example/
    assets/
        css/mod_example.css
        img/
        js/mod_example.js
    language/en-GB/
        en-GB.mod_example.ini
        en-GB.mod_example.sys.ini
    migrations/
        Migration20260101000000ModExample.php
    tmpl/
        default.php
        index.html
    composer.json
    helper.php
    index.html
    mod_example.php
    mod_example.xml
```

Only `mod_example.php` and `mod_example.xml` are required. In practice a
module that renders anything worth reading also has `helper.php` and
`tmpl/default.php`.

| Path | Purpose |
|---|---|
| `mod_example.php` | The entry point. Included by the loader with `$params` and `$module` in scope. |
| `mod_example.xml` | The manifest: name, client, files, and the parameter fields shown in the Module Manager. |
| `helper.php` | The module class, extending `Hubzero\Module\Module`. |
| `tmpl/` | Layouts. `default.php` is used unless a `layout` parameter says otherwise. |
| `assets/` | CSS, JavaScript, and images, split by type. |
| `language/` | Translation files, one directory per language tag. |
| `migrations/` | The registration migration and any schema changes. |
| `composer.json` | Package metadata for the installer. |
| `index.html` | An empty file in every directory, so a misconfigured web server cannot list it. |

## Where the loader looks

[`Hubzero\Module\Loader::path()`](../../../core/libraries/Hubzero/Module/Loader.php)
resolves a module name to its entry file by trying four paths in order and
returning the first that exists:

1. `app/modules/example/example.php`
2. `app/modules/mod_example/mod_example.php`
3. `core/modules/example/example.php`
4. `core/modules/mod_example/mod_example.php`

Two things follow from that order. First, `app/` wins over `core/`, so a hub
overrides a shipped module by copying the whole directory into `app/modules`
and editing it there — nothing else has to change. Second, the unprefixed
directory form is accepted, but no core module uses it; stick to the prefixed
form so the directory, the extension element, and the asset paths all agree.

The name is normalised first by `Loader::canonical()`, which strips anything
outside `[A-Za-z0-9_.-]` and adds the `mod_` prefix if it is missing. That is
why `Module::name('login')` and `Module::name('mod_login')` reach the same
module.

## Namespaces and class names

The module class lives under the `Modules` namespace, in StudlyCase, with the
`mod_` prefix dropped:

| Directory | Namespace |
|---|---|
| `mod_login` | `Modules\Login` |
| `mod_mygroups` | `Modules\MyGroups` |
| `mod_articles_archive` | `Modules\ArticleArchive` |

Note the third row: the namespace tracks the directory loosely, not
mechanically. Nothing enforces it, because the entry file `require_once`s
`helper.php` and instantiates the class directly — no autoloader ever has to
map the class name back to a path. For the same reason the class itself is
conventionally, and freely, named `Helper`. Every core module uses `Helper`.

> **Warning:** The namespace makes unqualified class names resolve inside
> `Modules\Example` first. `User::get('id')` in a namespaced helper looks for
> `Modules\Example\User` and fatals with "Class not found". Import each facade
> you use at the top of the file: `use User;`, `use Request;`, `use App;`. See
> [Facades](../03-foundation/04-facades.md).

## Site and administrator modules

A module belongs to one client, declared as `client="site"` or
`client="administrator"` on the manifest's `<extension>` element and as the
`$client` argument (`0` or `1`) to `addModuleEntry()` in the migration. Both live in the same `modules`
directory; the client is a property of the registration, not of the path.
`Loader::all()` filters on `m.client_id`, so an administrator module is
invisible to the site and vice versa.

## Adding more

Nothing stops a module from carrying models, helpers, or a `views` directory,
and a few core modules do. But a module that has grown a controller and
several screens is a component wearing a disguise; see
[Components](../09-components/README.md).
