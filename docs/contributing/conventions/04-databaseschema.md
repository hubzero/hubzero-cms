<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/conventions/databaseschema
-->
# Database Schema Conventions

Schema changes reach a hub through a [migration](../../developers/06-database/02-migrations.md),
never through a `.sql` file someone runs by hand. This chapter covers the names
a migration should use.

## The table prefix placeholder

Every table name is written with `#__` where the hub's prefix belongs:

```sql
SELECT `id` FROM `#__blog_entries`
```

`Hubzero\Database\Driver::replacePrefix()` rewrites `#__` to the prefix from the
hub's configuration before the statement is sent. The default prefix is `jos_`,
which is why a hardcoded `jos_` appears to work on most hubs and fails on any
hub installed with a different one.

> **Warning:** Never write a literal prefix in a query. It is a live bug class
> in this codebase — four commits on this branch replaced hardcoded prefixes in
> `com_config`, `com_installer`, `plg_groups_forum` and `plg_groups_resources`.
> The only `jos_` strings left in core are in comments and log messages.

`#__` works in raw SQL passed to `$db->setQuery()`, in the query builder's
`from()` and `join()`, and in a `Relational` model's `$table` property.

## Table names

Lowercase, words separated by underscores, prefixed with the extension that
owns the table, and the last word plural:

```
#__blog_entries
#__blog_comments
#__answers_questions
#__answers_responses
#__citations_authors
#__courses_grade_policies
```

The prefix keeps 438 core tables from colliding and makes it obvious which
extension to look in when a query goes wrong. The oldest tables predate the
convention — `#__users`, `#__categories`, `#__assets`, `#__content` — and are
not renamed.

The rule is not only a convention: a `Hubzero\Database\Relational` model that
does not set `$table` builds one from its own name.

```php
$namespace   = (!$this->namespace ? '' : $this->namespace . '_');
$plural      = \Hubzero\Utility\Inflector::pluralize(strtolower($this->getModelName()));
$this->table = $this->table ?: '#__' . $namespace . $plural;
```

`Components\Blog\Models\Entry` with `protected $namespace = 'blog';` therefore
reads `#__blog_entries`. Name the table to match the model and you write no
`$table` property at all.

Where the name is several words, only the last is plural: `application_functions`,
`application_function_roles`. Core is not uniform here; `#__answers_questions`
and `#__cart_carts` pluralise more than the last word. Follow the rule in new
tables and leave the existing names alone.

A table that links two others carries the `_assoc` suffix on the owning
extension's name: `#__citations_assoc`, `#__citations_sponsors_assoc`,
`#__author_assoc`.

## Column names

Lowercase, singular, words separated by underscores: `first_name`,
`order_amount`, `created_by`.

A set of column names recurs across core and carries the same meaning
everywhere. Use them rather than inventing a synonym:

| Column | Meaning |
|---|---|
| `id` | surrogate primary key, `int unsigned AUTO_INCREMENT` |
| `created`, `created_by` | creation timestamp and the user id behind it |
| `modified`, `modified_by` | last change and who made it |
| `state` | publication state; `Relational` defines 0 unpublished, 1 published, 2 deleted, and a model may add its own above those |
| `access` | the viewing level id the row requires |
| `ordering` | manual sort position |
| `params` | the row's own settings, JSON |
| `alias` | the URL-safe form of the title |
| `publish_up`, `publish_down` | the window the row is visible in |
| `checked_out`, `checked_out_time` | edit lock |

A foreign key is the singular of the table it points at plus `_id`: `entry_id`
in `#__blog_comments` points at `#__blog_entries`, `created_by` at `#__users`.

## Indexes

An index is named `idx_` plus the columns it covers:

```sql
ALTER TABLE `#__my_table` ADD INDEX `idx_created_by` (`created_by`);
```

For a multi-column index, list the columns in order of cardinality and join
their names:

```sql
ALTER TABLE `#__my_table` ADD INDEX `idx_category_referenceid` (`category`, `reference_id`);
```

A unique index is `uidx_`; a fulltext index is `ftidx_`:

```sql
ALTER TABLE `#__my_table` ADD UNIQUE `uidx_alias` (`alias`);
ALTER TABLE `#__my_table` ADD FULLTEXT `ftidx_content` (`content`);
```

Core holds 1,162 `idx_`, 53 `ftidx_` and 25 `uidx_` index names, so this one is
followed closely.

## A table in a migration

Put the whole definition in one `CREATE TABLE`, guarded by `tableExists()` so
the migration is safe to re-run:

<!--include: core/components/com_blog/migrations/Migration20170901000000ComBlog.php:23-52-->

Give every column an explicit `DEFAULT`. A `NOT NULL` column with no default
makes an insert that omits it fail; `Migration20260129000000Core` exists only to
undo three of those in `#__xprofiles`.

Older migrations write `ENGINE=MyISAM`; new tables should use `ENGINE=InnoDB`
for foreign keys and transactions, unless the table needs a `FULLTEXT` index on
a MySQL old enough not to support one on InnoDB.

The `down()` method reverses what `up()` did. Where reversing would destroy
data, say so in `down()` and do nothing rather than dropping the column.
