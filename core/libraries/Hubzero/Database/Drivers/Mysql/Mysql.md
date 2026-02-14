# MySQL Driver

## Architecture

The database driver system uses a layered inheritance hierarchy:

```
Drivers/Mysql/
  MysqlDriver.php  extends BaseSqlDriver
  MysqlSyntax.php  extends BaseSqlSyntax
  MysqlGrammar.php extends BaseSchemaGrammar
```

`BaseSqlDriver` extends `BasePdoDriver` (PDO connection wrapper), which extends `Driver` (abstract connection lifecycle, query execution, result fetching). All 12 concrete drivers follow this same three-file pattern.

The `BaseSqlDriver` base class defaults follow MySQL conventions (type names, function names) so that MySQL inherits most behavior for free. Other drivers override what differs.

## MySQL as the Baseline

MySQL is the reference implementation. The base class type map, SQL expression defaults, and introspection helpers all use MySQL syntax:

| Property | Base Default | Why |
|----------|-------------|-----|
| `$nowExpression` | `NOW()` | Works on MySQL, PostgreSQL |
| `$randExpression` | `RAND()` | PostgreSQL/SQLite override to `RANDOM()` |
| `$lengthFunction` | `CHAR_LENGTH` | SQLite overrides to `LENGTH` |
| `$ifNullFunction` | `IFNULL` | PostgreSQL overrides to `COALESCE` |
| `$wrapper` | `` `%s` `` | PostgreSQL overrides to `"%s"` |

The abstract type map (`$typeMap`) uses MySQL types as defaults:

| Abstract Type | MySQL Type | Notes |
|---------------|-----------|-------|
| `boolean` | `TINYINT(1)` | No native boolean; `1`/`0` values |
| `tinyInteger` | `TINYINT` | |
| `smallInteger` | `SMALLINT` | |
| `mediumInteger` | `MEDIUMINT` | Not available on PostgreSQL/SQLite |
| `integer` | `INT` | |
| `bigInteger` | `BIGINT` | |
| `string` | `VARCHAR` | Requires length parameter |
| `text` | `TEXT` | 64 KB max |
| `mediumText` | `MEDIUMTEXT` | 16 MB max; PostgreSQL/SQLite map to `TEXT` |
| `longText` | `LONGTEXT` | 4 GB max; PostgreSQL/SQLite map to `TEXT` |
| `json` | `JSON` | Native since 5.7; validated on write |
| `timestamp` | `TIMESTAMP` | |
| `uuid` | `CHAR(36)` | No native UUID type |

Drivers that lack these types override the map. SQLite maps everything to `INTEGER`, `REAL`, `TEXT`, or `BLOB`. PostgreSQL replaces `TINYINT(1)` with `BOOLEAN` and collapses all text sizes to `TEXT`.

## Connection

The constructor builds a PDO DSN from the options array:

```
mysql:host={host};charset=utf8;port={port};dbname={database}
```

- Charset is hardcoded to `utf8` in the DSN to ensure consistent encoding from connection inception
- SSL is supported via `PDO::MYSQL_ATTR_SSL_CA` when `ssl_ca` is provided and the host is not `localhost`
- The DSN prefix is validated — a non-`mysql:` DSN throws `ConnectionFailedException`

## Query Building (MysqlSyntax)

### SELECT with JOINs

MySQL handles UPDATE with JOIN natively in `buildUpdate()`:

```sql
UPDATE table JOIN other ON ... SET column = value WHERE ...
```

### JSON Queries (MySQL 5.7+)

The syntax class provides JSON-aware WHERE clause builders:

```php
// JSON_EXTRACT(column, "$.path") = value
$query->setJsonPathWhere('metadata', 'user.role', '=', 'admin');

// JSON_CONTAINS(column, '"admin"', "$.roles")
$query->setJsonContainsWhere('metadata', 'admin', 'roles');

// JSON_LENGTH(column, "$.items") > 5
$query->setJsonLengthWhere('metadata', '>', 5, 'items');
```

