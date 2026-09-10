<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/database
-->
# Database

Hubzero talks to the database through three layers, each built on the one
below it. The driver ([`Hubzero\Database\Driver`](../../core/libraries/Hubzero/Database/Driver.php))
wraps PDO and runs prepared statements. The
[query builder](#query-builder) assembles statements without you writing SQL
strings. The [ORM](#orm) maps table rows to model objects. Schema changes
are made by [migrations](#migrations).

This page covers the bottom layer: how a connection is configured, how to
get one, and what the driver can do once you have it.

## Configuration

The connection settings live in `app/config/database.php`, which returns a
plain array:

```php
return array(
    'dbtype'   => 'mysql',
    'host'     => '127.0.0.1',
    'user'     => 'hubzero',
    'password' => 'secret',
    'db'       => 'hubzero',
    'dbprefix' => 'jos_',
    'port'     => '3306',
);
```

`Hubzero\Database\DatabaseServiceProvider` reads those values at boot and
registers the resulting driver in the application container under `db`:

<!--include: core/libraries/Hubzero/Database/DatabaseServiceProvider.php:23-40-->

Note the shim on the first line of the closure. A `dbtype` of `mysql` is
turned into the `pdo` driver, and `Driver::getInstance()` then turns `pdo`
back into `mysql`. Both names reach `Hubzero\Database\Driver\Mysql`, which
extends `Hubzero\Database\Driver\Pdo`. The drivers that ship are `mysql`,
`mariadb`, `percona`, `pgsql` and `sqlite`; every one of them is a PDO
driver.

## Getting a connection

Inside the application, ask the container:

```php
$db = App::get('db');
```

That is the connection the query builder and the ORM use by default, and
almost all code should use it too. To open a second connection — a
reporting database, a middleware database — call the factory directly:

```php
$mydb = Hubzero\Database\Driver::getInstance([
    'driver'   => 'mysql',
    'host'     => 'example.org',
    'user'     => 'example',
    'password' => '******',
    'database' => 'mystuff',
    'prefix'   => 'hub_'
]);
```

`getInstance()` hashes the options array and caches the resulting object, so
two calls with identical options hand back the same instance. An unknown
`driver` value raises `Hubzero\Error\Exception\RuntimeException`.

> **Note:** There is no `getInstance()` call that returns "the current"
> connection with no arguments. With an empty array it builds a `mysql`
> driver against whatever defaults the driver class supplies, which is not
> the hub's database. Use `App::get('db')` for the hub connection.

## The table prefix

Tables are written with the placeholder prefix `#__` rather than the real
one. `Driver::replacePrefix()` swaps it for the configured `dbprefix` when
the statement is prepared, so `#__blog_entries` becomes `jos_blog_entries`
on a hub whose prefix is `jos_`. Write `#__` everywhere — in raw SQL, in
query builder calls, in migrations — and the same code runs on a hub with
any prefix.

## Running a statement

`setQuery()` prepares a statement; a load method or `query()` executes it.

```php
$db = App::get('db');

$db->setQuery("SELECT COUNT(*) FROM `#__blog_entries` WHERE `state` = 1");

$total = $db->loadResult();
```

`setQuery()` takes the statement and nothing else. `query()` is an alias for
`execute()` and returns the driver object, not a result resource, so chain a
load method rather than testing its return value.

> **Note:** A failed statement does not return `false`. The PDO driver
> converts the `PDOException` into
> `Hubzero\Database\Exception\QueryFailedException`. Catch that if you need
> to handle a failure.

### Binding values

Never interpolate user input into a statement. Prepare it with `?`
placeholders and bind:

```php
$db->prepare("SELECT * FROM `#__blog_entries` WHERE `scope` = ? AND `state` = ?")
   ->bind(['site', 1]);

$rows = $db->loadObjectList();
```

`bind()` infers a PDO type for each value, and takes an optional second
array of explicit types (`bool`, `null`, `int`, `str`) keyed the same way.
`setQuery()` is `prepare()` without the binding step.

Where a value genuinely cannot be bound — an identifier, say — quote it:

| Method | Purpose |
|---|---|
| `quote($text, $escape = true)` | Wrap a value in single quotes, escaping it first |
| `quoteName($name, $as = null)` | Wrap an identifier in backticks, honouring dot notation and an optional alias |
| `wrap($value)` | Quote a dot-notated identifier, understanding a trailing `AS` and leaving `*` alone |

`q()`, `qn()` and `nq()` are deprecated aliases handled by `__call()`.
`nameQuote()` is not one of them and does not exist.

## Reading results

Every method below prepares nothing itself; call `setQuery()` or
`prepare()` first.

| Method | Returns |
|---|---|
| `loadResult()` | The first column of the first row |
| `loadRow()` | One row as a numerically indexed array |
| `loadAssoc()` | One row as an associative array |
| `loadObject($class = 'stdClass')` | One row as an object |
| `loadColumn($offset = 0)` | One column from every row, as a flat array |
| `loadRowList($key = null)` | Every row as a numeric array, optionally keyed by column `$key` |
| `loadAssocList($key = null, $column = null)` | Every row as an associative array, optionally keyed by `$key` and reduced to `$column` |
| `loadObjectList($key = '', $class = 'stdClass')` | Every row as an object, optionally keyed by `$key` |
| `loadNextRow()` / `loadNextObject($class)` | The next row from an already executed statement, or `false` at the end |

```php
$db->setQuery("SELECT `id`, `title`, `alias` FROM `#__kb_articles` ORDER BY `title`");

foreach ($db->loadObjectList('alias') as $alias => $article)
{
    echo $article->title;
}
```

Each of the `load*` methods executes the statement, reads the whole result,
and frees it. Calling two of them in a row re-runs the statement. If the
execute fails they return `null`.

> **Note:** `getNumRows()` reports the row count of the statement that is
> still open. The `load*` methods free the result when they finish, so call
> `getNumRows()` after `query()` and before any `load*` call.

## Writing rows

For a single row built from an object, the driver has two helpers that
compose the statement for you:

```php
$entry = new stdClass;
$entry->title = 'Release notes';
$entry->state = 1;

$db->insertObject('#__blog_entries', $entry, 'id');   // sets $entry->id
$db->updateObject('#__blog_entries', $entry, 'id');   // $nulls = false skips null fields
```

Both skip array and object properties and any property whose name starts
with an underscore. `insertid()` returns the last auto-increment value.
`getAffectedRows()` returns the row count of the last statement.

For anything more involved, use the [query builder](#query-builder), which
builds and binds the statement for you.

## Transactions and locks

`transactionStart()`, `transactionCommit()` and `transactionRollback()` wrap
the PDO equivalents. `lockTable($table)` and `unlockTables()` are available
for the cases transactions do not cover. Since the driver throws on failure,
the natural shape is a `try`/`catch` around the body with a rollback in the
`catch`.

## Inspecting the schema

Migrations lean on these heavily, and so should any code that has to cope
with more than one schema version:

| Method | Returns |
|---|---|
| `tableExists($table)` | Whether the table is present |
| `tableHasField($table, $field)` | Whether the column is present |
| `tableHasKey($table, $key)` | Whether the index is present |
| `getTableList()` | Every table in the database |
| `getTableColumns($table, $typeOnly = true)` | Column names mapped to types, or to full definitions when `$typeOnly` is `false` |
| `getTableKeys($table)` | Index definitions |
| `getTableCreate($tables)` | `CREATE TABLE` statements |
| `getPrimaryKey($table)` | The primary key column |
| `getEngine($table)` / `setEngine($table, $engine)` | The storage engine |
| `getAutoIncrement($table)` | The next auto-increment value |
| `dropTable($table, $ifExists = true)` | Drops a table |
| `renameTable($old, $new)` | Renames a table |

## Debugging

`enableDebugging()` turns on timing and statement logging;
`disableDebugging()` turns it off again. `getLog()` returns the recorded
statements, `getCount()` the number run, and `getTimer()` the accumulated
time. `toString()` on the driver interpolates the bound values back into the
prepared statement, which is the fastest way to see what actually ran.

## Where to go next

- [Query builder](#query-builder) — building statements without SQL strings.
- [ORM](#orm) — `Relational` models, relationships, and saving rows.
- [Migrations](#migrations) — changing the schema in a repeatable way.
## Query builder

[`Hubzero\Database\Query`](../../core/libraries/Hubzero/Database/Query.php)
assembles a statement from method calls instead of string concatenation, binds
every value it is given, and hands the result to the
[driver](README.md). It sits between raw SQL and the
[ORM](#orm): models forward any method they do not define themselves down to
their query object, so everything on this page works on a model too.

### Getting a query

```php
$query = new \Hubzero\Database\Query;
```

The constructor takes an optional connection and falls back to `App::get('db')`,
so a bare `new Query` uses the hub's database. Pass a driver to run against
another connection:

```php
$query = new \Hubzero\Database\Query($mydb);
```

### Selecting

```php
$query = new \Hubzero\Database\Query;

$entries = $query->select('*')
                 ->from('#__blog_entries')
                 ->whereEquals('scope', 'site')
                 ->whereEquals('state', 1)
                 ->order('publish_up', 'desc')
                 ->limit(10)
                 ->fetch();
```

`select($column, $as = null, $count = false)` adds one column per call. The
second argument is an alias; the third wraps the column in `COUNT()`, and the
string `distinct` makes it `COUNT(DISTINCT …)`:

```php
$total = $query->select('id', 'total', true)
               ->from('#__blog_entries')
               ->fetch('row')
               ->total;
```

`from($table, $as = null)` names the table and optionally aliases it. Calling
`select()` after a plain `*` has been set replaces the `*` rather than adding
to it, which is how the ORM narrows down the default `select *` it seeds onto
every model query.

#### Joins

```php
$query->select('e.*')
      ->select('c.title', 'category_title')
      ->from('#__kb_articles', 'e')
      ->join('#__categories AS c', 'e.category', 'c.id', 'left');
```

`join($table, $leftKey, $rightKey, $type = 'inner')` is the general form.
`innerJoin()`, `leftJoin()`, `rightJoin()` and `fullJoin()` take the same first
three arguments and fix the type. `joinRaw($table, $raw, $type = 'inner')`
takes the whole `ON` condition as a string when the join is not a simple key
comparison.

#### Where clauses

Every method below has an `or…` twin that changes the logical operator from
`AND` to `OR`, and every one takes a trailing `$depth` argument used to build
parenthesised groups.

| Method | Produces |
|---|---|
| `where($column, $operator, $value, $logical = 'and', $depth = 0)` | `column <op> ?` |
| `whereEquals($column, $value)` | `column = ?` |
| `whereIn($column, $values)` | `column IN (?, ?, …)` |
| `whereNotIn($column, $values)` | `column NOT IN (?, ?, …)` |
| `whereLike($column, $value)` | `column LIKE ?`, the value wrapped in `%` |
| `whereIsNull($column)` | `column IS NULL` |
| `whereIsNotNull($column)` | `column IS NOT NULL` |
| `whereRaw($string, $bindings = [], $depth = 0)` | the string verbatim, with `?` placeholders bound from `$bindings` |

> **Note:** `whereLike()` always surrounds the value with `%`. There is no
> option to anchor the pattern; use `where($column, 'LIKE', 'prefix%')` for
> that.

Nested groups are expressed with `$depth`. Anything at depth 1 is wrapped in
parentheses, and `resetDepth($depth)` closes back down to the given level:

```php
$query->select('*')
      ->from('#__blog_entries')
      ->whereEquals('state', 1)
      ->whereEquals('scope', 'site', 1)
      ->orWhereEquals('scope', 'group', 1)
      ->resetDepth()
      ->order('publish_up', 'desc');
```

That is `state = 1 AND (scope = 'site' OR scope = 'group')`.

#### Ordering, grouping, limiting

| Method | Effect |
|---|---|
| `order($column, $dir)` | Adds an `ORDER BY` term |
| `unorder()` | Clears every `ORDER BY` term |
| `group($column)` | Adds a `GROUP BY` term |
| `having($column, $operator, $value)` | Adds a `HAVING` condition |
| `limit($limit)` | Sets the row limit, cast to `int` |
| `start($start)` | Sets the offset, cast to `int` |

### Fetching

`fetch($structure = 'rows', $noCache = false)` runs the statement and returns
the result in one of three shapes:

| `$structure` | Driver method | Result |
|---|---|---|
| `rows` | `loadObjectList()` | An array of `stdClass` objects |
| `row` | `loadObject()` | One `stdClass` object, or `null` |
| `column` | `loadColumn()` | A flat array of the first column |

```php
$titles = $query->select('title')
                ->from('#__blog_entries')
                ->whereEquals('state', 1)
                ->fetch('column');
```

#### Caching

Results are cached in a static array on the class, keyed by a hash of the
structure, the built statement, and the bindings. A second identical fetch in
the same request returns the cached array without touching the database. Pass
`true` as the second argument to bypass the cache for one call:

```php
$query->fetch('rows', true);
```

`Query::purgeCache()` empties the cache for the whole request. The ORM calls it
after every successful `save()`, and exposes `disableCaching()` and
`enableCaching()` on models.

> **Note:** The cache key includes the requested structure, so fetching the
> same statement as `rows` and then as `column` really does run it twice. The
> cache is per-request and in memory only; it is not the platform cache and
> survives nothing.

### Inserting, updating, deleting

Each of these has a long form and a shortcut. The long form ends in
`execute()`, which builds the statement for whichever of `select()`,
`insert()`, `update()` or `delete()` was called most recently.

```php
// Insert
$query->insert('#__blog_entries')
      ->values(['title' => 'Hello', 'scope' => 'site'])
      ->execute();

// Shortcut: returns the new auto-increment id
$id = $query->push('#__blog_entries', ['title' => 'Hello', 'scope' => 'site']);
```

`insert($table, $ignore = false)` and `push($table, $data, $ignore = false)`
both accept an `$ignore` flag that produces `INSERT IGNORE`.

```php
// Update
$query->update('#__blog_entries')
      ->set(['title' => 'Hello again'])
      ->whereEquals('id', 1)
      ->execute();

// Shortcut
$query->alter('#__blog_entries', 'id', 1, ['title' => 'Hello again']);
```

```php
// Delete
$query->delete('#__blog_entries')
      ->whereEquals('id', 1)
      ->execute();

// Shortcut
$query->remove('#__blog_entries', 'id', 1);
```

> **Note:** `remove()` returns `false` without running anything when the
> primary key value is null or empty — a deliberate guard against deleting
> every row in the table. `alter()` has no such guard: it applies exactly the
> one `whereEquals` it builds, so an empty key value updates the rows whose
> key is empty.

### Reuse and inspection

`fetch()` and `execute()` both reset the query afterwards, so one object can be
used for a sequence of unrelated statements. To clear it yourself, `clear()`
with no argument resets everything, and `clear('where')` — or `select`,
`from`, `join`, `set`, `values`, `group`, `having`, `order` — empties one
clause. `deselect()` is shorthand for `clear('select')`.

`toString()`, which `__toString()` also calls, prepares the statement and
interpolates the bindings back in, without executing it:

```php
echo $query->select('*')
           ->from('#__blog_entries')
           ->whereEquals('state', 1);
```

`query($sql, $structure = null)` runs a statement you built yourself, using any
bindings already set on the query. When the statement starts with `select` and
no structure is given, it defaults to `rows`.

### Schema queries

[`Hubzero\Database\Structure`](../../core/libraries/Hubzero/Database/Structure.php)
extends `Query` with one extra method, `getTableColumns($table, $typeOnly = true)`.
With `$typeOnly` false each column comes back as an array of `name`, `type`,
`allownull`, `default` and `pk`. This is what `Relational::getTableColumns()`
uses to decide which attributes on a model correspond to real columns.

### From the query builder to models

Everything above is available on a `Relational` model, which forwards unknown
method calls to its query object and qualifies bare column names in `where`
clauses with the model's table alias. Once a model is involved you usually want
`rows()` or `row()` rather than `fetch()`, because those return models instead
of `stdClass`. See the [ORM](#orm).
## Migrations

A migration is a small PHP class with an `up()` method and a `down()` method
that makes and reverses one change to a hub — a table, a column, an extension
entry, a data fix. [Muse](12-muse.md) finds them, works out which
have not run yet, runs them, and records each run in `#__migrations` so it
never runs the same one twice.

The runner is
[`Hubzero\Content\Migration`](../../core/libraries/Hubzero/Content/Migration.php);
every migration extends
[`Hubzero\Content\Migration\Base`](../../core/libraries/Hubzero/Content/Migration/Base.php).

### Where migrations live

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

### Naming

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

### Writing one

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

### Working with the database

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

### Working with extensions

Registering a component, module, plugin or template is common enough that
`Base` resolves those calls to macro classes in
[`Hubzero\Content\Migration\Macros`](../../core/libraries/Hubzero/Content/Migration/Macros).
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

### Reporting what happened

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

### Failing and skipping

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

### Hooks

A PHP file in a `migrations/hooks` directory is a hook: a class named after
the file, with a `fire()` method and a `$options` array declaring its
`timing` — `onBeforeMigrate`, `onAfterMigrate` or `onAll`. Hooks run on every
full migration, before or after the migrations themselves, and are skipped on
a dry run or a log-only run. `core/migrations/hooks/UpdateTimezoneDatabase.php`
is the one that ships.

### Running migrations

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

### The migrations table

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

### Related

- [Extension requirements](07-extensions/01-extreqs.md) and
  [deploying an extension](07-extensions/04-deployext.md) — where a
  migration fits into shipping an extension.
- [Muse](12-muse.md) and the
  [migration command reference](../reference/muse.md#muse-migration).
## ORM

A model that extends
[`Hubzero\Database\Relational`](../../core/libraries/Hubzero/Database/Relational.php)
maps one database table to one PHP class. It carries a
[query builder](#query-builder) internally and forwards to it any method it does
not define itself, so the whole query API is available on the model. What the
model adds on top is validation rules, automatically populated fields,
relationships to other models, and objects instead of `stdClass` rows.

### A model

The smallest useful model is a class with a namespace property:

```php
namespace Components\Blog\Models;

use Hubzero\Database\Relational;

class Entry extends Relational
{
    protected $namespace = 'blog';
}
```

The table name is derived in the constructor as `#__` + namespace + `_` +
the pluralised, lower-cased short class name, so `Entry` with a namespace of
`blog` becomes `#__blog_entries`. Set `protected $table` explicitly when the
real name does not follow that pattern. The primary key defaults to `id` and
is changed with `protected $pk`.

Here is a real model's declarations — a knowledge base article:

<!--include: core/components/com_kb/models/article.php:29-91-->

| Property | Meaning |
|---|---|
| `$namespace` | Table prefix segment used to build the table name |
| `$table` | The table name, when it is not derivable |
| `$tableAlias` | Alias applied to the table in the seeded query |
| `$pk` | Primary key column, default `id` |
| `$rules` | Field name to validation rule (see below) |
| `$always` | Fields regenerated on every save |
| `$initiate` | Fields generated only when the row is created |
| `$renew` | Fields generated only when an existing row is updated |
| `$parsed` | Fields whose content is run through the content parser |
| `$orderBy`, `$orderDir` | Defaults used by `ordered()`, and reported on the result set |

A model needing constructor work overrides `setup()` rather than
`__construct()`; `Relational` calls it at the end of construction.

### Retrieving rows

| Call | Returns |
|---|---|
| `Model::one($id)` | The model with that primary key, or `false` |
| `Model::oneOrFail($id)` | The same, but throws `RuntimeException` when missing |
| `Model::oneOrNew($id)` | The same, but returns an empty model when missing |
| `Model::oneByAlias($alias)` | The row whose `alias` column matches; an empty model when missing |
| `Model::all()` | A model with a fresh query, ready for constraints |
| `Model::blank()` | A new empty model |
| `->rows()` | A `Rows` collection of models |
| `->row()` | One model — an empty one when nothing matched |
| `->latest($limiter = 'created')` | The newest single row by that column |

```php
use Components\Blog\Models\Entry;

$entries = Entry::all()
    ->whereEquals('scope', 'site')
    ->whereEquals('state', Entry::STATE_PUBLISHED)
    ->ordered()
    ->paginated()
    ->rows();

foreach ($entries as $entry)
{
    echo $entry->title;
}
```

Models implement `IteratorAggregate`, so iterating one fetches for you — but
it iterates a *copy*, leaving the original query intact for a later call.
`ArrayAccess` is implemented too, so `$entry['title']` works alongside
`$entry->title` and `$entry->get('title')`.

> **Note:** `all()` accepts a `$columns` argument and ignores it entirely —
> the body is `return self::blank();`. Use `select()` to narrow the columns.

> **Note:** `one()` returns `false`, not an empty model, when the id does not
> exist, because it seeks into the fetched `Rows` collection. Prefer
> `oneOrFail()` or `oneOrNew()` so you always have an object to work with.

`count()` fetches the rows and counts them; `total()` runs a `COUNT()` query
instead and is what you want for pagination. `paginated($start = 'start',
$limit = 'limit')` reads those request variables, sets the limit clause, and
leaves a `Pagination` object on `$model->pagination`. `ordered($orderBy =
'orderby', $orderDir = 'orderdir')` reads the ordering from the request,
remembers it in user state, and understands `relationship.field` notation by
joining the relationship first. `whereIsMine($column = 'created_by')`
constrains to the current user.

#### Result collections

`rows()` returns a [`Hubzero\Database\Rows`](../../core/libraries/Hubzero/Database/Rows.php)
collection, keyed by primary key where possible. Beyond `count()`, `first()`,
`last()`, `next()` and `prev()`, it offers `seek($pk)`, `sort($field, $asc =
true)`, `fieldsByKey($key)`, `pickRandom($n)`, `latest()`, `toArray()`,
`toJson()`, `save()` and `destroyAll()`.

> **Note:** `Rows::search($key, $value)` returns `true` or `false`, not the
> matching model, despite the name.

### Attributes, transformers and parsed fields

Values that came from the database, or that you intend to save, live in the
attributes array. Read them with `get($key, $default = null)` or the magic
property, and set them with `set($key, $value)` or `set(['a' => 1, 'b' => 2])`.
Assigning a public property directly does *not* put it in the attributes and
so does not save it.

A method named `transformFoo()` makes `$model->foo` return its result instead
of the raw attribute. `com_blog` uses one to hand back the entry's parameters
as a `Registry` rather than a JSON string:

<!--include: core/components/com_blog/models/entry.php:566-584-->

A method named `helperFoo()` makes `$model->foo(...)` call it. Listing a field
in `$parsed` makes `$model->field` return the content run through
`Hubzero\Html\Builder\Content::prepare()`, and `$model->field('raw')` return it
with the format comment stripped.

### Validation

`$rules` maps a field name to one rule, or to several separated by `|`. The
built-in rules are `notempty`, `positive`, `nonzero`, `alpha`, `phone` and
`email`. `save()` calls `validate()` first and returns `false` if it fails;
the messages are then on `getErrors()`.

For anything the built-ins do not cover, register a closure from `setup()`
with `addRule($key, $rule)`. The closure receives the whole attributes array
and returns `false` when valid or a message when not:

<!--include: core/components/com_blog/models/entry.php:112-122-->

> **Note:** `Rules::validate()` iterates the *data*, not the rules. A rule on
> a field that was never set on the model is never evaluated, so `notempty`
> does not make a field required on create — it only rejects an empty value
> that was explicitly supplied.

### Automatic fields

For each field named in `$always`, `$initiate` or `$renew`, the model calls a
method named `automatic` plus the field name in studly case, passing the
current attributes, and stores the return value. `$initiate` runs on insert,
`$renew` on update, and `$always` on both.

`Relational` supplies `automaticCreated()` (now, unless already set),
`automaticCreatedBy()` (the current user id, unless already set) and
`automaticAssetId()` (resolves an `#__assets` entry). Everything else you
write yourself; a slug generator is the usual case:

<!--include: core/components/com_kb/models/article.php:113-124-->

### Saving and deleting

```php
$entry = Entry::oneOrNew($id);
$entry->set([
    'title'   => 'Release notes',
    'content' => 'Everything that changed.',
    'scope'   => 'site'
]);

if (!$entry->save())
{
    // $entry->getError() / getErrors() explain why
}
```

`save()` decides between insert and update from whether the primary key is
set, runs the automatics for that direction, filters the attributes down to
real table columns, purges the query cache, sets the new id back on the model,
and triggers `system.onContentSave` (plus `<table>_new` on a create).
`destroy()` removes the row, deleting any associated asset first and
triggering `system.onContentDestroy`.

`saveAndPropagate()` saves the model and then every relationship attached to
it with `attach($relationship, $models)`, stopping and copying the errors up
on the first failure.

`checkout($userId = null)` and `checkin()` set and clear `checked_out` and
`checked_out_time`, but only when those columns exist on the table;
`isCheckedOut()` reports the state.

### Relationships

A relationship is a public method that returns one of these:

| Method | Relationship |
|---|---|
| `oneToOne($model, $childKey = null, $thisKey = null)` | One row on the other side |
| `oneToMany($model, $relatedKey = null, $thisKey = null)` | Many rows on the other side |
| `belongsToOne($model, $thisKey = null, $parentKey = null)` | The inverse — this row's parent |
| `manyToMany($model, $associativeTable = null, $thisKey = null, $relatedKey = null)` | Many-to-many through a join table |
| `oneToManyThrough($model, $through, $relatedKey = null, $localKey = null)` | Many-to-many where the join table has its own model |
| `oneShiftsToMany($model, $relatedKey = 'scope_id', $shifter = 'scope', $thisKey = null)` | One-to-many where the child also stores which type of parent it has |
| `manyShiftsToMany($model, $associativeTable = null, $thisKey = 'scope_id', $shifter = 'scope', $relatedKey = null)` | The many-to-many equivalent |
| `shifter($shifter = 'scope', $thisKey = 'scope_id')` | The inverse of `oneShiftsToMany` — resolves the parent class from the shifter column |

`$model` is a class name. It is resolved first as given, then against the
current model's own namespace, so a sibling model can be named bare and
anything else needs its full namespaced name. Keys default from the model
names: `oneToMany` looks for `<modelname>_id` on the related table,
`belongsToOne` looks for `<parentmodelname>_id` on this one, and
`manyToMany` guesses an associative table of `#__<namespace>_<name>_<name>`
with the two names sorted alphabetically, so both sides agree.

The knowledge base article declares three:

<!--include: core/components/com_kb/models/article.php:192-200-->

```php
public function comments()
{
    return $this->oneToMany('Comment', 'entry_id');
}

public function votes()
{
    return $this->oneShiftsToMany('Vote', 'object_id', 'type');
}
```

Access them as properties — `$article->comments`, `$article->creator` — and
the model fetches the related rows once and keeps them. Call them as methods
instead when you want to constrain the related query before fetching:
`$article->comments()->whereEquals('state', 1)->rows()`.

`manyToMany` relationships add `connect($ids)`, `disconnect($ids)` and
`sync($ids)` for maintaining the associative table; `sync()` inserts what is
missing and deletes what should no longer be there. `oneToMany` adds
`save($data)`, `saveAll($models)` and `destroyAll()`.

#### Eager loading and constraining

`including()` fetches named relationships alongside the main result set,
avoiding one query per row. It accepts nested names with dots, and a
`[name, closure]` pair to constrain the related query:

```php
$articles = Article::all()
    ->including('creator', ['comments', function ($comment) {
        $comment->whereEquals('state', 1);
    }])
    ->rows();
```

`whereRelatedHas($relationship, $constraint)`, its `orWhereRelatedHas()` twin,
and `whereRelatedHasCount($relationship, $count = 1, $depth = 0, $operator =
'>=')` narrow the main query by what exists on the other side. `forwardTo()`
adds relationships to search when an attribute is missing on this model.

Relationships can also be added from outside the class —
`Relational::registerRelationship($name, $closure)` registers one at runtime,
which is how plugins bolt a relationship onto a core model.

### Connections and caching

Models use the connection in `Relational::$connection`, which is null by
default and so falls through to `App::get('db')`.
`Relational::setDefaultConnection($driver)` points every model at another
driver — useful in tests. `disableCaching()` and `enableCaching()` control
whether a model's fetches consult the query cache, and `purgeCache()` empties
it.

### Trees

[`Hubzero\Database\Nested`](../../core/libraries/Hubzero/Database/Nested.php)
extends `Relational` for nested-set trees, adding `saveAsRoot()`,
`saveAsChildOf($parent)`, `saveAsFirstChildOf()`, `saveAsLastChildOf()`,
`children()` (which is `descendants(1)`) and `descendants($level = null)`. Its
`destroy()` cascades: it removes the node, then every descendant, then closes
the gap left in the tree.
