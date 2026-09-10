<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
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

## Where they go

```
core/components/com_kb/
    migrations/
        Migration20170831000000ComKb.php
        Migration20170901000000ComKb.php
        Migration20190221000000ComKb.php
```

The file name is `Migration` + a fourteen-digit timestamp + the component name
in studly case with `com_` expanded to `Com`. The class name matches the file
name exactly. Files run in sorted order, so the timestamp decides the
sequence.

> **Note:** `muse scaffolding create migration -e=com_example` writes the stub
> into `core/migrations`, not into the component. Add
> `--install-dir=components/com_example` to put it where it belongs.

## Three migrations, not one

Convention splits installation into three files: register the component,
create the tables, seed the data. Nothing enforces the split — one migration
could do all three — but keeping them apart lets you re-run a piece without
re-running the rest, and makes each `down()` obvious.

### Registering the component

`addComponentEntry()` writes the `#__extensions` row, creates the asset row
that permissions hang off, and adds the administrator menu entry. It is the
whole of `com_kb`'s first migration:

<!--include: core/components/com_kb/migrations/Migration20170831000000ComKb.php:8-33-->

The `com_` prefix is optional; `addComponentEntry('kb')` and
`addComponentEntry('com_kb')` do the same thing. The full signature is
`addComponentEntry($name, $option = null, $enabled = 1, $params = '', $createMenuItem = true)`,
so pass `false` as the fifth argument for a component that should not appear in
the administrator's Components menu.

`deleteComponentEntry()` in `down()` removes the extension row, the asset row,
and the menu entry.

### Creating the tables

`$this->db` is a full database driver. Guard every statement so the migration
can run against a hub that already has the table:

<!--include: core/components/com_kb/migrations/Migration20170901000000ComKb.php:23-56-->

`com_kb` repeats that block for `#__kb_comments` and `#__kb_votes`, and its
`down()` drops all three. Table names use the `#__` prefix placeholder, which
the driver expands to the hub's configured prefix.

Name tables after the component: `#__kb_articles`, `#__blog_entries`. The ORM
depends on it — a `Relational` model with `protected $namespace = 'kb'` and
the class name `Article` resolves to `#__kb_articles` without being told. See
[Models](05-models.md).

### Seeding data

A data migration checks whether its rows are already there before inserting,
the same way a table migration checks for its table:

```php
public function up()
{
    if (!$this->db->tableExists('#__example_entries'))
    {
        return;
    }

    $this->db->setQuery(
        "SELECT `id` FROM `#__example_entries` WHERE `alias`='sample'"
    );

    if ($this->db->loadResult())
    {
        return;
    }

    $now = with(new \Hubzero\Utility\Date('now'))->toSql();

    $this->db->setQuery(
        "INSERT INTO `#__example_entries`
         (`title`, `alias`, `content`, `created`, `created_by`, `state`, `access`)
         VALUES ('Sample', 'sample', 'Sample content!', "
         . $this->db->quote($now) . ", 1000, 1, 1)"
    );
    $this->db->query();
}
```

`down()` deletes the same rows by the same criteria.

## Changing an existing schema

Everything after the install migrations is maintenance, and it follows the
same rule: check first, then change. `com_kb`'s third migration converts its
`DATETIME` columns from `NOT NULL DEFAULT '0000-00-00 00:00:00'` to nullable,
and checks both the table and the column before touching either:

<!--include: core/components/com_kb/migrations/Migration20190221000000ComKb.php:16-58-->

The three checks worth knowing are `tableExists()`, `tableHasField()`, and
`tableHasKey()`.

> **Warning:** Write `down()` even when you do not expect to use it. It is the
> only description of what the migration changed that a later reader can trust,
> and `muse migration -d=down` rolls a hub back with it.

## Running them

```bash
php core/bin/muse migration                    # dry run: what would happen
php core/bin/muse migration -f                 # actually run it
php core/bin/muse migration -f -e=com_kb       # only this component
php core/bin/muse migration -f -d=down -e=com_kb   # roll it back
```

Every migration that runs is recorded in `#__migrations`, so a second run is a
no-op. That is a record, not a guarantee: a hub restored from a backup or an
extension copied between hubs can present a migration with a schema it did not
expect, which is why the existence checks matter.