### Date Filtering

```php
// DATE(column) = '2024-01-15'
$query->setDateWhere('created', '=', '2024-01-15', 'date');

// YEAR(column) = 2024
$query->setDateWhere('created', '=', 2024, 'year');
```

Supported parts: `date`, `time`, `year`, `month`, `day`.

### Upsert

Two mechanisms:

- `INSERT IGNORE INTO` — silently skip rows that violate unique constraints
- `INSERT ... ON DUPLICATE KEY UPDATE` — update existing rows on conflict

```sql
-- buildUpsert() generates:
INSERT INTO table (cols) VALUES (vals)
ON DUPLICATE KEY UPDATE col = VALUES(col), ...
```

Other drivers use different syntax (`ON CONFLICT` for PostgreSQL/SQLite, `MERGE INTO` for Informix) but the query builder API is the same.

### Pagination

MySQL uses `LIMIT offset, count` syntax:

```sql
LIMIT 10, 20   -- skip 10, return 20
```

This differs from the SQL:2008 standard (`OFFSET m ROWS FETCH FIRST n ROWS ONLY`) and from PostgreSQL's `LIMIT n OFFSET m` (reversed argument order). The syntax class handles this transparently.

### String Concatenation

MySQL uses `CONCAT()` function calls. PostgreSQL and SQLite use the `||` operator. The syntax class abstracts this via `buildConcat()`.

## DDL Generation (MysqlGrammar)

MySQL's DDL compiler supports inline index definitions within `CREATE TABLE`:

```sql
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100),
    KEY `idx_email` (`email`),
    UNIQUE KEY `uniq_email` (`email`),
    FULLTEXT KEY `ft_name` (`name`),
    CONSTRAINT `fk_org` FOREIGN KEY (`org_id`) REFERENCES `orgs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

PostgreSQL and SQLite cannot define indexes inline — they require separate `CREATE INDEX` statements after the table is created. `MysqlGrammar::compileCreate()` returns a single statement; `PgsqlGrammar::compileCreate()` returns an array (CREATE TABLE + CREATE INDEX for each index).

### ALTER TABLE

`compileAlterTable()` generates a unified ALTER TABLE statement supporting:

- ADD/DROP/MODIFY COLUMN with AFTER/FIRST positioning
- RENAME COLUMN (via CHANGE syntax)
- ADD/DROP INDEX, UNIQUE, FULLTEXT
- ADD/DROP PRIMARY KEY
- ADD/DROP FOREIGN KEY with ON DELETE/UPDATE rules
- ENGINE change, CHARSET/COLLATION change

### Column Modifiers

- `UNSIGNED` — integer types only
- `AUTO_INCREMENT PRIMARY KEY` — single auto-incrementing primary key
- Length specifications: `VARCHAR(255)`, `INT(11)`, `DECIMAL(10,2)`

## Schema Introspection

MySQL uses `SHOW` commands and `information_schema` for introspection:

| Method | Implementation |
|--------|---------------|
| `getTableColumns($table)` | `SHOW FULL COLUMNS FROM` |
| `getTableKeys($table)` | `SHOW KEYS FROM` |
| `getForeignKeys($table)` | `information_schema.KEY_COLUMN_USAGE` + `REFERENTIAL_CONSTRAINTS` |
| `getTableList()` | `SHOW TABLES` |
| `getTableCreate($tables)` | `SHOW CREATE TABLE` |
| `tableExists($table)` | `SHOW TABLES LIKE` |
| `tableHasField($table, $field)` | `SHOW FIELDS FROM` |
| `getCollation()` | `SHOW VARIABLES LIKE 'collation_database'` |
| `getEngine($table)` | `SHOW TABLE STATUS` |
| `getPrimaryKey($table)` | Extracted from `SHOW KEYS WHERE Key_name = 'PRIMARY'` |
| `getPrimaryKeyColumns($table)` | Multi-column PK support from same source |
| `getCharacterSet($table, $field)` | Parsed from `SHOW CREATE TABLE` output |
| `getAutoIncrement($table)` | Parsed from `SHOW CREATE TABLE` AUTO_INCREMENT value |

