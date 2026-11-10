<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
-->
# Migrations

A migration is a small PHP class with an `up()` method and a `down()` method
that makes and reverses one change to a hub — a table, a column, an extension
entry, a data fix. [Muse](../12-muse.md) finds them, works out which
have not run yet, runs them, and records each run in `#__migrations` so it
never runs the same one twice.

Migrations are how a schema change ships. There is no other supported way.
An extension that needs a table creates it in a migration, not in an
installer, not in a `.sql` file someone is told to load, and not in code that
runs a `CREATE TABLE IF NOT EXISTS` on every request. The reason is that a
hub is upgraded, not reinstalled: the administrator runs
[`muse migration -f`](../../reference/muse.md#muse-migration), every pending
migration in `core`, `app` and every extension runs once in timestamp order,
and the run is recorded. A change made any other way is a change that some
hubs have and others do not.

Write one whenever your extension needs the database to look different from
the way it looked in the last release — including on the very first release,
where the migration is what creates your tables and registers the extension.

The runner is
[`Hubzero\Content\Migration`](../../../core/libraries/Hubzero/Content/Migration.php);
every migration extends
[`Hubzero\Content\Migration\Base`](../../../core/libraries/Hubzero/Content/Migration/Base.php).

## Where migrations live

The runner searches a `migrations` directory under `core` and `app`, and under
every extension directory in both trees:

- `core/migrations` and `app/migrations`
- `core/components/com_blog/migrations`
- `core/modules/mod_login/migrations`
- `core/plugins/system/cache/migrations`
- `core/templates/hzadmin/migrations`

Passing `--vendor` adds `app/vendor/<namespace>/<package>/src/migrations` to
the search. `-r=/some/path` replaces the whole search with the `migrations`
directory under that path.

## Naming

A migration file must be named `Migration` + a fourteen-digit timestamp +
the extension name in studly case, with the `com_`, `mod_`, `plg_` or `tpl_`
prefix expanded into words:

```
Migration20170901000000ComBlog.php
Migration20190221000000ComKb.php
Migration20130101000000PlgMembersDashboard.php
```

Anything that does not match `Migration[0-9]{14}[[:alnum:]]+\.php` is ignored,
silently. The class name must equal the file name; if the class is namespaced,
the runner derives the expected namespace from the path — `Components\Blog\Migrations`
for a file in `core/components/com_blog/migrations` — and looks for it there.
A file whose class it cannot find is logged as a warning and skipped.

Files are run in sorted order, which is why the timestamp comes first.

## Writing one

A migration does one thing and undoes it. `up()` makes the change; `down()`
reverses it. Both halves are yours to write, and both run against a database
whose exact state you do not know, so both start by asking.

Here is the whole of the first migration `com_bookings` would ship — it
creates the reservations table and registers the component:

```php
<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for com_bookings
 **/
class Migration20260910120000ComBookings extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__bookings_reservations')) {
            $query = "CREATE TABLE `#__bookings_reservations` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `instrument_id` int(11) unsigned NOT NULL DEFAULT 0,
                `starts` datetime DEFAULT NULL,
                `ends` datetime DEFAULT NULL,
                `state` tinyint(2) NOT NULL DEFAULT 0,
                `created` datetime DEFAULT NULL,
                `created_by` int(11) unsigned NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `idx_instrument_id` (`instrument_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

            $this->db->setQuery($query);
            $this->db->query();
        }

        $this->addComponentEntry('bookings');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('bookings');

        if ($this->db->tableExists('#__bookings_reservations')) {
            $this->db->setQuery("DROP TABLE `#__bookings_reservations`");
            $this->db->query();
        }
    }
}
```

Three things in there are not optional. The table is written with `#__`. The
`up()` half checks before it creates and the `down()` half checks before it
drops, because a migration is re-run in testing and against hubs that are
already part-way there. And `down()` reverses `up()` in the opposite order.

> **Warning:** Nothing verifies that `down()` undoes `up()`. A migration with
> an empty `down()` is recorded as reversed successfully, and the hub is left
> with the change still applied. If a change genuinely cannot be reversed —
> data that has been discarded — say so with
> `$this->setError('Cannot be reversed', 'warning')` rather than leaving the
> method blank.

### Generating the stub

`muse scaffolding create migration` writes the file and opens it in
`$EDITOR`. `-e` is required and names the extension, which must already
exist:

```bash
php core/bin/muse scaffolding create migration -e=com_bookings \
    --install-dir=components/com_bookings
```

