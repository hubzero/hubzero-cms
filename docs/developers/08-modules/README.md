<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/modules
-->
# Modules

A module is a small, repeatable block of output that a template drops into a
named position on the page: a login form, a list of the groups you belong to,
a breadcrumb trail. Unlike a component, a module never owns the request. It is
handed the page's parameters, renders some HTML into an output buffer, and the
buffer is spliced into the template.

Modules live in `core/modules` (the ones that ship with the CMS) and in
`app/modules` (the ones a hub adds or overrides). Every module directory is
named after the module and prefixed with `mod_`, and the same name is used for
the entry file, the manifest, the language files, and the row in the
`#__extensions` table.

## When you want one

Reach for a module when you want to show something on pages you do not own.
The lab's booking screens belong to a component; a box in the sidebar of
*every* page saying "your next two instrument slots" belongs to a module,
because no component owns the front page, the knowledge base, and a group
overview all at once.

| You want | Build |
|---|---|
| A screen with records behind it, and a URL | a [component](../09-components/README.md) |
| A block that appears on pages a component already draws | a module |
| Something to happen when a record is saved | a [plugin](../10-plugins/README.md) |
| A form that posts and acts on the result | a component; the module renders the form and posts to it |

A module has no router, no tasks, and no request of its own. The moment you
want it to handle a submission you have outgrown it — see
[Controllers](03-controllers.md).

## The example this section builds

The pages that follow build one module: **`mod_upcoming_bookings`**, a sidebar
block listing the current user's next few instrument reservations. It reads
`Components\Bookings\Models\Reservation` from `com_bookings`, the
instrument-booking component the [Components](../09-components/README.md)
section builds, and stores nothing of its own.

That is the ordinary shape of a module: a few lines of query against somebody
else's model, one layout, two parameters. Where a page shows code *included
from the tree* it comes from a shipped module — `mod_mygroups`,
`mod_findresources`, `mod_login` — because those are the working files. Where
it shows `mod_upcoming_bookings`, that is what you write.

## What a module is made of

At minimum a module is two files: an entry point named after the directory,
and an XML manifest. Nearly every real module adds a helper class, a layout,
and a language file:

```
app/modules/mod_upcoming_bookings/
    assets/
        css/mod_upcoming_bookings.css
    language/en-GB/
        en-GB.mod_upcoming_bookings.ini
        en-GB.mod_upcoming_bookings.sys.ini
    migrations/
        Migration20260101000000ModUpcomingBookings.php
    tmpl/
        default.php
    composer.json
    helper.php
    mod_upcoming_bookings.php
    mod_upcoming_bookings.xml
```

The entry file is included by [`Hubzero\Module\Loader`](../../../core/libraries/Hubzero/Module/Loader.php)
with two variables already in scope, `$params` and `$module`. It hands both to
a helper class extending [`Hubzero\Module\Module`](../../../core/libraries/Hubzero/Module/Module.php),
which gathers data and requires a layout out of `tmpl/`.

That two-part arrangement — a stub that `require_once`s `helper.php` and
constructs a class named `Helper` — is what 99 of the 100 shipped modules do,
and it is what the loader expects. It does not instantiate anything itself: it
`include`s the entry file inside an output buffer and keeps whatever was
echoed. See [Controllers](03-controllers.md).

> **Note:** Module classes are namespaced (`namespace Modules\UpcomingBookings;`) but
> the layouts they require are not. A namespaced class must `use` every facade
> it calls — `use User;`, `use Request;` — or the call resolves to a class in
> the module's own namespace and fatals. See
> [Facades](../03-foundation/06-facades.md). Layouts run in the global
> namespace and need no imports.

## Two things stand between your files and the page

Copying a module into `app/modules` is not enough, and neither is registering
it. Both of these have to be true before anything renders:

1. **A row in `#__extensions`.** A migration writes it. Until it exists the
   Module Manager will not offer your module at all — of the four extension
   kinds this is the strictest, and the
   [extensions chapter](../07-extensions/README.md#how-an-extension-is-found)
   compares them. See [Migrations](01-migrations.md).
2. **An instance assigned to a position.** An administrator creates an
   *instance* in the Module Manager, gives it a title, picks a template
   position, sets the access level and the menu assignments, and publishes it.
   Several instances of the same module can exist at once, each with its own
   parameters. Everything the loader knows about an instance comes from
   `#__modules` and `#__modules_menu`.

Neither failure says anything. A module with no extension row is missing from
a dropdown; a module with no instance is missing from the page.

> **Warning:** Positions belong to the template, not to the module. Your
> module never names a position, and the position picker only lists what the
> active template declared. Assign an instance to a position the active
> template's `index.php` never includes and it renders nothing, with no error.
> See [Loading](09-loading.md#positions-belong-to-the-template).

## In this section

- [Migrations](01-migrations.md) — registering the module with the CMS.
- [Structure](02-structure.md) — the directory layout and where the loader
  looks for it.
- [Controllers](03-controllers.md) — the entry file, and what is in scope
  when it runs.
- [Helpers](04-helpers.md) — the module class, its properties, and caching.
- [Languages](05-languages.md) — translation files and how they are loaded.
- [Views](06-views.md) — layouts, `getLayoutPath()`, and template overrides.
- [Assets](07-assets.md) — pushing CSS and JavaScript to the document.
- [Packaging](08-packaging.md) — the Composer and XML manifests.
- [Loading](09-loading.md) — rendering modules from templates, components,
  and article content.
