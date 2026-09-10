<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/components
-->
# Components

A component is the largest of the extension types: a self-contained
application with its own controllers, views, models, database tables,
configuration, permissions, and URL space. `com_blog`, `com_kb`, and
`com_resources` are components. Each owns a top-level path on the site and
everything below it.

## When you want one

Reach for a component when the thing you are adding **is a page** — when a
user will navigate to it, it has records of its own, and someone has to
administer them. A component owns the request, so only one runs per page.

Reach for something smaller when it does not:

| You want | Build |
|---|---|
| A screen with records behind it | a component |
| Something to happen when a record is saved, a user logs in, a group page is drawn | a [plugin](../10-plugins/README.md) |
| A block in the sidebar of pages someone else owns | a [module](../08-modules/README.md) |
| A different look for pages that already exist | a [template](../11-templates/README.md) or a [template override](../11-templates/09-overrides.md) |

A component that only ever renders a box on other people's pages is a module
wearing the wrong clothes, and it will fight the router for a URL it does not
need.

## The example this section builds

The pages that follow build one component, in the order you would build it:
**`com_bookings`**, which books a lab's instruments. It is small and it is
complete:

- two tables, `#__bookings_instruments` and `#__bookings_reservations`;
- an administrator face where a lab manager adds instruments and cancels
  reservations;
- a site face where a user picks an instrument, sees what is already booked,
  and reserves a slot;
- a `bookings` plugin group, so a hub can hook a reservation being created —
  to email the lab, or push it to a scheduling system — without editing the
  component.

`com_bookings` is the component being written. The code samples that are
*included from the tree* come from `com_kb`, the knowledge base: it is a real
shipped component small enough to read in an afternoon and complete enough to
show every piece — site, administrator and API clients, an ORM model, a
router, `config.xml`, `access.xml`, language files, and migrations. Where a
page shows `com_kb`, that is what the working code looks like today. Where it
shows `com_bookings`, that is what you write.

The same example runs through [Database](../06-database.md); the model,
queries and table names there are the ones used here.

## Register it, or it is invisible

> **Important:** A component is not installed by its XML manifest. Nothing
> reads a manifest and creates anything from it, and there is no package
> installer to run one — see
> [Deploying extensions](../07-extensions/04-deployext.md). A component
> announces itself to the hub from a **migration**, by calling
> `addComponentEntry()`. Skip that step and you get a component that exists on
> disk, runs when you type its URL, and is invisible everywhere else: absent
> from the administrator's **Components** menu, with no parameters, and with
> no asset row for its permissions to hang off.

This is the step most often missed, which is why [Migrations](01-migrations.md)
is the first chapter rather than the last.

## The smallest component that runs

Four files under `app/components/com_bookings/` and one command:

```
app/components/com_bookings/
    site/bookings.php
    site/controllers/instruments.php
    site/views/instruments/tmpl/display.php
    migrations/Migration20260901000000ComBookings.php
```

`site/bookings.php` — the entry point. It is `require`d, so it runs at the top
level; it names a controller and executes it:

```php
namespace Components\Bookings\Site;

defined('_HZEXEC_') or die();

$controller = new Controllers\Instruments();
$controller->execute();
```

`site/controllers/instruments.php` — one class, one task:

```php
namespace Components\Bookings\Site\Controllers;

use Hubzero\Component\SiteController;

class Instruments extends SiteController
{
	public function displayTask()
	{
		$this->view->display();
	}
}
```

`site/views/instruments/tmpl/display.php` — the layout the task renders,
chosen by name and wired up by nothing:

```php
<?php defined('_HZEXEC_') or die(); ?>
<h2>Instruments</h2>
```

Then register it:

```bash
php core/bin/muse migration -f -e=com_bookings
```

`/index.php?option=com_bookings` now renders the heading. Everything in the
rest of this section is added on top of those four files.

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
[Structure](02-structure.md).

Write your own component into `app/`. `core/` is the platform's, and an
upgrade replaces it.

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
valid; so is one with only `api/`. `com_bookings` grows a `site/` first, then
an `admin/`, and never needs an `api/`.

## How a request reaches your code

The site and administrator applications route a request to a single component,
identified by the `option` query variable, then call
`Component::render('com_bookings')`. That method defines `PATH_COMPONENT` (the
client directory), loads the component's language files, and executes the entry
point. The entry point picks a controller class, constructs it, and calls
`execute()`. The controller runs a task, hands data to a view, and the view
renders a layout. See [Controllers](03-controllers.md) and [Views](07-views.md).

The API application takes a different path through
[`Hubzero\Api\Component\Loader`](../../../core/libraries/Hubzero/Api/Component/Loader.php),
which resolves a versioned controller class and calls `execute()` on it
directly. There is no view layer; responses are set on the response object.

## Naming

Every name in a component derives from one word. For `com_bookings` that word
is `bookings`:

| Thing | Form |
|---|---|
| Directory | `com_bookings` |
| Entry points | `site/bookings.php`, `admin/bookings.php` |
| Namespace | `Components\Bookings` |
| Table prefix | `#__bookings_` |
| Language files | `en-GB.com_bookings.ini` |
| Migration classes | `Migration20260901000000ComBookings` |
| Language keys | `COM_BOOKINGS_*` |

Throughout these pages, `{ComponentName}` stands for the studly-cased name —
`Bookings`, `Kb`, `Blog` — and `{componentname}` for its lowercase form. The
same pairing applies to `{ControllerName}`, `{ViewName}`, and `{ModelName}`.

> **Warning:** Keep the name a single word of letters and digits. The
> migration runner's `-e` option validates the extension against
> `^com_[[:alnum:]]+$`, so `muse migration -e=com_instrument_bookings` is
> refused outright — a component whose name carries a second underscore cannot
> be migrated by name.

## In this section

- [Migrations](01-migrations.md) — registering the component and creating its
  tables. Start here.
- [Structure](02-structure.md) — the directory layout, the namespaces, and the
  entry point.
- [Controllers](03-controllers.md) — tasks, the task map, and the site,
  administrator, and API base classes.
- [Helpers](04-helpers.md) — shared static classes, and helpers callable from a
  view.
- [Models](05-models.md) — the ORM models and plain classes that hold a
  component's data and rules.
- [Views](07-views.md) — view objects, layouts, the search order, and template
  overrides.
- [Languages](06-languages.md) — the `.ini` files, where each is loaded from, and
  `Lang::txt()`.
- [Assets](08-assets.md) — pushing CSS and JavaScript, and building image paths.
- [Routing](09-routing.md) — `router.php`, `build()`, and `parse()`.
- [Configuration](10-configuration.md) — `config.xml`, `access.xml`, and reading
  parameters back.
- [Packaging](11-packaging.md) — `composer.json`, the manifest, and what a
  distributable component contains.