The stub it writes is the class above with both methods empty:

```php
<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
 **/
class Migration20260910120000ComBookings extends Base
{
    /**
     * Up
     **/
    public function up()
    {
    }

    /**
     * Down
     **/
    public function down()
    {
    }
}
```

> **Note:** `-e` only decides the suffix on the class name. Without
> `--install-dir` the file lands in `core/migrations` whatever extension you
> named, which is not where an extension's migrations belong. `--app` writes
> under `app` instead of `core`.

The stub omits the `defined('_HZEXEC_') or die();` guard that every shipped
migration carries. Add it.

If the table already exists on your development hub, muse will write the
migration for you. Name the table with its **real** prefix; the generator
substitutes `#__` in what it writes:

```bash
php core/bin/muse scaffolding create migration for jos_bookings_reservations \
    -e=com_bookings --install-dir=components/com_bookings
```

That fills `up()` with a guarded `CREATE TABLE` taken from the live table,
with `AUTO_INCREMENT` reset to zero, and `down()` with the matching guarded
drop.

## Working with the database

`$this->db` is a [database driver](01-driver.md). Anything the driver can do, a
migration can do:

```php
$this->db->setQuery("ALTER TABLE `#__bookings_reservations` ADD `notes` TEXT");
$this->db->query();
```

Migrations run against hubs at different versions and are re-run in testing,
so guard every change with the schema checks rather than assuming a starting
state. `tableExists()`, `tableHasField()` and `tableHasKey()` are the three
that matter, and a real migration reads like this:

<!--include: core/components/com_blog/migrations/Migration20190221000000ComBlog.php:38-58-->

> **Note:** The methods are `tableExists()`, `tableHasField()` and
> `tableHasKey()`. Older documentation mentions `ifTableExists()`,
> `ifTableHasField()` and `ifTableHasKey()`; those have never existed on this
> driver and calling them raises a `BadMethodCallException`.

`Base` also has protected helpers that build the statement for you:
`generateSafeAddColumns($table, $columns)` and `generateSafeDropColumns()`
produce an `ALTER TABLE` containing only the columns that are actually missing
or actually present, and `queryIfTableExists($table, $query)` runs a
statement only when the table is there.

## Working with extensions

Registering a component, module, plugin or template is common enough that
`Base` resolves those calls to macro classes in
[`Hubzero\Content\Migration\Macros`](../../../core/libraries/Hubzero/Content/Migration/Macros).
The whole of `com_blog`'s first migration is one call each way:

<!--include: core/components/com_blog/migrations/Migration20170831000000ComBlog.php:16-33-->

| Macro | Signature |
|---|---|
| `addComponentEntry` | `($name, $option = null, $enabled = 1, $params = '', $createMenuItem = true)` |
| `deleteComponentEntry` | `($name)` |
| `enableComponent` / `disableComponent` | `($element)` |
| `addPluginEntry` | `($folder, $element, $enabled = 1, $params = '')` |
| `deletePluginEntry` | `($folder, $element = null)` |
| `enablePlugin` / `disablePlugin` | `($folder, $element)` |
| `renamePluginEntry` | `($folder, $element, $name)` |
| `addModuleEntry` | `($element, $enabled = 1, $params = '', $client = 0)` |
| `installModule` | `($module, $position, $always = true, $params = '', $client = 0, $menus = 0)` |
| `deleteModuleEntry` | `($element, $client = null)` |
| `enableModule` / `disableModule` | `($element)` |
| `addTemplateEntry` | `($element, $name = null, $client = 1, $enabled = 1, $home = 0, $styles = null, $protected = 0)` |
| `installTemplateEntry` | `($element, $name = null, $client = 1, $styles = null, $protected = 0)` |
| `deleteTemplateEntry` | `($element, $client = 1)` |
| `enableTemplate` | `($element)` |
| `getParams` | `($element, $returnRaw = false)` |
| `saveParams` | `($element, $params)` |
| `savePluginParams` | `($folder, $element, $params)` |
| `setAssetRules` | `($element, $rules)` |

A component or plugin can add macros of its own with
`Base::registerMacroNamespace($namespace, $paths)`, or register a single one
with `Base::macro($name, $macro)`.

## Reporting what happened

`$this->log($message, $type = 'info')` writes a line to the migration log,
which muse prints and, with `--email`, mails. The types are `info`,
`success`, `warning` and `error`, and each is coloured differently in the
terminal.

For a migration that takes a while, drive the progress indicator through the
`progress` callback:

```php
$this->callback('progress', 'init', ['Running ' . __CLASS__ . ':']);

