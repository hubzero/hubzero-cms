<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/modules/helpers
-->
# Helpers

The helper class is where a module does its work. It gathers whatever data the
layout needs, exposes it as properties, and requires the layout. By convention
it lives in `helper.php` beside the entry file and is named `Helper`, and it
extends [`Hubzero\Module\Module`](../../../core/libraries/Hubzero/Module/Module.php).

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

## A minimal helper

```php
<?php

namespace Modules\Example;

use Hubzero\Module\Module;
use User;

class Helper extends Module
{
    public function display()
    {
        $this->name = User::get('name');

        require $this->getLayoutPath();
    }
}
```

Properties set on `$this` before the `require` are visible to the layout,
because the layout is included in the method's scope. There is no separate
view object and no `assign()` step.

> **Warning:** `use User;` is not optional. Inside `namespace Modules\Example`
> an unqualified `User` resolves to `Modules\Example\User`, which does not
> exist, and the call is a fatal error. Import every facade the file names —
> `App`, `Lang`, `Request`, `Route`, `User`, `Component`, `Plugin`. See
> [Facades](../03-foundation/06-facades.md).

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

Read parameters through `get()` with a default rather than assuming a value is
present; a module instance created before you added a field will not have it.

## Caching

`getCacheContent()` gives a module an opt-in output cache keyed on the
instance id. It returns an empty string — meaning "no cached content, do the
work" — unless a cache store is configured, the instance's `cache` parameter
is on, `cache_time` is non-zero, and the application is not in debug mode. On
a miss it buffers a call to `run()`, appends an HTML comment recording when
the copy was made, stores it, and returns it.

That contract splits the class in two: `run()` produces the output, and
`display()` decides whether to reuse a stored copy. `mod_findresources` is the
pattern in full:

<!--include: core/modules/mod_findresources/helper.php:14-56-->

> **Note:** A module that calls `getCacheContent()` **must** define `run()`.
> The base class does not: `getCacheContent()` calls `$this->run()`, and a
> module without one fatals the first time the cache misses.

> **Note:** The cache key is `modules.{id}` and nothing else. It does not vary
> by user, view level, or menu item. Only cache a module whose output is the
> same for everyone who can see it — `mod_findresources` lists popular tags,
> `mod_mygroups` does not cache at all.

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
