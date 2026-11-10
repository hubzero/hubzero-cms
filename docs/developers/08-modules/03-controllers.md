<!--
status: rewritten
reviewed-against: per-cs-3.1 @ 6196e778df
reviewed: 2026-11-10
-->
# Controllers

Modules have no controllers. There is no `controllers` directory, no
`controller.php`, no task dispatch, and no `Hubzero\Module\Controller` class in
the framework. A module never handles a request of its own: it is rendered as
a side effect of whatever page the component is already drawing.

What plays the part of a controller is one method, `display()`, on the class in
the entry file. The loader calls it once for each instance on the page.

## The entry file

The file is named after the directory and sits at its top level —
`mod_multilangstatus/mod_multilangstatus.php`. It declares the module class
and nothing else. This one needs no code of its own, because the base class
already knows how to require a layout:

<!--include: core/modules/mod_multilangstatus/mod_multilangstatus.php-->

`mod_upcoming_bookings.php` starts the same way, with its own name in it:

```php
<?php

namespace Modules\UpcomingBookings;

use Hubzero\Module\Module;

class UpcomingBookings extends Module
{
}
```

The file runs no code when it is included. It has no `defined('_HZEXEC_')`
guard either — none of the 100 core entry files does — because a file that
only declares a class does nothing if a web server is tricked into executing
it. The layouts, which do emit output, keep the guard; see
[Views](06-views.md).

## How the loader runs it

[`Hubzero\Module\Loader::render()`](../../../core/libraries/Hubzero/Module/Loader.php)
finds the entry file, loads the module's language file, and then:

<!--include: core/libraries/Hubzero/Module/Loader.php:262-289-->

In order:

1. `resolveClassName()` turns `mod_upcoming_bookings` into
   `Modules\UpcomingBookings\UpcomingBookings`. See
   [Structure](02-structure.md#namespaces-and-class-names).
2. If that class is not declared yet, the file is included. On the second
   instance of the same module on a page it already is, so the file is not
   included again and nothing is declared twice.
3. If the class exists and extends `Hubzero\Module\Module`, the loader
   constructs it with this instance's parameters and row, calls `display()`, and
   keeps everything echoed as `$module->content`.

The loader then wraps `$module->content` in the chrome function named by the
render style, which is what actually emits the markup into the page. See
[Loading](09-loading.md).

On the base class, `display()` calls `run()`, and `run()` requires the
layout named by the `layout` parameter. So the ordinary module overrides
`display()`: it gathers data, sets properties, and requires a layout. 79 of
the 100 core modules do exactly that and leave `run()` alone. The 19 that
cache their output override both: `run()` does the work, and `display()`
serves a stored copy when there is one. See
[The module class](04-helpers.md#caching).

## What the class is handed

The constructor stores its two arguments as properties:

| Property | Type | Contents |
|---|---|---|
| `$this->module` | `stdClass` | The row from `#__modules` for this instance: `id`, `title`, `module`, `position`, `content`, `showtitle`, `params`, `menuid`, `protected`, plus `name`, `style` and `path` added by the loader. |
| `$this->params` | `Hubzero\Config\Registry` | The instance's parameters, parsed from `$module->params` and merged with any parameters passed by the caller. |

> **Note:** The loader constructs a new object for every *instance*. Two
> instances of `mod_login` in two positions are two `Login` objects, with
> different `$module` rows and different `$params`. Keep per-instance state
> on `$this`; a `static` property is shared by every instance on the page.

## Old-style entry files

Before this release a module's entry file was a stub that required
`helper.php` and called a `Helper` class:

```php
<?php

namespace Modules\UpcomingBookings;

require_once __DIR__ . DS . 'helper.php';

with(new Helper($params, $module))->display();
```

The loader still runs files like that. When the file declares no class of the
expected name, `render()` includes it inside the output buffer, from inside
the method, so `$params` and `$module` are in scope as local variables, and
whatever the file echoes becomes the module's output. A hub's own modules in
`app/modules` written for an earlier release keep working without changes.

Two things are different about that path. The file is included again for
every instance, so anything it declares at the top level fatals the second
time — which is why the old stub kept its class behind a `require_once`. And
the same fallback is what runs when you *meant* to write a class but got its
name wrong: the file is included, declares a class nobody asks for, echoes
nothing, and the module renders empty.

Write new modules as a class.

## What replaces a controller

If you find yourself wanting a controller — a module that responds to a form
post, or that needs several tasks — the work belongs somewhere else. Post to a
component and let it redirect back; the module then only renders the form.
`mod_login` does exactly this: it renders a form whose action points at
`com_login`, and holds no submission logic itself.

Apply that to the booking module and the line falls in an obvious place. A
**Cancel** button beside each reservation is fine, as long as it is a link or
a form posting to
`index.php?option=com_bookings&controller=reservations&task=cancel`, which
does the work and redirects. The module never cancels anything itself. If it
did, the cancellation would run on every page of the hub that carries the
module, and there would be no route to it that a menu, a permission check, or
a redirect could see.
