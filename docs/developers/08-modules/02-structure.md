<!--
status: rewritten
reviewed-against: per-cs-3.1 @ 6196e778df
reviewed: 2026-11-10
-->
# Structure

Everything about a module hangs off its directory name. Pick the name once —
`mod_upcoming_bookings` — and the entry file, the manifest, the language
files, the CSS, the extension row, the namespace and the class name all
follow from it.
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
    index.html
    mod_upcoming_bookings.php
    mod_upcoming_bookings.xml
```

Only `mod_upcoming_bookings.php` and `mod_upcoming_bookings.xml` are required.
In practice a module that renders anything worth reading also has
`tmpl/default.php`.

| Path | Purpose |
|---|---|
| `mod_upcoming_bookings.php` | The entry point: the module class, extending `Hubzero\Module\Module`. |
| `mod_upcoming_bookings.xml` | The manifest: name, client, files, and the parameter fields shown in the Module Manager. |
| `tmpl/` | Layouts. `default.php` is used unless a `layout` parameter says otherwise. |
| `assets/` | CSS, JavaScript, and images, split by type. |
| `language/` | Translation files, one directory per language tag. |
| `migrations/` | The registration migration and any schema changes. |
| `composer.json` | Package metadata for the installer. |
| `index.html` | An empty file in every directory, so a misconfigured web server cannot list it. |

Only the entry file and the layouts produce output. The manifest and the migration are what
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
skips the module rather than complaining. So a module whose entry file is
misnamed — `upcoming_bookings.php` inside `mod_upcoming_bookings/`, a rename
that missed one file — renders nothing at all. The name in the
`#__extensions` row, the directory name, and the entry filename all have to be
the same string.

## Namespaces and class names

The loader does not search for the class. `Loader::resolveClassName()` builds
it from the directory name: drop `mod_`, split the rest on underscores,
capitalise each piece, and use the result as both the namespace segment and
the class name.

| Directory | Class |
|---|---|
| `mod_login` | `Modules\Login\Login` |
| `mod_mygroups` | `Modules\Mygroups\Mygroups` |
| `mod_articles_archive` | `Modules\ArticlesArchive\ArticlesArchive` |
| `mod_upcoming_bookings` | `Modules\UpcomingBookings\UpcomingBookings` |

An underscore is a word break; nothing else is. `mod_mygroups` has no
underscore, so its class is `Mygroups`. PHP compares class names without
regard to case, so `MyGroups` would match too, but every core module spells
the class the way the rule produces it.

The loader also requires the class to extend `Hubzero\Module\Module`. When
the file declares no class of the expected name, or one that extends something
else, the loader treats the file as an old-style procedural entry point and
keeps whatever it echoed while being included — which, for a file that only
declares a class, is nothing. A typo in the class name renders an empty
module, with no error. See
[Controllers](03-controllers.md#old-style-entry-files).

The class loader resolves `Modules\…` the same way in reverse, so further
classes in the module's namespace load without a `require`.
`Modules\UpcomingBookings\Slot` is looked for as `Slot.php` and `slot.php` in
both `mod_upcomingbookings/` and `mod_upcoming_bookings/` — the namespace
segment is tried as one word and split back into words at each capital — and
`mod_adminmenu` reaches its `Tree` and `Node` classes, in `tree.php` and
`node.php`, this way. See
[Autoloading](../03-foundation/03-autoloading.md#the-per-kind-details-that-catch-people).

> **Warning:** The namespace also makes unqualified class names resolve inside
> `Modules\UpcomingBookings` first. `User::get('id')` in the module class
> looks for `Modules\UpcomingBookings\User` and fatals with "Class not
> found". Import each facade you use at the top of the file —
> `use Hubzero\Facades\User;`, as core does, or the root alias `use User;`.
> See [Facades](../03-foundation/06-facades.md).

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

Nothing stops a module from carrying classes of its own beside the entry
file, and three core modules do: `mod_adminmenu`, `mod_mytools` and
`mod_quickicon`. But a module that has grown a controller and
several screens is a component wearing a disguise; see
[Components](../09-components/index.md). `mod_upcoming_bookings` stays small
precisely because everything it knows about a reservation lives in
`com_bookings`: the module queries a model and renders a list.
