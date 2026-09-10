<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/modules/helpers
-->
# Helpers

The helper class is where a module does its work. It gathers whatever data the
layout needs, exposes it as properties, and requires the layout. By convention
it lives in `helper.php` beside the entry file and is named `Helper`, and it
extends [`Hubzero\Module\Module`](../../../core/libraries/Hubzero/Module/Module.php).

It exists for a reason that has nothing to do with tidiness. The loader
`include`s the entry file once per *instance*, so anything declared at that
file's top level is declared twice the second time the module appears on a
page. Putting the class behind a `require_once` in a separate file is what
makes a module safe to place more than once. See
[Controllers](03-controllers.md).

## The base class

`Hubzero\Module\Module` extends `Hubzero\Base\Obj` and mixes in two traits,
`AssetAware` and `Escapable`. It is small enough to hold in your head:

| Member | What it is |
|---|---|
| `$params` | `Hubzero\Config\Registry` of the instance's parameters. |
| `$module` | The `#__modules` row for this instance. |
| `__construct($params, $module)` | Stores both. Takes them positionally, in that order. |
| `display()` | Requires the layout named by the `layout` parameter, defaulting to `default`. |
| `getLayoutPath($layout = 'default')` | Resolves a layout name to a file path, honouring template overrides. |
| `getCacheContent()` | Returns cached output for this instance, generating it by calling `run()` if the cache is cold. |
| `css()`, `js()`, `img()` | From `AssetAware`. See [Assets](07-assets.md). |
| `escape($var)`, `setEscape($spec)` | From `Escapable`. Escapes a value for output; `htmlspecialchars` by default. |

Because it extends `Obj`, the class also carries the error bag: `setError()`,
`getError()`, `getErrors()`.

## The smallest one that works

The whole of `mod_upcoming_bookings/helper.php`:

```php
<?php

namespace Modules\UpcomingBookings;

use Hubzero\Module\Module;
use Components\Bookings\Models\Reservation;
use Date;
use User;

defined('_HZEXEC_') or die();

class Helper extends Module
{
	public function display()
	{
		$query = Reservation::all()
			->whereEquals('created_by', User::get('id'))
			->whereEquals('state', 1)
			->where('starts', '>=', Date::of('now')->toSql());

		if ($instrument = intval($this->params->get('instrument_id', 0)))
		{
			$query->whereEquals('instrument_id', $instrument);
		}

		$this->reservations = $query
			->order('starts', 'asc')
			->limit(intval($this->params->get('limit', 5)))
			->rows();

		require $this->getLayoutPath();
	}
}
```

Properties set on `$this` before the `require` are visible to the layout,
because the layout is included in the method's scope. There is no separate
view object and no `assign()` step.

`Components\Bookings\Models\Reservation` needs no `require`: the class loader
maps `Components\` namespaces to component directories on its own. Several
shipped modules still write `include_once Component::path('com_groups') . DS .
'models' . DS . 'recent.php';` before naming the class. That is the older
convention, from before the class loader covered extension namespaces; it is
harmless but do not copy it. See
[Autoloading](../03-foundation/03-autoloading.md).

> **Warning:** `use User;` is not optional. Inside
> `namespace Modules\UpcomingBookings` an unqualified `User` resolves to
> `Modules\UpcomingBookings\User`, which does not exist, and the call is a
> fatal error — a white page, or an exception trace naming a class you never
> wrote. Import every facade the file names: `App`, `Lang`, `Request`,
> `Route`, `User`, `Component`, `Plugin`, `Date`. See
> [Facades](../03-foundation/06-facades.md).

> **Warning:** A module is rendered inside somebody else's page, so an
> uncaught exception in `display()` takes the whole page with it — not just
> the block. Nothing wraps the include in a `try`. A model call that can throw
> because the component is missing, or a table that does not exist yet, breaks
> every page the module is placed on.

## Choosing a layout

`display()` on the base class requires the layout named by the `layout`
parameter. If you override `display()`, you choose the layout yourself.
`mod_mygroups` switches on a parameter:

```php
$layout = 'default';
if (!$this->params->get('show_recent', 1))
{
    $layout = 'simple';
}

require $this->getLayoutPath($layout);
```

## Parameters

`$this->params` is a `Registry` built from the instance's `params` column, so
every field declared in the manifest's `<config>` block is readable by name,
with a fallback for the case where an administrator never saved the form:

```php
$this->limit = intval($this->params->get('limit', 100));
```

Parameters are what let one module serve two placements. `mod_upcoming_bookings`
declares `limit` and `instrument_id`, so the same code renders "your next five
bookings, anywhere" in the sidebar and "your next booking on the confocal" on
the instrument's own page, without a second module.

> **Warning:** Always pass the default to `get()`. The `default` attribute in
> the manifest is applied by the *form*, not by the Registry, so an instance
> saved before you added the field has no value for it and
> `$this->params->get('limit')` returns null. That is not an error anywhere
> down the stack: `limit(null)` becomes `limit(0)`, and the query builder
> emits `LIMIT 18446744073709551615`. Your five-item sidebar block quietly
> becomes every row in the table.

## Caching

Cache a module when the query behind it is expensive and its output is the
same for everybody. `getCacheContent()` gives an opt-in output cache keyed on
the instance id. It returns an empty string — meaning "no cached content, do
the work" — unless a cache store is configured, the instance's `cache`
parameter is on, `cache_time` is non-zero, and the application is not in debug
mode. On a miss it buffers a call to `run()`, appends an HTML comment
recording when the copy was made, stores it, and returns it.

That contract splits the class in two: `run()` produces the output, and
`display()` decides whether to reuse a stored copy. `mod_findresources` is the
pattern in full:

<!--include: core/modules/mod_findresources/helper.php:14-56-->

> **Note:** A module that calls `getCacheContent()` **must** define `run()`.
> The base class does not: `getCacheContent()` calls `$this->run()`, and a
> module without one fatals the first time the cache misses.

> **Warning:** The cache key is `modules.{id}` and nothing else. It does not
> vary by user, view level, or menu item. Cache a per-user module and the
> first visitor's output is served to everyone else — `mod_upcoming_bookings`
> would show one researcher's reservations to the whole hub. That is a data
> leak, not a rendering bug, and nothing in the framework stops you: the
> `cache` parameter is on the instance, so an administrator can switch it on
> for a module you never intended to cache. Do not declare `cache` and
> `cache_time` fields in the manifest of a module whose output is per-user.
> `mod_findresources` lists popular tags and caches; `mod_mygroups` is
> per-user and does not.

`cache_time` is stored in seconds by the Module Manager but the cache store
takes minutes, so `getCacheContent()` divides values above 120 by 60 and
passes smaller values through unchanged. A `cache_time` of 900 caches for
fifteen minutes; a `cache_time` of 30 caches for thirty minutes, not thirty
seconds.

## More than one class

Nothing limits a module to one class. Put additional classes in their own
files under the module directory and `require_once` them from `helper.php`,
keeping them in the module's namespace so they cannot collide with another
extension's.

`require_once` them by path, not by name. The class loader resolves
`Modules\…` by collapsing the namespace segment to one word, so a class in
`mod_upcoming_bookings` is looked for under `mod_upcomingbookings` and never
found — see [Structure](02-structure.md#namespaces-and-class-names) and
[Autoloading](../03-foundation/03-autoloading.md#the-per-kind-details-that-catch-people).

Before you add the second class, ask whether it belongs in the component. A
"does this reservation clash" method is the `Reservation` model's, and putting
it in the module hides it from the administrator screens that need the same
rule.
