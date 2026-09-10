<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/modules/structure
-->
# Structure

Everything about a module hangs off its directory name. Pick the name once —
`mod_upcoming_bookings` — and the entry file, the manifest, the language
files, the CSS, the extension row, and the namespace all follow from it.
Getting one of them out of step is the usual reason a module that looks
complete renders nothing, and none of the mismatches raise an error.

## Directory layout

```
app/modules/mod_upcoming_bookings/
    assets/
        css/mod_upcoming_bookings.css
        img/
        js/mod_upcoming_bookings.js
    language/en-GB/
        en-GB.mod_upcoming_bookings.ini
        en-GB.mod_upcoming_bookings.sys.ini
    migrations/
        Migration20260101000000ModUpcomingBookings.php
    tmpl/
        default.php
        index.html
    composer.json
    helper.php
    index.html
    mod_upcoming_bookings.php
    mod_upcoming_bookings.xml
```

Only `mod_upcoming_bookings.php` and `mod_upcoming_bookings.xml` are required.
In practice a module that renders anything worth reading also has `helper.php`
and `tmpl/default.php`.

| Path | Purpose |
|---|---|
| `mod_upcoming_bookings.php` | The entry point. Included by the loader with `$params` and `$module` in scope. |
| `mod_upcoming_bookings.xml` | The manifest: name, client, files, and the parameter fields shown in the Module Manager. |
| `helper.php` | The module class, extending `Hubzero\Module\Module`. |
| `tmpl/` | Layouts. `default.php` is used unless a `layout` parameter says otherwise. |
| `assets/` | CSS, JavaScript, and images, split by type. |
| `language/` | Translation files, one directory per language tag. |
| `migrations/` | The registration migration and any schema changes. |
| `composer.json` | Package metadata for the installer. |
| `index.html` | An empty file in every directory, so a misconfigured web server cannot list it. |

Only the first three produce output. The manifest and the migration are what
make the module *reachable* — see [Migrations](01-migrations.md) — so write
them early rather than last, or you will have a module you cannot place.

## Where the loader looks

[`Hubzero\Module\Loader::path()`](../../../core/libraries/Hubzero/Module/Loader.php)
resolves a module name to its entry file by trying four paths in order and
returning the first that exists:

1. `app/modules/upcoming_bookings/upcoming_bookings.php`
2. `app/modules/mod_upcoming_bookings/mod_upcoming_bookings.php`
3. `core/modules/upcoming_bookings/upcoming_bookings.php`
4. `core/modules/mod_upcoming_bookings/mod_upcoming_bookings.php`

Two things follow from that order. First, `app/` wins over `core/`, so a hub
overrides a shipped module by copying the whole directory into `app/modules`
and editing it there — nothing else has to change. Second, the unprefixed
directory form is accepted, but no core module uses it; stick to the prefixed
form so the directory, the extension element, and the asset paths all agree.

The name is normalised first by `Loader::canonical()`, which strips anything
outside `[A-Za-z0-9_.-]` and adds the `mod_` prefix if it is missing. That is
why `Module::name('login')` and `Module::name('mod_login')` reach the same
module.

`path()` returns an empty string when none of the four exists, and `render()`
skips the include rather than complaining. So a module whose entry file is
misnamed — `upcoming_bookings.php` inside `mod_upcoming_bookings/`, a rename
that missed one file — renders nothing at all. The name in the
`#__extensions` row, the directory name, and the entry filename all have to be
the same string.

## Namespaces and class names

The module class lives under the `Modules` namespace, in StudlyCase, with the
`mod_` prefix dropped:

| Directory | Namespace |
|---|---|
| `mod_login` | `Modules\Login` |
| `mod_mygroups` | `Modules\MyGroups` |
| `mod_articles_archive` | `Modules\ArticleArchive` |
| `mod_upcoming_bookings` | `Modules\UpcomingBookings` |

Note the third row: the namespace tracks the directory loosely, not
mechanically. Nothing enforces it, because the entry file `require_once`s
`helper.php` and instantiates the class directly — no autoloader ever has to
map the class name back to a path. For the same reason the class itself is
conventionally, and freely, named `Helper`. Ninety-nine of the 100 shipped
modules use `Helper`, and so should yours: the loader finds the file, not the class, so a
different class name buys nothing and surprises the next reader.

> **Warning:** The class loader *can* resolve `Modules\…` classes, and it
> resolves them by collapsing the namespace segment to one lowercase word:
> `Modules\UpcomingBookings\Slot` is looked for in
> `modules/mod_upcomingbookings/`, which is not the directory you created.
> Sixteen shipped module directories carry an underscore and have the same
> problem. It costs nothing for `helper.php`, which is `require_once`d by
> path, but any *extra* class you add to an underscored module must be
> required by path too. See
> [Autoloading](../03-foundation/03-autoloading.md#the-per-kind-details-that-catch-people).

> **Warning:** The namespace also makes unqualified class names resolve inside
> `Modules\UpcomingBookings` first. `User::get('id')` in a namespaced helper
> looks for `Modules\UpcomingBookings\User` and fatals with "Class not found".
> Import each facade you use at the top of the file: `use User;`,
> `use Request;`, `use App;`. See [Facades](../03-foundation/06-facades.md).

## Site and administrator modules

A module belongs to one client, declared as `client="site"` or
`client="administrator"` on the manifest's `<extension>` element and as the
`$client` argument (`0` or `1`) to `addModuleEntry()` in the migration. Both live in the same `modules`
directory; the client is a property of the registration, not of the path.
`Loader::all()` filters on `m.client_id`, so an administrator module is
invisible to the site and vice versa.

`mod_upcoming_bookings` is a site module, `client="site"` and `$client = 0`.
A lab manager's version of the same list, in the administrator's control
panel, is a *second* module with its own directory and its own row — one
module cannot serve both clients. If the two would share query code, put that
code on the `Reservation` model where both can call it.

> **Warning:** Set `client="administrator"` in the manifest but leave the
> migration's `$client` at its default of `0` and the module registers as a
> site module. It then never appears in the administrator's Module Manager,
> and the manifest that says otherwise is not consulted.

## Adding more

Nothing stops a module from carrying models, helpers, or a `views` directory,
and a few core modules do. But a module that has grown a controller and
several screens is a component wearing a disguise; see
[Components](../09-components/README.md). `mod_upcoming_bookings` stays small
precisely because everything it knows about a reservation lives in
`com_bookings`: the module queries a model and renders a list.
