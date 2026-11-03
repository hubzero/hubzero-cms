<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
-->
# Query builder

[`Hubzero\Database\Query`](../../../core/libraries/Hubzero/Database/Query.php)
assembles a statement from method calls instead of string concatenation, binds
every value it is given, and hands the result to the [driver](01-driver.md).

Reach for it when there is no model to reach for: a report that joins tables
belonging to three components, a `COUNT()` for a dashboard, a one-off update
in an administrator controller. It is also the layer you are already using
whenever you chain `whereEquals()` or `order()` onto a model, because a model
forwards any method it does not define itself down to its own query object.
Everything in this section therefore works unchanged on a model.

The gain over a hand-built string is that every value you pass is bound, not
interpolated, so a search box cannot become an injection, and `#__` is
handled for you.

## Getting a query

```php
$query = new \Hubzero\Database\Query;
```

The constructor takes an optional connection and falls back to `App::get('db')`,
so a bare `new Query` uses the hub's database. Pass a driver to run against
another connection:

```php
$query = new \Hubzero\Database\Query($mydb);
```

## Selecting

```php
$query = new \Hubzero\Database\Query;

$reservations = $query->select('*')
                       ->from('#__bookings_reservations')
                       ->whereEquals('instrument_id', 12)
                       ->whereEquals('state', 1)
                       ->order('starts', 'asc')
                       ->limit(10)
                       ->fetch();
```

`select($column, $as = null, $count = false)` adds one column per call. The
second argument is an alias; the third wraps the column in `COUNT()`, and the
string `distinct` makes it `COUNT(DISTINCT …)`:

```php
$total = $query->select('id', 'total', true)
               ->from('#__bookings_reservations')
               ->fetch('row')
               ->total;
```

`from($table, $as = null)` names the table and optionally aliases it. Calling
`select()` after a plain `*` has been set replaces the `*` rather than adding
to it, which is how the ORM narrows down the default `select *` it seeds onto
every model query.

### Joins

```php
$query->select('r.*')
      ->select('i.title', 'instrument_title')
      ->from('#__bookings_reservations', 'r')
      ->join('#__bookings_instruments AS i', 'r.instrument_id', 'i.id', 'left');
```

`join($table, $leftKey, $rightKey, $type = 'inner')` is the general form.
`innerJoin()`, `leftJoin()`, `rightJoin()` and `fullJoin()` take the same first
three arguments and fix the type. `joinRaw($table, $raw, $type = 'inner')`
takes the whole `ON` condition as a string when the join is not a simple key
comparison.

### Where clauses

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
      ->from('#__bookings_reservations')
      ->whereEquals('state', 1)
      ->whereEquals('instrument_id', 12, 1)
      ->orWhereEquals('instrument_id', 13, 1)
      ->resetDepth()
      ->order('starts', 'asc');
```

That is `state = 1 AND (instrument_id = 12 OR instrument_id = 13)`.

### Ordering, grouping, limiting

| Method | Effect |
|---|---|
| `order($column, $dir)` | Adds an `ORDER BY` term |
| `unorder()` | Clears every `ORDER BY` term |
| `group($column)` | Adds a `GROUP BY` term |
| `having($column, $operator, $value)` | Adds a `HAVING` condition |
| `limit($limit)` | Sets the row limit, cast to `int` |
| `start($start)` | Sets the offset, cast to `int` |

## Fetching

`fetch($structure = 'rows', $noCache = false)` runs the statement and returns
the result in one of three shapes:

| `$structure` | Driver method | Result |
|---|---|---|
| `rows` | `loadObjectList()` | An array of `stdClass` objects |
| `row` | `loadObject()` | One `stdClass` object, or `null` |
| `column` | `loadColumn()` | A flat array of the first column |

```php
$ids = $query->select('id')
             ->from('#__bookings_reservations')
             ->whereEquals('state', 1)
             ->fetch('column');
```

### Caching

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

## Inserting, updating, deleting

Each of these has a long form and a shortcut. The long form ends in
`execute()`, which builds the statement for whichever of `select()`,
`insert()`, `update()` or `delete()` was called most recently.

```php
// Insert
$query->insert('#__bookings_reservations')
      ->values(['instrument_id' => 12, 'state' => 1])
      ->execute();

// Shortcut: returns the new auto-increment id
$id = $query->push('#__bookings_reservations', ['instrument_id' => 12, 'state' => 1]);
```

`insert($table, $ignore = false)` and `push($table, $data, $ignore = false)`
both accept an `$ignore` flag that produces `INSERT IGNORE`.

```php
// Update
$query->update('#__bookings_reservations')
      ->set(['state' => 0])
      ->whereEquals('id', 1)
      ->execute();

// Shortcut
$query->alter('#__bookings_reservations', 'id', 1, ['state' => 0]);
```

```php
// Delete
$query->delete('#__bookings_reservations')
      ->whereEquals('id', 1)
      ->execute();

// Shortcut
$query->remove('#__bookings_reservations', 'id', 1);
```

> **Note:** `remove()` returns `false` without running anything when the
> primary key value is null or empty — a deliberate guard against deleting
> every row in the table. `alter()` has no such guard: it applies exactly the
> one `whereEquals` it builds, so an empty key value updates the rows whose
> key is empty.

## Reuse and inspection

`fetch()` and `execute()` both reset the query afterwards, so one object can be
used for a sequence of unrelated statements. To clear it yourself, `clear()`
with no argument resets everything, and `clear('where')` — or `select`,
`from`, `join`, `set`, `values`, `group`, `having`, `order` — empties one
clause. `deselect()` is shorthand for `clear('select')`.

`toString()`, which `__toString()` also calls, prepares the statement and
interpolates the bindings back in, without executing it:

```php
echo $query->select('*')
           ->from('#__bookings_reservations')
           ->whereEquals('state', 1);
```

`query($sql, $structure = null)` runs a statement you built yourself, using any
bindings already set on the query. When the statement starts with `select` and
no structure is given, it defaults to `rows`.

## Schema queries

[`Hubzero\Database\Structure`](../../../core/libraries/Hubzero/Database/Structure.php)
extends `Query` with one extra method, `getTableColumns($table, $typeOnly = true)`.
With `$typeOnly` false each column comes back as an array of `name`, `type`,
`allownull`, `default` and `pk`. This is what `Relational::getTableColumns()`
uses to decide which attributes on a model correspond to real columns.

## From the query builder to models

Everything above is available on a `Relational` model, which forwards unknown
method calls to its query object and qualifies bare column names in `where`
clauses with the model's table alias. Once a model is involved you usually want
`rows()` or `row()` rather than `fetch()`, because those return models instead
of `stdClass`. See the [ORM](04-orm.md).
