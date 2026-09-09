<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/components
-->
# Components

A component is the largest of the extension types: a self-contained
application with its own controllers, views, models, database tables,
configuration, permissions, and URL space. `com_blog`, `com_kb`, and
`com_resources` are components. Each owns a top-level path on the site and
everything below it.

The pages in this section walk through the parts of a component in the order
you build them. The worked example throughout is
`com_kb`, the knowledge base: it is small
enough to read in an afternoon and complete enough to show every piece — site,
administrator, and API clients, an ORM model, a router, `config.xml`,
`access.xml`, language files, and migrations.

## Where components live

There are two roots. `core/components/` holds what the platform ships.
`app/components/` holds what a single hub adds, and it wins. `Component::path()`
in [`Hubzero\Component\Loader`](../../../core/libraries/Hubzero/Component/Loader.php)
tries four directories and takes the first that exists:

1. `PATH_APP/components/{name}`
2. `PATH_APP/components/com_{name}`
3. `PATH_CORE/components/{name}`
4. `PATH_CORE/components/com_{name}`

Whichever directory it finds owns the component outright. Putting
`app/components/com_kb/` beside the shipped `core/components/com_kb/` replaces
it wholesale; the two trees are never merged, so a partial copy is a broken
copy. The same rule governs class loading — see
[Structure](structure.md).

## The three clients

A component serves up to three applications, and each gets its own
subdirectory:

| Directory | Application | Entry point |
|---|---|---|
| `site/` | the public hub | `site/{name}.php` |
| `admin/` | the administrator at `/administrator` | `admin/{name}.php` |
| `api/` | the JSON API at `/api` | `api/controllers/{name}v{major}_{minor}.php` |

Controllers, views, assets, and language files belong to one client and live
inside its directory. Models, `config/`, and `migrations/` sit above the client
directories because all three clients share them.

None of the three is required. A component with only `site/` is perfectly
valid; so is one with only `api/`.

## How a request reaches your code

The site and administrator applications route a request to a single component,
identified by the `option` query variable, then call
`Component::render('com_kb')`. That method defines `PATH_COMPONENT` (the client
directory), loads the component's language files, and executes the entry
point. The entry point picks a controller class, constructs it, and calls
`execute()`. The controller runs a task, hands data to a view, and the view
renders a layout. See [Controllers](controllers.md) and [Views](views.md).

The API application takes a different path through
[`Hubzero\Api\Component\Loader`](../../../core/libraries/Hubzero/Api/Component/Loader.php),
which resolves a versioned controller class and calls `execute()` on it
directly. There is no view layer; responses are set on the response object.

## Naming

Throughout these pages, `{ComponentName}` stands for the studly-cased name a
developer chooses — `Kb`, `Blog`, `Resources` — and `{componentname}` for its
lowercase form. Directories on disk are lowercase and prefixed: `com_kb`.
Namespaces are studly-cased and unprefixed: `Components\Kb`. The same pairing
applies to `{ControllerName}`, `{ViewName}`, and `{ModelName}`.

## In this section

- [Migrations](migrations.md) — registering the component and creating its
  tables.
- [Structure](structure.md) — the directory layout, the namespaces, and the
  entry point.
- [Controllers](controllers.md) — tasks, the task map, and the site,
  administrator, and API base classes.
- [Helpers](helpers.md) — shared static classes, and helpers callable from a
  view.
- [Models](models.md) — the ORM models and plain classes that hold a
  component's data and rules.
- [Views](views.md) — view objects, layouts, the search order, and template
  overrides.
- [Languages](languages.md) — the `.ini` files, where each is loaded from, and
  `Lang::txt()`.
- [Assets](assets.md) — pushing CSS and JavaScript, and building image paths.
- [Routing](routing.md) — `router.php`, `build()`, and `parse()`.
- [Configuration](configuration.md) — `config.xml`, `access.xml`, and reading
  parameters back.
- [Packaging](packaging.md) — `composer.json`, the manifest, and what a
  distributable component contains.
