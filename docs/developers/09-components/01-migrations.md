<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/components/migrations
-->
# Migrations

A component's `migrations` directory holds the code that installs it: the row
in `#__extensions` that makes the hub aware of it, the tables it needs, and any
seed data. Later migrations in the same directory carry the schema forward as
the component changes.

This page covers what a component's migrations do. The mechanics — naming,
the runner, logging, hooks, and the full macro list — are in
[Migrations](../06-database.md#migrations) under Database.

## Why this comes first

**The migration is the installer.** There is no other one. No package
installer reads an XML manifest and creates anything from it; the
**Install**, **Update**, **Discover** and **Database** screens do not exist in
this release (see
[Deploying extensions](../07-extensions/04-deployext.md)). Code arrives as a
directory, by `git` or by hand, and then a migration is run against it.

That makes the first migration you write the one that decides whether the
hub can see the component at all. Its failure mode is quiet and confusing:

- `/index.php?option=com_bookings` renders. The component *works*.
- **Components** in the administrator menu does not list it.
- `Component::params('com_bookings')` returns an empty registry, so every
  setting silently falls back to whatever second argument you passed
  `get()` — or to `null` if you passed none.
- `User::authorise('core.manage', 'com_bookings')` has no asset row to consult.

The component runs because
[`Hubzero\Component\Loader::load()`](../../../core/libraries/Hubzero/Component/Loader.php)
manufactures a default record with `enabled` set to 1 when the query finds no
row. Everything stored *against* the row is what you lose.

## Where they go

```
app/components/com_bookings/
    migrations/
        Migration20260901000000ComBookings.php   register the component
        Migration20260901000100ComBookings.php   create the tables
        Migration20260901000200ComBookings.php   seed the instruments
```

The file name is `Migration` + a fourteen-digit timestamp + the component name
in studly case with `com_` expanded to `Com`. The class name matches the file
name exactly. Files run in sorted order, so the timestamp decides the
sequence.

The runner finds them without being told. On construction
[`Hubzero\Content\Migration`](../../../core/libraries/Hubzero/Content/Migration.php)
scans `core/` and `app/` and every directory under their `components`,
`modules`, `templates` and `plugins` trees, and adds any that has a
`migrations` subdirectory. A component dropped into `app/components/` is
picked up on the next run.

> **Note:** `muse scaffolding create migration -e=com_bookings` writes the stub
> into `core/migrations`, not into the component. Add
> `--install-dir=components/com_bookings` to put it where it belongs.

## Three migrations, not one

Convention splits installation into three files: register the component,
create the tables, seed the data. Nothing enforces the split — one migration
could do all three — but keeping them apart lets you re-run a piece without
re-running the rest, and makes each `down()` obvious.

### Registering the component

`addComponentEntry()` writes the `#__extensions` row, creates the asset row
that permissions hang off, and adds the administrator menu entry. That one
call is the whole of the first migration. `com_kb`'s is the pattern to copy:

<!--include: core/components/com_kb/migrations/Migration20170831000000ComKb.php:8-33-->

`com_bookings` is the same file with the name changed:

```php
use Hubzero\Content\Migration\Base;

defined('_HZEXEC_') or die();

class Migration20260901000000ComBookings extends Base
{
	public function up()
	{
		$this->addComponentEntry('bookings');
	}

	public function down()
	{
		$this->deleteComponentEntry('bookings');
	}
}
```

The `com_` prefix is optional; `addComponentEntry('bookings')` and
`addComponentEntry('com_bookings')` do the same thing. The full signature is
`addComponentEntry($name, $option = null, $enabled = 1, $params = '', $createMenuItem = true)`,
so pass `false` as the fifth argument for a component that should not appear in
the administrator's Components menu.

`deleteComponentEntry()` in `down()` removes the extension row, the asset row,
and the menu entry.

The macros are the way to do this. Writing the `INSERT` yourself is the older
route, and it goes wrong in ways that are hard to see: a missing asset row, a
menu entry with no place in the nested set, a second row when you re-run.
`addComponentEntry()` sets everything the loaders read and is safe to run
twice. The same is true of `addModuleEntry()`, `addPluginEntry()` and
`addTemplateEntry()` for the other kinds.

### Creating the tables

`$this->db` is a full database driver. Guard every statement so the migration
can run against a hub that already has the table:

<!--include: core/components/com_kb/migrations/Migration20170901000000ComKb.php:23-56-->

`com_kb` repeats that block for `#__kb_comments` and `#__kb_votes`, and its
`down()` drops all three. Table names use the `#__` prefix placeholder, which
the driver expands to the hub's configured prefix. Writing `jos_` or `hub_`
into a migration hard-codes one hub's prefix into every other hub — see
[the prefix](../06-database.md).

Name tables after the component: `#__bookings_instruments`,
`#__bookings_reservations`. The ORM depends on it — a `Relational` model with
`protected $namespace = 'bookings'` and the class name `Reservation` resolves
to `#__bookings_reservations` without being told. Get the plural wrong in the
migration and the model looks for a table that is not there. See
[Models](05-models.md).

### Seeding data

A data migration checks whether its rows are already there before inserting,
the same way a table migration checks for its table:

```php
public function up()
{
	if (!$this->db->tableExists('#__bookings_instruments'))
	{
		return;
	}

	$this->db->setQuery(
		"SELECT `id` FROM `#__bookings_instruments` WHERE `alias`='confocal'"
	);

	if ($this->db->loadResult())
	{
		return;
	}

	$now = with(new \Hubzero\Utility\Date('now'))->toSql();

	$this->db->setQuery(
		"INSERT INTO `#__bookings_instruments`
		 (`title`, `alias`, `created`, `created_by`, `state`, `access`)
		 VALUES ('Confocal microscope', 'confocal', "
		 . $this->db->quote($now) . ", 1000, 1, 1)"
	);
	$this->db->query();
}
```

`down()` deletes the same rows by the same criteria.

Seed only what the component cannot work without. Sample content that a hub
then has to delete is worse than none.

## Changing an existing schema

Everything after the install migrations is maintenance, and it follows the
same rule: check first, then change. `com_kb`'s third migration converts its
`DATETIME` columns from `NOT NULL DEFAULT '0000-00-00 00:00:00'` to nullable,
and checks both the table and the column before touching either:

<!--include: core/components/com_kb/migrations/Migration20190221000000ComKb.php:16-58-->

The three checks worth knowing are `tableExists()`, `tableHasField()`, and
`tableHasKey()`.

Never edit a migration that has run anywhere. `#__migrations` records it as
done, so the edit will never be applied on the hubs that already have it, and
the two hubs drift apart with nothing to show for it. Add another migration
instead.

> **Warning:** Write `down()` even when you do not expect to use it. It is the
> only description of what the migration changed that a later reader can trust,
> and `muse migration -d=down` rolls a hub back with it.

## Running them

```bash
php core/bin/muse migration                          # dry run: what would happen
php core/bin/muse migration -f                       # actually run it
php core/bin/muse migration -f -e=com_bookings       # only this component
php core/bin/muse migration -f -d=down -e=com_bookings   # roll it back
```

Run the dry form first. It prints the files it would execute, which is also
the quickest way to find out that it can see none of yours.

> **Warning:** `-e` matches on the **class name suffix**, not on the
> directory. `-e=com_bookings` is turned into `ComBookings` and matched against
> `/Migration[0-9]{14}ComBookings\.php/`. A file named
> `Migration20260901000000Bookings.php`, or `...ComBooking.php`, is in the
> right directory and is skipped without comment — the runner reports nothing
> to do. The same option also validates the extension against
> `^com_[[:alnum:]]+$`, so a name with a second underscore is refused outright.

Every migration that runs is recorded in `#__migrations`, so a second run is a
no-op. That is a record, not a guarantee: a hub restored from a backup or an
extension copied between hubs can present a migration with a schema it did not
expect, which is why the existence checks matter.
