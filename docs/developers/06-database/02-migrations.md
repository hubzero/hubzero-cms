<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/database/migrations
-->
# Migrations

A migration is a small PHP class with an `up()` method and a `down()` method
that makes and reverses one change to a hub — a table, a column, an extension
entry, a data fix. [Muse](../12-muse/README.md) finds them, works out which
have not run yet, runs them, and records each run in `#__migrations` so it
never runs the same one twice.

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

`muse scaffolding create migration -e=com_example` writes a stub from the
template and opens it in `$EDITOR`. The result is:

```php
<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for ...
 **/
class Migration20260901120000ComExample extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // Changes to be made ...
    }

    /**
     * Down
     **/
    public function down()
    {
        // Reverse the changes ...
    }
}
```

> **Note:** `-e` only decides the suffix on the class name. The file is
> written to `core/migrations` regardless. Pass `--app` to write into `app`
> instead, and `--install-dir=components/com_example` to put it in the
> extension's own directory — which is where an extension's migrations
> belong.

`muse scaffolding create migration for jos_example_things -e=com_example`
writes the migration for you, filling `up()` with a `CREATE TABLE` taken from
the live table, with the prefix replaced by `#__` and `AUTO_INCREMENT` reset
to zero, and `down()` with the matching drop.

## Working with the database

`$this->db` is a [database driver](README.md). Anything the driver can do, a
migration can do:

```php
$this->db->setQuery("ALTER TABLE `#__blog_entries` ADD `summary` TEXT");
$this->db->query();
```

Migrations run against hubs at different versions and are re-run in testing,
so guard every change with the schema checks rather than assuming a starting
state. `tableExists()`, `tableHasField()` and `tableHasKey()` are the three
that matter, and a real migration reads like this:

<!--include: core/components/com_blog/migrations/Migration20190221000000ComBlog.php:38-59-->

> **Note:** The methods are `tableExists()`, `tableHasField()` and
> `tableHasKey()`. Older documentation mentions `ifTableExists()`,
> `ifTableHasField()` and `ifTableHasKey()`; those have never existed on this
> driver and calling them raises a `BadMethodCallException`.

`Base` also has protected helpers that build the statement for you:
`_generateSafeAddColumns($table, $columns)` and `_generateSafeDropColumns()`
produce an `ALTER TABLE` containing only the columns that are actually missing
or actually present, and `_queryIfTableExists($table, $query)` runs a
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

foreach ($rows as $i => $row)
{
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

```bash
php core/bin/muse migration
```

With no options this is a dry run: it lists what would happen and changes
nothing. Add `-f` to actually run it:

```bash
php core/bin/muse migration -f
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

`php core/bin/muse migration history` prints the contents of the migrations
table, and `php core/bin/muse migration help` prints the full option list.

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
- [Muse](../12-muse/README.md) and the
  [migration command reference](../../reference/muse/migration.md).