foreach ($rows as $i => $row) {
    // ... work ...
    $this->callback('progress', 'setProgress', [$i]);
}

$this->callback('progress', 'done');
```

`init` also takes a style and a total for a ratio rather than a percentage:
`['Running …:', 'ratio', 25]`, then `setProgress` with `[4, 25]`. The
callbacks are only registered when muse is running interactively, and are
no-ops otherwise.

## Failing and skipping

`$this->setError($message, $type = 'fatal')` records an error. The type
decides what the runner does with it:

| Type | Effect |
|---|---|
| `fatal` | Logged as an error, recorded as `fatal`, and the whole run stops |
| `warning` | Logged, recorded as `warning`; the run continues |
| `info` | Logged only |
| `skipped` | Logged, recorded as `skipped` |

A migration whose preconditions are simply absent — an optional database that
is not configured, say — should throw instead:

```php
throw new \Hubzero\Content\Migration\SkipMigrationException('Metrics database not available');
```

The runner records the migration as `skipped` rather than run, so it is tried
again on the next migration run. Any `QueryFailedException` or `PDOException`
that escapes `up()` stops the run with the message printed.

## Hooks

A PHP file in a `migrations/hooks` directory is a hook: a class named after
the file, with a `fire()` method and a `$options` array declaring its
`timing` — `onBeforeMigrate`, `onAfterMigrate` or `onAll`. Hooks run on every
full migration, before or after the migrations themselves, and are skipped on
a dry run or a log-only run. `core/migrations/hooks/UpdateTimezoneDatabase.php`
is the one that ships.

## Running migrations

[`muse migration`](../../reference/muse.md#muse-migration) is what runs them, and
it is the command an administrator runs after every update. Run it yourself
before you commit, both ways, on a hub that has the change and on one that
does not.

With no options it is a **dry run**: it lists what would happen and changes
nothing. That is the first thing to do with a migration you have just written,
because it tells you whether the runner found the file at all — a file the
naming rules reject is skipped in silence, and a dry run that lists nothing is
what that looks like.

```bash
php core/bin/muse migration                       # dry run: what would happen
php core/bin/muse migration -f                    # actually do it
php core/bin/muse migration -f -e=com_bookings    # just this extension
php core/bin/muse migration -f -d=down -e=com_bookings   # and reverse it
```

| Option | Effect |
|---|---|
| `-f` | Full run. Without it, everything is a dry run |
| `-d=up` / `-d=down` | Direction; `up` is the default |
| `-e=com_example` | Restrict to one extension: `com_*`, `mod_*`, `plg_group_name`, `tpl_*` or `core` |
| `--file=Migration…php` | Run exactly one file |
| `-a` | List every migration found, not only the pending ones |
| `-i` | Deprecated; now identical to `-a` |
| `-m` | Log only — record the migration as run without running its SQL. Requires `-e` or `--file` |
| `--force` | Run even if the log says it has already been run. Requires `-e` or `--file` |
| `-r=/path` | Use an alternative document root for the search |
| `--group=name` | Run a super group's migrations against its own database |
| `--vendor` | Also search `app/vendor` packages |
| `--email=you@example.org` | Mail the output, if any files were affected |

[`muse migration history`](../../reference/muse.md#muse-migration-history) prints
the contents of the migrations table, and `php core/bin/muse migration help`
prints the full option list. The
[muse reference](../../reference/muse.md#muse-migration) is generated from the
command class.

## The migrations table

Runs are recorded in `#__migrations`, which the runner creates on first use:
`file`, `scope`, `hash`, `direction`, `date`, `action_by` and `status`. The
scope is the path to the migration relative to the document root
(`core/migrations`, `core/components/com_blog/migrations`), so the same file
name in two extensions is tracked separately.

A migration is considered done when the most recent row for that file and
scope has the same direction as the run and a status of `success`. That is
why running `down` before an `up` is refused, and why a `skipped` or
`warning` migration is offered again on the next run.

> **Note:** Down migrations are only as good as you write them. Nothing
> verifies that `down()` undoes `up()`, and the runner will happily record a
> `down` that did nothing.

## Related

- [Extension requirements](../07-extensions/01-extreqs.md) and
  [deploying an extension](../07-extensions/04-deployext.md) — where a
  migration fits into shipping an extension.
- [Muse](../12-muse.md) and the
  [migration command reference](../../reference/muse.md#muse-migration).
