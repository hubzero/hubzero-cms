<!--
status: reviewed
reviewed-against: per-cs-3.1 @ 9307c2eacf
reviewed: 2026-11-10
-->
# Structure

A plugin is identified by two names: the group it belongs to and its own. Get
them right once and everything else follows from them, because both are
directory names and together they determine the class name, the language file
name, the asset paths, the view override path, and the `folder` and `element`
columns of the plugin's row in `#__extensions`. Get one of them wrong and the
plugin does not load, with no message anywhere. That is what this chapter is
about.

For the worked example the two names are `bookings` and `notify`, so the
plugin is `plg_bookings_notify` and it lives in
`app/plugins/bookings/notify/`.

## Directory layout

Plugins are never at the top level of `plugins`; they are always one level
down, inside a group directory:

```
app/plugins/
    {group}/
        {name}/
            {name}.php
            {name}.xml
```

So `plg_bookings_notify`:

```
app/plugins/bookings/notify/
    language/en-GB/en-GB.plg_bookings_notify.ini
    language/en-GB/en-GB.plg_bookings_notify.sys.ini
    migrations/Migration20260210000000PlgBookingsNotify.php
    views/email/tmpl/message.php
    composer.json
    index.html
    notify.php
    notify.xml
```

Only `notify.php` and `notify.xml` are required, and the migration if the
plugin is to run at all. Because the group is part of the path, the same
plugin name can be reused in different groups — `core/plugins/content/formatwiki`
and a `wiki` group plugin of the same name would coexist without conflict.

## Group and directory must match

The group name is not a label. It is a path segment, and it comes from the
database:
[`Hubzero\Plugin\Loader::path()`](../../../core/libraries/Hubzero/Plugin/Loader.php)
takes the `folder` column of the extension row, joins it to `plugins`, and
looks for that directory. The same value is the prefix on the events the
plugin hears, because `Event::trigger('bookings.onReservationCreate', …)`
splits at the dot and imports the group named on the left.

Nothing validates the pair. There is no register of legal group names, no
manifest check, and no warning when the directory named by a row does not
exist.

> **Warning:** If the row says `folder = 'booking'` and the directory is
> `app/plugins/bookings/`, `path()` finds no directory and returns an empty
> string, the loader builds the path `/notify.php`, `file_exists()` says no,
> and `init()` returns `null`. The plugin is listed in the Plugin Manager,
> shows as enabled, and never runs. The same happens the other way round —
> directory `booking`, trigger `bookings.` — and again if the plugin's own
> name and its `element` column disagree. The group name has to be identical
> in the directory, in the `folder` column, and in the prefix on every event
> the component triggers.

So when `com_bookings` fires `bookings.onReservationCreate`, the plugin
directory is `plugins/bookings/`, the migration calls
`addPluginEntry('bookings', 'notify')`, and the manifest says
`group="bookings"`. Singular or plural is your choice; making the same choice
in all four places is not.

## Characters in the two names

`Hubzero\Plugin\Loader` strips anything outside letters, digits, `_`, `.`, and
`-` from both names before building a class name or a path. That is what is
*permitted*. What is *safe* is narrower: lowercase letters and digits, and
nothing else.

