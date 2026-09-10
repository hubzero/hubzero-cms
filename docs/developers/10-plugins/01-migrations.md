<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/plugins/migrations
-->
# Migrations

A plugin does nothing until it has a row in `#__extensions`. Dropping the
files into `core/plugins` or `app/plugins` is not enough:
[`Hubzero\Plugin\Loader::all()`](../../../core/libraries/Hubzero/Plugin/Loader.php)
builds its list from that table, not from the filesystem, so an unregistered
plugin is never required, never constructed, and never bound to an event.

This is the first chapter of the section because it is the first thing to
write. A plugin with no migration behaves exactly like a plugin whose event
never fires and exactly like a plugin with a misspelled method name — nothing
happens, and nothing says why. Write the migration, run it, confirm the plugin
appears in the Plugin Manager, and only then start debugging anything else.

Registration is done by a migration in the plugin's own `migrations`
directory, run by [`muse migration`](../12-muse.md).

## Where migrations live

```
app/plugins/bookings/notify/
    migrations/
        Migration20260210000000PlgBookingsNotify.php
```

The class name is `Migration{timestamp}Plg{Group}{Name}`, and the file is
named after the class. Migrations run in timestamp order across every
extension on the hub, so give a plugin that depends on a component's tables a
later timestamp than the migration that creates them — `plg_bookings_notify`
after `com_bookings`, not before.