### ENUM Introspection

- `getEnumValues($table, $column)` — parses `ENUM('a','b','c')` from SHOW COLUMNS
- `addEnumValue($table, $column, $value)` — appends value via ALTER MODIFY
- `removeEnumValue($table, $column, $value)` — rebuilds ENUM without the value

### Views

- `createOrReplaceView($name, $selectSql, $options)` — `CREATE OR REPLACE VIEW` with ALGORITHM, DEFINER, SQL SECURITY INVOKER
- `dropView($name, $ifExists)`, `viewExists($name)`, `getViews()`

## Transaction Support

Nested transactions are implemented via savepoints:

```
transactionStart():
  depth 0 → START TRANSACTION
  depth N → SAVEPOINT SP_N

transactionCommit():
  depth 0 → COMMIT
  depth N → RELEASE SAVEPOINT SP_N

transactionRollback():
  depth 0 → ROLLBACK
  depth N → ROLLBACK TO SAVEPOINT SP_N
```

MySQL requires InnoDB for transaction support. MyISAM silently ignores transaction commands — there is no error, the operations simply have no effect.

## Workarounds and Emulations

These are places where MySQL lacks a feature that other databases have natively, or where MySQL behavior requires special handling.

### FULL OUTER JOIN Emulation

MySQL does not support `FULL OUTER JOIN`. When the query contains a FULL JOIN, `MysqlSyntax::buildSelect()` rewrites it as a UNION:

```sql
-- What the caller writes:
SELECT * FROM a FULL JOIN b ON a.id = b.a_id

-- What MySQL executes:
SELECT * FROM a LEFT JOIN b ON a.id = b.a_id
UNION ALL
SELECT * FROM a RIGHT JOIN b ON a.id = b.a_id WHERE a.id IS NULL
```

The `$fullJoinUnionBuilt` flag suppresses duplicate clause output — when building the UNION, all other clause builders (`buildJoin`, `buildFrom`, `buildWhere`, `buildGroup`, `buildHaving`, `buildOrder`) return empty strings since their content is already embedded in the UNION branches.

**Limitation:** The emulation handles a single FULL JOIN between two base tables. Additional joins in the same query must be INNER or LEFT.

**Performance:** The UNION approach scans both sides twice. On large tables this is measurably slower than a native FULL OUTER JOIN. No index-only optimization is possible for the RIGHT JOIN branch's IS NULL filter.

### Sequence Emulation

MySQL has no native `CREATE SEQUENCE` / `NEXTVAL()`. A `_sequences` table is created on first use (lazy init):

```sql
CREATE TABLE _sequences (
    name VARCHAR(255) PRIMARY KEY,
    current_value BIGINT NOT NULL DEFAULT 0,
    increment_value INT NOT NULL DEFAULT 1,
    table_name VARCHAR(255) NULL
) ENGINE=InnoDB
```

`nextSequenceValue($name)` uses MySQL's `LAST_INSERT_ID(expr)` trick for atomic increment without explicit row-level locking:

```sql
UPDATE _sequences
   SET current_value = LAST_INSERT_ID(current_value + increment_value)
 WHERE name = ?;
SELECT LAST_INSERT_ID();
```

The UPDATE implicitly row-locks the sequence row for the duration of the statement, and `LAST_INSERT_ID(expr)` stores the computed value in the connection's session state so the subsequent SELECT retrieves it atomically.

**Performance:** Each `nextSequenceValue()` call requires two round-trips (UPDATE + SELECT). Native sequences on PostgreSQL or Oracle are single-call operations. Under high concurrency the row lock on `_sequences` serializes all callers for the same sequence name, whereas native sequences are lock-free or use lightweight latches.