> **Warning:** An underscore in either name breaks two separate things, neither
> of them loudly. `Hubzero\Document\Asset\File` splits `plg_group_element` on
> underscores and reads the second and third pieces as the group and the
> element, so [assets](06-assets.md) resolve to a directory that does not
> exist and silently load nothing. The `getParams` and `saveParams`
> [migration macros](01-migrations.md#shipping-default-parameters) split the
> same string the same way and read or write the wrong row. No plugin
> in `core/plugins` has an underscore in its group or its element. Use a
> hyphen if you need a separator, as `editors-xtd` does, or run the words
> together, as `formatwiki` does.

## Where the loader looks

`Loader::path()` resolves a group and name to a directory by checking
`PATH_APP` first and `PATH_CORE` second:

1. `app/plugins/{group}/{name}`
2. `core/plugins/{group}/{name}`

The entry file is `{name}.php` inside whichever directory exists. A hub
overrides a shipped plugin by copying the whole directory into `app/plugins`;
the extension row does not change, because it records only the group and the
element. Your own plugins belong in `app/`, always —
`core/` is replaced wholesale by an upgrade.

## The class

The entry file declares one class, namespaced by the group and the plugin and
named after the plugin:

```php
<?php

namespace Plugins\Bookings\Notify;

use Hubzero\Plugin\Plugin;

class Notify extends Plugin
{
    public function onReservationCreate($reservation)
    {
        // ...
    }
}
```

All 329 plugins in `core/plugins` are written this way:
`Plugins\Members\Blog\Blog`, `Plugins\Content\Formatwiki\Formatwiki`.
[`Hubzero\Plugin\Loader::init()`](../../../core/libraries/Hubzero/Plugin/Loader.php)
builds the expected name from the `folder` and `element` columns, requires
`{name}.php` if the class is not already defined, and then checks two names in
order:

| Form | Example |
|---|---|
| `Plugins\{Group}\{Name}\{Name}` | `Plugins\Bookings\Notify\Notify` |
| `plg{Group}{Name}`, in the root namespace | `plgBookingsNotify` |

Each segment of the namespaced form is the column value with its first letter
capitalised, and a hyphen dropped with the letter after it capitalised, so
`editors-xtd` becomes `EditorsXtd`. PHP resolves class and namespace names
case-insensitively, so `Formatwiki` and `FormatWiki` both match. Capitalise
only the first letter of each name anyway: it is the name the loader builds,
and the one every core plugin uses.

The root-namespace `plg` form is still accepted so that a hub's own plugin
written for an earlier release keeps loading unchanged. Nothing in core uses
it. When the loader finds the namespaced class it also registers the `plg`
name as an alias, so code that still names a core plugin the old way — a
`plgMembersBlog::` static call in an override, say — resolves to the new class.

> **Note:** One plugin name is a PHP keyword. `citation/default` cannot
> declare a class called `Default`, so the loader maps the element `default`
> to `DefaultHandler`, and the class is
> `Plugins\Citation\DefaultHandler\DefaultHandler`. The class loader maps
> the segment back to the `default` directory. Do not name a plugin after a
> keyword.

Supporting classes under the plugin — models, helpers, macros — sit in the
same namespace, one level down: `Plugins\Content\Formathtml\Macros\Iframe` is
`core/plugins/content/formathtml/macros/iframe.php`, loaded by the
[class loader](../03-foundation/03-autoloading.md).

> **Note:** The `editors-xtd` group follows the namespaced form —
> `Plugins\EditorsXtd\Image\Image` — but the loader does not construct those
> classes. `Hubzero\Html\Editor` imports the group with autocreation switched
> off (`Plugin::import('editors-xtd', $name, false)`) and instantiates each
> class itself, passing the editor rather than the dispatcher as the subject.

## Facades in a plugin

The entry class is namespaced, so the rule that applies to every other
namespaced file applies here: import each facade you call. Core plugins
import the facade classes themselves:

```php
use Hubzero\Facades\Config;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
```

`use Lang;`, naming the root-namespace alias, works as well.

It is subtler than "it fatals" when you forget: an unimported facade in a
namespaced file usually *works*, because the alias autoloader falls back to
the last segment of the class name, and then fails later in a handful of
specific situations. Read [Facades](../03-foundation/06-facades.md#importing-a-facade)
for which ones, and run
`php core/bin/lint/missing-facade-imports.php app/plugins/bookings/notify` before
you ship.

## The entry guard

A file that runs code when it is included begins with

```php
defined('_HZEXEC_') or die();
```

before anything else executes. Plugin directories are inside the document root
on a default install, so this line is what stops a direct HTTP request from
running the file outside the application. Every view template in
`core/plugins` has it — all 486 of them.

The entry file and the migration do not need it. Each declares a class and
does nothing else, so a direct request defines the class and exits, and
[PSR-1](https://www.php-fig.org/psr/psr-1/#23-side-effects) asks that a file
declaring a class have no other side effects. Four of the 329 entry files and
six of the 339 migrations still carry the line. See
[Constants](../03-foundation/02-constants.md).

## Subdirectories

Everything else is optional and follows fixed names, because the framework
looks for them by convention: `views/{name}/tmpl/{layout}.php` for
[views](05-views.md), `assets/{css,js,img}` for [assets](06-assets.md),
`language/{tag}` for [languages](04-languages.md), `migrations` for
[migrations](01-migrations.md). Models and helpers have no enforced location;
`helpers` beside the entry file is the common arrangement, and it is where a
`Plugins\{Group}\{Name}\Helpers\…` class autoloads from — which is the first
place a plugin view looks when a layout calls a method the view does not have.
See [Views](05-views.md#helpers).
