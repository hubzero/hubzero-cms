<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/modules/controllers
source-state: unpublished
-->
# Controllers

Modules have no controllers. There is no `controllers` directory, no
`controller.php`, no task dispatch, and no `Hubzero\Module\Controller` class in
the framework. A module never handles a request of its own: it is rendered as
a side effect of whatever page the component is already drawing.

What plays the part of a controller is a single unconditional script, the
entry file, which the loader includes to produce the module's output.

## The entry file

The file is named after the directory and sits at its top level —
`mod_login/mod_login.php`. This is the whole of it:

<!--include: core/modules/mod_login/mod_login.php-->

Three lines of work: declare the namespace, pull in the class, construct it
and call `display()`. `with()` is a global helper that returns the object it
is given, so that a method can be called on a freshly constructed instance in
one expression.

Write the same file for your module and change the namespace:

```php
<?php

namespace Modules\UpcomingBookings;

require_once __DIR__ . DS . 'helper.php';

with(new Helper($params, $module))->display();
```

That stub-plus-`helper.php` pair is not one style among several. It is what
[`Loader::render()`](../../../core/libraries/Hubzero/Module/Loader.php) is
built around:

```php
$path = $this->path($module->module);

if (file_exists($path))
{
	// … load the language file …
	ob_start();
	include $path;
	$module->content = ob_get_contents() . $content;
	ob_end_clean();
}
```

The loader `include`s a file. It never instantiates a class, never looks one
up by name, and never calls a method on your module. Whatever your entry file
echoes is the module's output; whatever it does not echo does not exist. All
100 shipped modules are written this way, and 99 of them put the work in a
`Helper` class beside the stub. The exception, `mod_multilangstatus`, has no
data to gather and constructs the base `Hubzero\Module\Module` directly so
that `display()` requires its layout — which is the shortest a module can be
and still have one.

## What is in scope

[`Hubzero\Module\Loader::render()`](../../../core/libraries/Hubzero/Module/Loader.php)
includes the entry file inside an output buffer, from inside a method, so the
file inherits that method's local variables. Two of them matter:

| Variable | Type | Contents |
|---|---|---|
| `$module` | `stdClass` | The row from `#__modules` for this instance: `id`, `title`, `module`, `position`, `content`, `showtitle`, `params`, `menuid`, plus `name` and `style` added by the loader. |
| `$params` | `Hubzero\Config\Registry` | The instance's parameters, parsed from `$module->params` and merged with any parameters passed by the caller. |

Both are handed straight to the module class constructor, which stores them as
`$this->module` and `$this->params`.

Anything the entry file echoes is captured. When the include returns, the
loader assigns the buffer to `$module->content` and then runs the chrome
function named by the render style, which is what actually emits the markup
into the page.

> **Note:** The include happens once per *instance*, not once per module. Two
> instances of `mod_login` in two positions run the file twice, with different
> `$module` rows and different `$params`. Nothing in the entry file may assume
> it runs only once — declaring a function or a class at the top level of the
> entry file will fatal on the second instance. That is precisely why the
> class lives in `helper.php` behind a `require_once`.

## Doing the work inline

The class in `helper.php` is a convention, not a requirement. A module with
almost no logic can do everything in the entry file:

```php
<?php

namespace Modules\UpcomingBookings;

use Lang;

defined('_HZEXEC_') or die();

echo '<p>' . Lang::txt('MOD_UPCOMING_BOOKINGS_NONE') . '</p>';
```

That works, and it is the wrong shape for anything you will keep. It gives up
both things the module class provides: a template override point, because
`getLayoutPath()` is a method on the class, and the asset helpers `css()`,
`js()`, and `img()`. It also means a hub cannot restyle your module without
editing your file. Use the class.

## Guarding the file

Core module entry files are reached only through the loader, which resolves
them from `PATH_APP` or `PATH_CORE`, so a direct HTTP request cannot execute
them unless the whole `modules` tree is inside the document root. Layouts and
helper files still carry the guard:

```php
defined('_HZEXEC_') or die();
```

Add it to anything a misconfigured server might serve directly. See
[Constants](../03-foundation/02-constants.md) for what `_HZEXEC_` is and where
it is defined.

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
