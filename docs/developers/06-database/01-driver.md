<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
-->
# The driver

The driver is the bottom layer: a connection, a prepared statement, and the
methods that read a result back. Use it directly for the statements the
builder cannot express, and for the schema questions a migration has to ask.
Everything above it ends up here.

## Configuration

The connection settings live in `app/config/database.php`, which returns a
plain array:

```php
return [
    'dbtype'   => 'mysql',
    'host'     => '127.0.0.1',
    'user'     => 'hubzero',
    'password' => 'secret',
    'db'       => 'hubzero',
    'dbprefix' => 'jos_',
    'port'     => '3306',
];
```

`Hubzero\Database\DatabaseServiceProvider` reads those values at boot and
registers the resulting driver in the application container under `db`:

<!--include: core/libraries/Hubzero/Database/DatabaseServiceProvider.php:24-39-->

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

## Running a statement

`setQuery()` prepares a statement; a load method or `query()` executes it.

```php
$db = App::get('db');

$db->setQuery("SELECT COUNT(*) FROM `#__bookings_reservations` WHERE `state` = 1");

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
$db->prepare("SELECT * FROM `#__bookings_reservations` WHERE `instrument_id` = ? AND `state` = ?")
   ->bind([$instrumentId, 1]);

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
$db->setQuery("SELECT `id`, `title`, `alias` FROM `#__bookings_instruments` ORDER BY `title`");

foreach ($db->loadObjectList('alias') as $alias => $instrument) {
    echo $instrument->title;
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
$reservation = new stdClass;
$reservation->instrument_id = 12;
$reservation->starts = '2026-09-14 09:00:00';
$reservation->state = 1;

$db->insertObject('#__bookings_reservations', $reservation, 'id');   // sets ->id
$db->updateObject('#__bookings_reservations', $reservation, 'id');   // $nulls = false skips null fields
```

Both skip array and object properties and any property whose name starts
with an underscore. `insertid()` returns the last auto-increment value.
`getAffectedRows()` returns the row count of the last statement.

For anything more involved, use the [query builder](02-query-builder.md), which
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