`createSequence()` seeds `current_value` as `start - increment` so the first `nextSequenceValue()` call returns the expected start value.

### InnoDB AUTO_INCREMENT Reset

In MySQL 8.0+, `ALTER TABLE t AUTO_INCREMENT = N` is silently ignored when N is less than the internal counter. The counter persists in the InnoDB redo log even after `DELETE FROM`, so a simple ALTER cannot reset it. On MyISAM, ALTER always works.

`setAutoIncrement()` works around this: when the table is empty, `TRUNCATE TABLE` is used instead — it reliably resets the InnoDB counter. Foreign key checks are temporarily disabled since TRUNCATE cannot operate on referenced tables:

```sql
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `table`;
SET FOREIGN_KEY_CHECKS = 1;
ALTER TABLE `table` AUTO_INCREMENT = $value;  -- only if value > 1
```

When the table has data, `ALTER TABLE AUTO_INCREMENT = N` is used directly (it works when N exceeds the current max).

### Boolean as TINYINT(1)

MySQL has no native `BOOLEAN` type. The type map maps `boolean` to `TINYINT(1)` and `formatBooleanLiteral()` returns `'1'` or `'0'`. Application code that writes `true`/`false` gets integer values in the database. When reading, values come back as `'0'` or `'1'` strings — use attribute casting in the ORM layer for proper boolean conversion.

## Cross-Driver Comparison

| Feature | MySQL | PostgreSQL | SQLite | Firebird | Informix |
|---------|-------|-----------|--------|----------|----------|
| Identifier quote | `` ` `` | `"` | `` ` `` | `"` | none |
| Native sequences | No | Yes | No | Yes | Yes |
| FULL OUTER JOIN | Emulated | Native | Native | Native | Native |
| Storage engines | Multiple | Single | Single | Single | Single |
| Transactions | InnoDB only | Yes | Yes | Yes | Yes |
| Foreign keys | InnoDB only | Yes | PRAGMA-gated | Yes | Yes |
| NOW() | `NOW()` | `NOW()` | `datetime('now')` | `CURRENT_TIMESTAMP` | `CURRENT` |
| Random | `RAND()` | `RANDOM()` | `RANDOM()` | `RAND()` | `DBMS_RANDOM.RANDOM` |
| Auto increment | `AUTO_INCREMENT` | `SERIAL` | `AUTOINCREMENT` | Generator | `SERIAL` |
| Boolean type | `TINYINT(1)` | `BOOLEAN` | `INTEGER` | `SMALLINT` | `BOOLEAN` |
| Inline indexes | Yes | No | No | No | No |
| JSON type | Native (5.7+) | Native | No | No | No |
| Upsert syntax | `ON DUPLICATE KEY UPDATE` | `ON CONFLICT DO UPDATE` | `ON CONFLICT DO UPDATE` | `UPDATE OR INSERT` | `MERGE INTO` |
| Pagination | `LIMIT o,c` | `LIMIT c OFFSET o` | `LIMIT c OFFSET o` | `FIRST c SKIP o` | `FIRST c SKIP o` |

## File Summary

| File | Purpose |
|------|---------|
| `MysqlDriver.php` | Connection, introspection, engines, transactions, sequences, views |
| `MysqlSyntax.php` | DML: SELECT, INSERT, UPDATE, DELETE, FULL JOIN emulation, JSON, upsert |
| `MysqlGrammar.php` | DDL: CREATE TABLE, ALTER TABLE, indexes, foreign keys, type mapping |
| `BaseSqlDriver.php` | Abstract SQL contract, shared helpers, type map, expression defaults |
| `BaseSqlSyntax.php` | Base query builder: clauses, bindings, joins, subqueries |
| `BaseSchemaGrammar.php` | Base DDL compiler: column types, modifiers, constraint generation |
