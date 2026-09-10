<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/plugins/structure
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

> **Warning:** An underscore in either name breaks three separate things, none
> of them loudly. `Hubzero\Document\Asset\File` splits `plg_group_element` on
> underscores and reads the second and third pieces as the group and the
> element, so [assets](06-assets.md) resolve to a directory that does not
> exist and silently load nothing. The `getParams` and `saveParams`
> [migration macros](01-migrations.md#shipping-default-parameters) split the
> same string the same way and read or write the wrong row. And
> `muse migration -e=` validates its argument against
> `^plg_[[:alnum:]]+_[[:alnum:]]+$` and refuses the name outright. No plugin
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

The entry file must define a class the loader can find. Two names are
accepted, checked in this order:

| Form | Example |
|---|---|
| `plg{Group}{Name}` | `plgBookingsNotify`, `plgMembersBlog`, `plgContentFormatwiki` |
| `Plugins\{Group}\{Name}` | `Plugins\Bookings\Notify` |

The loader concatenates the two column values without changing their case and
calls `class_exists()` on the result, and PHP resolves class names
case-insensitively, so the capitalisation in your file is not what makes the
match. Follow the convention anyway: only the first letter of each name is
capitalised, so `formatwiki` gives `plgContentFormatwiki` and not
`plgContentFormatWiki`, and every plugin shipped in `core/plugins` is written
that way.

Every shipped plugin uses the first form, in the global namespace. The
namespaced form is supported by the loader but unused; supporting classes
under a plugin — models, helpers, macros — do use `Plugins\Group\Name\…`
namespaces, and are loaded by the framework's class loader.

> **Note:** The `editors-xtd` group is the exception that proves the rule. Its
> classes are named `plgButtonImage`, `plgButtonReadmore`, and so on, which
> matches neither form, so `Hubzero\Plugin\Loader` cannot construct them.
> `Hubzero\Html\Editor` imports that group with autocreation switched off
> (`Plugin::import('editors-xtd', $name, false)`) and instantiates the class
> itself. Do not copy the pattern in a new plugin.

```php
<?php
// No direct access
defined('_HZEXEC_') or die();

class plgBookingsNotify extends \Hubzero\Plugin\Plugin
{
	public function onReservationCreate($reservation)
	{
		// ...
	}
}
```

## Facades in a plugin

The plugin entry class is declared in the **root** namespace, so an
unqualified `Lang::txt()`, `Route::url()` or `Config::get()` in it already
names the alias. No `use` line is needed, and none of the shipped plugins has
one. This is the one place in the tree where the import rule does not bite,
and it is why plugin files look different from component files.

Everything else beside the plugin does declare a namespace — models, helpers,
a `Plugins\Bookings\Notify\Helpers\Recipients` class — and there the rule
applies in full. It is subtler than "it fatals": an unimported facade in a
namespaced file usually *works*, because the alias autoloader falls back to
the last segment of the class name, and then fails later in a handful of
specific situations. Read [Facades](../03-foundation/06-facades.md#importing-a-facade)
for which ones, and run
`php tools/lint/missing-facade-imports.php app/plugins/bookings/notify` before
you ship.

## The entry guard

Plugin files begin with

```php
defined('_HZEXEC_') or die();
```

before any executable code. Plugin directories are inside the document root on
a default install, so this line is the only thing preventing a direct HTTP
request from running the file outside the application. It belongs at the top
of the entry file, the migration, and every view template. See
[Constants](../03-foundation/02-constants.md).

## Subdirectories

Everything else is optional and follows fixed names, because the framework
looks for them by convention: `views/{name}/tmpl/{layout}.php` for
[views](05-views.md), `assets/{css,js,img}` for [assets](06-assets.md),
`language/{tag}` for [languages](04-languages.md), `migrations` for
[migrations](01-migrations.md). Models and helpers have no enforced location;
`helpers` beside the entry file is the common arrangement, and is where a
plugin view looks first when a layout calls a method the view does not have.
