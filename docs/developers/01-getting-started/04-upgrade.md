<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/index/upgrade
source-id: 3426
modified: 2015-07-06
-->
# Upgrade guide

Hubzero began as a fork of Joomla, and at 2.0 it replaced most of the
Joomla names with its own. An extension written against `JFactory`,
`JText` and `JRequest` still needs converting. This page is that
translation table, plus what to do about the database when you upgrade a
hub.

For the Hubzero 1.x names that changed at the same time, see
[Release notes](releasenotes.md#the-2-0-namespacing).

## Directory structure

The tree has two top-level directories:

```
/app
/core
index.php
```

`core/` is the platform — the framework library, the shipped components,
plugins, modules and templates, and the migrations. `app/` is one hub's
own state: its configuration, its overrides, its logs, cache and uploads.
`index.php` at the root is the only entry point. There is no
`administrator/` directory and no `api/` directory; both applications are
served from the same front controller.

[Structure](../03-foundation/01-structure.md) covers the layout in full.

## Constants

| Joomla | Hubzero |
|---|---|
| `JPATH_ROOT`, `JPATH_BASE`, `JPATH_SITE` | `PATH_ROOT` |
| `JPATH_ADMINISTRATOR` | Nothing. The directory does not exist. |
| `JPATH_COMPONENT` | `Component::path($option)` |
| `_JEXEC` | `_HZEXEC_` |
| n/a | `PATH_APP` — `ROOT/app`, this hub's own data |
| n/a | `PATH_CORE` — `ROOT/core`, the framework and shipped extensions |

> **Note:** The `JPATH_*` names are still defined, in
> [`core/bootstrap/app.php`](../../../core/bootstrap/app.php), so old code
> keeps running. They are compatibility aliases, not the names to write.
> `JPATH_ADMINISTRATOR` points at a `ROOT/administrator` that is not in the
> tree.

When including a file from within the same extension, prefer PHP's own
`__DIR__` and `__FILE__` over any constant:

```php
// This file is ROOT/app/components/com_example/admin/example.php

// dirname(__DIR__) moves up one level, to com_example
require_once dirname(__DIR__) . DS . 'models' . DS . 'foo.php';
require_once __DIR__ . DS . 'controllers' . DS . 'example.php';
```

Better still, do not include anything. The class loader resolves
`Components\Example\Models\Foo` to that path on its own; see
[Constants](../03-foundation/02-constants.md) and
[Structure](../03-foundation/01-structure.md).

## Classes

Most of the conversions are a facade with the `J` dropped. Facades are
registered as root-namespace aliases, so a namespaced file must import the
one it uses — `use Route;` — or the call fatals at runtime. See
[Facades](../03-foundation/04-facades.md#importing-a-facade).

### JRoute

| Joomla | Hubzero |
|---|---|
| `JRoute::_($url)` | `Route::url($url)` |

### JText

`Lang` replaces `JText`, and `_()` and `sprintf()` merged into one
`Lang::txt()` that takes a variable number of arguments. Pass more than
one and the translator does the replacement.

```php
// Language file
COM_EXAMPLE_HELLO="Hello!"
COM_EXAMPLE_HELLO_NAME="Hello, %s!"
```

```php
// Outputs 'Hello!'
echo Lang::txt('COM_EXAMPLE_HELLO');

// Outputs 'Hello, Hubzero!'
echo Lang::txt('COM_EXAMPLE_HELLO_NAME', 'Hubzero');
```

| Joomla | Hubzero |
|---|---|
| `JText::_()` | `Lang::txt()` |
| `JText::sprintf()` | `Lang::txt()` |
| `JText::plural()` | `Lang::txts()` |
| `JText::alt()` | `Lang::alt()` |

### JRequest

Every public `JRequest` method survives on the request object, so dropping
the `J` is usually enough:

```php
// Via the application container
$foo = App::get('request')->getVar('foo');

// Via the facade
$foo = Request::getVar('foo');
```

| Joomla | Hubzero |
|---|---|
| `JRequest::*` | `Request::*` |

### JToolbarHelper and JSubMenuHelper

Class name only; the methods and their arguments are unchanged.

```php
// Joomla
JToolbarHelper::publishList();

// Hubzero
Toolbar::publishList();
```

`JSubMenuHelper` becomes `Submenu`, with one difference worth noticing:
the link is routed.

```php
Submenu::addEntry(
	Lang::txt('COM_COLLECTIONS_POSTS'),
	Route::url('index.php?option=com_collections&controller=posts'),
	$controllerName == 'posts'
);
```

### JHtml

This one is not a rename. Joomla passed everything through `JHtml::_()`
with a dotted first argument naming the sub-library and the function.
Hubzero makes the sub-library the method and the function the first
argument:

```php
// Joomla
echo JHtml::_('grid.sort', 'COM_COLLECTIONS_COL_TITLE', 'title', $dir, $sort);
echo JHtml::_('behavior.framework');

// Hubzero
echo Html::grid('sort', 'COM_COLLECTIONS_COL_TITLE', 'title', $dir, $sort);
echo Html::behavior('framework');
```

The sub-libraries are the classes in
[`core/libraries/Hubzero/Html/Builder/`](../../../core/libraries/Hubzero/Html/Builder):
`access`, `asset`, `batch`, `behavior`, `category`, `content`,
`contentlanguage`, `grid`, `input`, `select`, `sliders`, `tabs`. There is
no `date` sub-library; for a relative date use `Date::of($d)->relative()`.

## Factory objects

Objects that came from `JFactory` come from the service container, and most
have a facade in front of them. `method()` below stands for whatever you
used to call on the Joomla object:

```php
// Joomla
$user = JFactory::getUser();
echo $user->get('name');

// Hubzero
echo User::get('name');
```

| Joomla | Container | Facade |
|---|---|---|
| `JFactory::getDbo()` | `App::get('db')` | n/a |
| `JFactory::getUser()`, `JUser::getInstance()` | `App::get('user')` | `User::method()` |
| `JFactory::getSession()` | `App::get('session')` | `Session::method()` |
| `JFactory::getDocument()` | `App::get('document')` | `Document::method()` |
| `JFactory::getConfig()` | `App::get('config')` | `Config::method()` |
| `JFactory::getLanguage()` | `App::get('lang')` | `Lang::method()` |
| `JFactory::getCache()` | `App::get('cache.store')` | `Cache::method()` |
| `JFactory::getLogger()` | `App::get('log')->logger('{name}')` | `Log::method()` |

> **Note:** The `Log` facade writes to the `debug` log. For any other log,
> go through `App::get('log')->logger('{name}')`.

## Dates

`JDate` becomes [`Hubzero\Utility\Date`](../../../core/libraries/Hubzero/Utility/Date.php),
reached through the `Date` facade. With no argument it means now, in UTC.

```php
// The current UTC timestamp in the database's format: "2026-04-03 12:23:56"
echo Date::toSql();

// The current UTC timestamp as a Unix time
echo Date::toUnix();

// The current UTC year: "2026"
echo Date::format('Y');

// Adjusted to the hub's timezone. UTC 12:23 pm on an Eastern hub is "08:23 am"
echo Date::toLocal('g:i a');
```

`Date::of()` takes a specific timestamp, and an optional timezone that
defaults to UTC:

```php
echo Date::of('2013-08-12 17:01:34')->format('Y');    // "2013"
echo Date::of('2013-08-12 17:01:34')->toLocal('g:i a'); // "1:01 pm"
```

See [Dates](../05-basics/10-dates.md).

## Users

Any method called statically on `User`, other than `getInstance()`, acts on
the current user — the equivalent of `JFactory::getUser()->method()`.

```php
// Joomla
echo JFactory::getUser()->get('name');

// Hubzero
echo User::get('name');
```

`getInstance()` returns the object behind the facade, with or without an
id or username:

```php
$user  = User::getInstance();      // the current user
$other = User::getInstance(1234);  // JFactory::getUser(1234)
```

## The database

An upgrade is not finished when the files are in place. Schema and data
changes ship as [migrations](../06-database/02-migrations.md) — small PHP
classes under a `migrations` directory with an `up()` and a `down()` —
and [muse](../12-muse/README.md) runs the ones a hub has not seen,
recording each in `#__migrations` so it never runs twice.

```bash
php core/bin/muse migration      # dry run: lists what would happen
php core/bin/muse migration -f   # actually run them
```

The dry run is the default, which is the safest thing about the command
and the easiest to miss. `-e com_example` limits the run to one extension,
`-d down` reverses, and the full option list is in the
[muse migration reference](../../reference/muse/migration.md).

> **Warning:** Reversing a schema change is often impossible without data
> loss, whatever `down()` claims. Take a database dump before an upgrade:
> `php core/bin/muse database dump`.

> **Note:** Older instructions pair `-f` with `-i`. `-i` now behaves as
> `-a`, which only widens the list of migrations considered; it is not
> needed for an ordinary upgrade.
