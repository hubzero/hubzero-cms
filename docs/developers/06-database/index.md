<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
-->
# Database

Hubzero talks to the database through three layers, each built on the one
below it, and this page covers all three. The
[driver](01-driver.md) ([`Hubzero\Database\Driver`](../../../core/libraries/Hubzero/Database/Driver.php))
wraps PDO and runs prepared statements. The
[query builder](02-query-builder.md) assembles statements without you writing SQL
strings. The [ORM](04-orm.md) maps table rows to model objects. Schema changes are
made by [migrations](03-migrations.md), which is the only supported way to change a
hub's tables.

The examples that are not taken from a shipped extension use one running
example: a component that books a lab's instruments, `com_bookings`, whose
reservations live in `#__bookings_reservations` and are modelled by
`Components\Bookings\Models\Reservation`.

## Which layer to use

Reach for the ORM. A `Relational` model gives you validation, automatic
fields, relationships and objects instead of anonymous rows, and because it
forwards every method it does not define down to its own query builder, you
lose nothing by starting there. That forwarding is why the examples on this
page mix the two freely: `Reservation::all()->whereEquals('state', 1)->rows()`
is a model call and a builder call in the same chain.

| Use | When |
|---|---|
| [ORM](04-orm.md) | Anything that reads or writes rows of your own table. This is the default. |
| [Query builder](02-query-builder.md) | A report, an aggregate, a join across tables that have no models, or a one-off statement in a controller. |
| [Driver](01-driver.md) | A statement the builder cannot express — `ALTER TABLE`, a stored procedure, a vendor-specific query — and the schema checks a migration needs. |

Write raw SQL only when the layer above genuinely cannot express the
statement. Older code in this tree builds SQL strings by concatenation and
passes them to `setQuery()`; that still runs, but it is not the pattern to
copy. A concatenated string is where prefix bugs and injection bugs come
from, and both of the layers above bind their values for you.

> **Note:** There is a second, older family of table classes in the tree:
> classes extending
> [`Hubzero\Database\Table`](../../../core/libraries/Hubzero/Database/Table.php),
> with `load()`, `bind()`, `check()` and `store()`. The base class is marked
> `@deprecated` and over a hundred of them are still in `core/components`.
> They work. Do not write new ones and do not copy one as a starting point —
> `Relational` replaced them.

## The table prefix

Get this right before anything else, because it fails on someone else's hub
rather than on yours.

Every hub picks its own table prefix at install time. Never write it out.
Write the placeholder `#__` and let the driver substitute the real one:

```php
// Right — runs on any hub
$db->setQuery("SELECT COUNT(*) FROM `#__bookings_reservations` WHERE `state` = 1");

// Wrong — runs only on a hub whose prefix happens to be jos_
$db->setQuery("SELECT COUNT(*) FROM `jos_bookings_reservations` WHERE `state` = 1");
```

`Hubzero\Database\Driver\Pdo::prepare()` passes every statement through
`Driver::replacePrefix()` before handing it to PDO, so the substitution
happens at prepare time and applies to raw SQL, query builder calls and
migrations alike. The scan skips quoted string literals, so a `#__` inside a
bound or quoted value is left alone.

The failure is quiet on the machine you develop on and total everywhere
else. `jos_` is the default prefix, so a literal prefix works on a default
install and on nothing else: the hub that renamed its prefix gets
`Table 'hub.jos_bookings_reservations' doesn't exist`, raised as a
[`QueryFailedException`](../../../core/libraries/Hubzero/Database/Exception/QueryFailedException.php),
usually as a white page in the middle of a page that worked yesterday. Nothing
in the test suite catches it.

> **Warning:** The same rule covers everything that carries a table name —
> `insertObject()`, `updateObject()`, `from()`, `join()`, a model's `$table`
> property, a migration's `CREATE TABLE`, and the `--group` database of a
> super group. If you have written a prefix out anywhere, it is a bug.

If you genuinely need the configured prefix — printing a table name in a
report, say — read it rather than assuming it: `$db->getPrefix()`, or
`Config::get('dbprefix')`.

The rest of the naming rules for a new table — what to call it, its columns
and its indexes — are in
[Database schema conventions](../19-conventions/04-database-schema.md).