> **Note:** See [Migrations](../06-database.md#migrations) for naming
> conventions, the `muse` commands, and the schema helpers on `$this->db`.

## The registration migration

For `plg_bookings_notify`, the whole migration is two calls:

```php
<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

class Migration20260210000000PlgBookingsNotify extends Base
{
	public function up()
	{
		$this->addPluginEntry('bookings', 'notify');
	}

	public function down()
	{
		$this->deletePluginEntry('bookings', 'notify');
	}
}
```

Which is, allowing for the names, the shipped members blog migration:

<!--include: core/plugins/members/blog/migrations/Migration20170831000000PlgMembersBlog.php-->

`addPluginEntry()` and `deletePluginEntry()` are macros resolved by
[`Hubzero\Content\Migration\Base`](../../../core/libraries/Hubzero/Content/Migration/Base.php).
The plugin-related ones are:

| Macro | Signature |
|---|---|
| `addPluginEntry` | `($folder, $element, $enabled = 1, $params = '')` |
| `deletePluginEntry` | `($folder, $element = null)` |
| `enablePlugin` | `($folder, $element)` |
| `disablePlugin` | `($folder, $element)` |
| `renamePluginEntry` | `($folder, $element, $name)` |
| `savePluginParams` | `($folder, $element, $params)` |

`$folder` is the group directory — `bookings`, `content`, `system` — and
`$element` is the plugin's own directory name. Neither carries a prefix; the
`plg_bookings_notify` form is assembled where it is needed.
[`AddPluginEntry`](../../../core/libraries/Hubzero/Content/Migration/Macros/AddPluginEntry.php)
lower-cases both before writing them, so the row always holds
`folder = 'bookings'`, whatever case you passed.

> **Important:** `$folder` must be the name of the directory the plugin is in.
> The loader turns the `folder` column straight into a path, and nothing
> checks that the path exists. Register `bookings` for a plugin living in
> `app/plugins/booking/` and the row is written, the Plugin Manager lists the
> plugin, and it still never loads. See
> [Group and directory must match](02-structure.md#group-and-directory-must-match).

The row is written with `enabled` as given, `access` 1 (Public), `state` 0,
and `client_id` 0. `addPluginEntry()` first checks for an existing row with
that folder and element and returns early if it finds one, so re-running a
migration is safe — and so a second `addPluginEntry()` cannot be used to
change a plugin that is already installed. Use `enablePlugin()`,
`disablePlugin()` or `savePluginParams()` for that.

> **Note:** `deletePluginEntry('bookings')` with no element removes **every**
> plugin in the group. Always pass the element unless that is genuinely what
> you want.

## Shipping default parameters

`addPluginEntry()` takes a fourth argument, stored in the row's `params`
column. Pass a JSON string, or an array, which the macro encodes for you. Use
it when a plugin must arrive with a value other than the manifest default:

```php
public function up()
{
	$this->addPluginEntry('content', 'formatwiki', 1, '{"applyFormat":"0","convertFormat":"1"}');
}
```

For a plugin that is already installed, `savePluginParams()` writes the
`params` column instead. It **replaces** the column outright — it does not
merge — so read the current values first and set only what you are changing.
That is the right tool when a later migration adds a field:

```php
public function up()
{
	$params = $this->getParams('plg_content_formatwiki');
	$params->set('convertFormat', 0);

	$this->savePluginParams('content', 'formatwiki', $params);
}
```

`getParams()` returns a `Hubzero\Config\Registry` unless you pass `true` as
its second argument, in which case you get the raw JSON string.
`savePluginParams()` accepts a `Registry`, an array, or nothing usable — the
third case logs a warning and returns `false` rather than throwing.

> **Warning:** `getParams()` and `savePluginParams()` locate the row by
> splitting `plg_group_element` on underscores and taking the second and third
> pieces. An underscore anywhere in the group or the element breaks that:
> `plg_bookings_lab_notify` is read as group `bookings`, element `lab`, and
> the call quietly reads or writes the wrong row, or none. No plugin shipped
> in `core/plugins` has an underscore in either name. Do not be the first.

## Installing disabled

Pass `0` as the third argument to register a plugin without switching it on.
This is the polite default for anything that changes site behaviour on sight,
or that cannot work until it is configured — an authentication provider, a
content filter, or `plg_bookings_notify`, which has no manager address to mail
until an administrator types one in:

```php
$this->addPluginEntry('bookings', 'notify', 0);
```

The plugin then appears in the Plugin Manager, disabled, which is the
difference between "waiting for you" and the silence of no row at all.

## Ordering

Plugins in a group run in the order given by the `ordering` column, ascending;
`Loader::all()` sorts on it and the dispatcher preserves insertion order for
listeners of equal priority.

`addPluginEntry()` sets the ordering itself: it takes the highest `ordering`
already present in the same folder and adds one, so a newly registered plugin
runs **last** in its group. That is the right default. If your plugin must run
before a specific sibling — a content filter that has to see raw text, for
instance — update the column explicitly in the migration and say why in a
comment, because nothing else in the tree records the dependency.

## Tables

Create any tables the plugin needs in the same migration, and write the
matching `down()`:

```php
public function up()
{
	if (!$this->db->tableExists('#__bookings_notifications'))
	{
		$this->db->query("CREATE TABLE `#__bookings_notifications` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`reservation_id` int(11) NOT NULL DEFAULT 0,
			`sent` datetime DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	}

	$this->addPluginEntry('bookings', 'notify', 0);
}
```

Write `#__` for the table prefix, never a literal `jos_`; see
[Database](../06-database.md#the-table-prefix). Most core plugins reverse only
the registration in `down()` and leave the schema alone, because dropping a
table throws away data that a re-run cannot restore.

## Running them

```bash
muse migration          # dry run
muse migration -f       # apply
muse migration -f -i    # apply, including migrations dated before the last run
```

`-e=plg_bookings_notify` limits a run to one extension, which is what you want
while iterating.

> **Warning:** `-e` matches on the **class name**, not the directory. The
> value is split on underscores, each piece is `ucfirst()`ed, and the result
> is matched against `/Migration[0-9]{14}PlgBookingsNotify\.php/`. A file
> called `Migration20260210000000BookingsNotify.php` sits in the right
> directory and is skipped in silence — the runner reports nothing to do. The
> option itself is validated against
> `^plg_[[:alnum:]]+_[[:alnum:]]+$`, so a group or element containing an
> underscore is refused outright, and the filename must match
> `^Migration[0-9]{14}[[:alnum:]]+\.php$`, so it cannot contain one either.

If the migration runs and the plugin still does not appear, the row went in
under a folder or element you did not expect: check `#__extensions` directly
before assuming the loader is at fault.
