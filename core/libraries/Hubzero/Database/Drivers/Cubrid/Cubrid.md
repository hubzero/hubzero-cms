# CUBRID Driver

**This is a proof-of-concept driver.** It passed all tests when originally written but is not maintained on a regular testing cadence. It should not be considered production-ready without re-validation against the current test suite.

**This driver requires a patched `pdo_cubrid` extension.** The stock PECL `PDO_CUBRID` 10.1.0.0004 does not compile on PHP 8.3 due to extensive API changes between PHP 7.x and 8.x. The extension was ported to the PHP 8.3 PDO ABI — function signatures, string management, hash iteration, LOB stream handling, and memory management were all updated across `cubrid_driver7.c` and `cubrid_statement7.c`. See the [Technical Notes](#technical-notes-pdo_cubrid-php-83-port) section for details.

## Architecture

```
Drivers/Cubrid/
  CubridDriver.php  extends BaseSqlDriver
  CubridSyntax.php  extends BaseSqlSyntax
  CubridGrammar.php extends MysqlGrammar
```

`BaseSqlDriver` extends `BasePdoDriver` (PDO connection wrapper), which extends `Driver` (abstract connection lifecycle, query execution, result fetching). All 12 concrete drivers follow this same three-file pattern.

CUBRID is the most MySQL-compatible of the non-MySQL drivers. The grammar extends `MysqlGrammar` (not `BaseSchemaGrammar`) and the driver reuses many MySQL-style introspection methods (`SHOW COLUMNS`, `SHOW TABLES`, `SHOW KEYS`). The driver overrides type maps, ENUM/SET handling, transaction management, and has workarounds for several `pdo_cubrid` quirks.

## Expression Overrides

| Property | CUBRID | Base (MySQL) Default |
|----------|--------|---------------------|
| `$wrapper` | `` `%s` `` | `` `%s` `` |

CUBRID uses MySQL-style backtick quoting. `$nowExpression`, `$randExpression`, `$lengthFunction`, and `$ifNullFunction` are not overridden — CUBRID supports `NOW()`, `RAND()`, `CHAR_LENGTH`, and `IFNULL` natively.

## Type System

CUBRID uses `STRING` (variable-length up to 1GB) for text types. No native `BOOLEAN` — use `TINYINT`. No `UNSIGNED` modifier. No `ENUM`/`SET` types — these are converted to `VARCHAR` with lengths calculated from declared values.

| Abstract Type | CUBRID Type | Notes |
|---------------|------------|-------|
| `boolean` | `TINYINT` | No native BOOLEAN |
| `tinyInteger` | `TINYINT` | No display widths |
| `smallInteger` | `SMALLINT` | |
| `mediumInteger` | `MEDIUMINT` | |
| `integer` | `INT` | |
| `bigInteger` | `BIGINT` | |
| `string` | `VARCHAR` | |
| `char` | `CHAR` | |
| `tinyText` | `VARCHAR(255)` | |
| `text` .. `longText` | `STRING` | Up to 1GB |
| `float` | `FLOAT` | |
| `double` | `DOUBLE` | |
| `decimal` | `DECIMAL` | |
| `date` | `DATE` | |
| `time` | `TIME` | |
| `datetime` | `DATETIME` | Returns `.000` milliseconds |
| `timestamp` | `TIMESTAMP` | |
| `year` | `SMALLINT` | |
| `binary` | `BIT VARYING` | |
| `json` | `STRING` | No native JSON type |
| `uuid` | `CHAR(36)` | |

No `UNSIGNED` modifier. No `ENUM`/`SET` types — the grammar calculates the minimum `VARCHAR` length from declared values (ENUM uses max value length, SET uses total).

### Auto-Increment via AUTO_INCREMENT

```sql
`id` INT AUTO_INCREMENT, PRIMARY KEY (`id`)
```

CUBRID uses MySQL-style `AUTO_INCREMENT` but requires a separate `PRIMARY KEY` clause — the auto-increment declaration does not implicitly create a primary key. CUBRID also names primary keys with generated identifiers (`pk_tablename_id`) instead of the canonical `PRIMARY`; the driver normalizes this.

## Connection

The constructor builds a PDO DSN:

```
cubrid:host={host};port={port};dbname={database};charset=utf8
```

Default port is 33300. Charset is always `utf8` (not `utf8mb4`).

Post-connection configuration:

- **`SET NAMES utf8`** — avoids "incompatible code sets" errors with REGEXP and charset-sensitive operations

**Important:** PDO constructor `driver_options` must not be passed as the fourth parameter. `PDO_CUBRID` appends numeric option keys into CCI URL parameters, producing `SQLSTATE[HY000] [-30019] CLIENT, Invalid connection string`. Use a plain constructor and call `setAttribute()` after connecting.

Uses the `pdo_cubrid` PHP extension (PHP 8.3 ported build).

## Identifier Quoting

CUBRID uses MySQL-style backtick quoting. Column names that are reserved words (e.g., `value`, `key`, `order`) must be quoted — CUBRID is stricter about reserved keywords than MySQL. The syntax class wraps all column names in SELECT clauses for safety.

## Query Building (CubridSyntax)

### Pagination

CUBRID uses MySQL-style `LIMIT offset, count`:

```sql
SELECT * FROM `users` LIMIT 10, 20
```

### INSERT / Upsert

CUBRID uses `ON DUPLICATE KEY UPDATE` (like MySQL) but does **not** support the `VALUES()` function to reference the would-be inserted value:

```sql
-- MySQL: INSERT INTO t (a, b) VALUES (?, ?)
--        ON DUPLICATE KEY UPDATE a = VALUES(a)
-- CUBRID: INSERT INTO t (a, b) VALUES (?, ?)
--         ON DUPLICATE KEY UPDATE a = ?
```

Values must be bound again for the UPDATE clause. Bulk upserts are not supported and fall back to individual per-row upserts.

### INSERT IGNORE

CUBRID does not support `INSERT IGNORE` syntax. The driver emulates it using `ON DUPLICATE KEY UPDATE` with a no-op update (`col = col`):

```sql
INSERT INTO `users` (`id`, `name`) VALUES (?, ?)
ON DUPLICATE KEY UPDATE `id` = `id`
```

Multi-row INSERT IGNORE falls back to individual inserts.

### Bulk INSERT

Standard multi-row VALUES syntax is supported for non-ignore inserts:

```sql
INSERT INTO `users` (`name`, `email`)
VALUES (?, ?), (?, ?), (?, ?)
```

### EXISTS/NOT EXISTS Rewriting

CUBRID does not support subqueries in `JOIN ON` clauses. The syntax class detects EXISTS/NOT EXISTS predicates in JOIN conditions and rewrites them:

- **Correlated EXISTS** — rewritten as an INNER JOIN on a derived table with `SELECT DISTINCT`
- **Correlated NOT EXISTS** — rewritten as a LEFT JOIN with `IS NULL` in WHERE
- **Non-correlated** — deferred to the WHERE clause

### JSON Support

CUBRID supports MySQL-style JSON functions (`JSON_EXTRACT`, `JSON_CONTAINS`, `JSON_LENGTH`). The syntax class delegates to the shared MySQL-style JSON where-clause builders.

### Date Filtering

```php
// DATE(column) = '2024-01-15'
$query->setDateWhere('created', '=', '2024-01-15', 'date');

// YEAR(column) = 2024
$query->setDateWhere('created', '=', 2024, 'year');
```

### Function Translation

CUBRID supports most MySQL functions natively. No translation is needed for:

| Function | Support |
|----------|---------|
| `CONCAT(a, b, c)` | Native |
| `IFNULL(a, b)` | Native |
| `NOW()` | Native |
| `YEAR(col)` | Native |
| `DATE_FORMAT(col, fmt)` | Native |
| `DATE_ADD` / `DATE_SUB` | Native |
| `SUBSTRING_INDEX` | Native |
| `UNIX_TIMESTAMP` | Native |
| `REGEXP` | Native |

### FULL OUTER JOIN

CUBRID does not support `FULL OUTER JOIN`. The syntax class emulates it using `UNION ALL` of a LEFT JOIN branch and a RIGHT JOIN anti-join branch, the same strategy used by the MySQL driver.

### UPDATE with JOIN

CUBRID supports MySQL-style `UPDATE ... JOIN`:

```sql
UPDATE `table` JOIN `other` ON ... SET `col` = ? WHERE ...
```

### TRUNCATE

Standard syntax:

```sql
TRUNCATE TABLE `table`
```

## DDL Generation (CubridGrammar)

### CREATE TABLE

The grammar extends `MysqlGrammar` but strips MySQL-specific options:

```sql
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100),
  PRIMARY KEY (`id`)
)
```

No ENGINE, CHARSET, or COLLATION clauses. UNSIGNED is stripped from all column types. Integer display widths (e.g., `TINYINT(1)`) are stripped. Zero-date defaults are converted to `DEFAULT NULL`.

### ALTER TABLE

CUBRID supports most MySQL ALTER TABLE syntax:

- **ADD COLUMN** — `ALTER TABLE t ADD COLUMN col type`
- **DROP COLUMN** — `ALTER TABLE t DROP COLUMN col`
- **MODIFY COLUMN** — `ALTER TABLE t MODIFY COLUMN col type`
- **CHANGE COLUMN** — `ALTER TABLE t CHANGE COLUMN old new type`
- **RENAME COLUMN** — `ALTER TABLE t RENAME COLUMN old TO new`
- **ADD/DROP INDEX** — standard syntax
- **ADD/DROP PRIMARY KEY** — standard syntax
- **Column positioning** — `FIRST`, `AFTER col` supported

### ENUM/SET Conversion

CUBRID has no ENUM or SET types. The grammar converts them to VARCHAR:

- `ENUM('a','bb','ccc')` → `VARCHAR(3)` (max value length)
- `SET('a','bb','ccc')` → `VARCHAR(7)` (sum of lengths + separators)

### Foreign Keys

CUBRID does not support `ON UPDATE CASCADE`. The driver converts it to `ON UPDATE NO ACTION`. `ON DELETE CASCADE` is supported.

### Fulltext Indexes

CUBRID does not support MySQL `FULLTEXT` index syntax. `addFulltextIndex()` creates a regular index instead.

## Schema Introspection

CUBRID uses MySQL-compatible `SHOW` commands for most introspection:

| Method | Implementation |
|--------|---------------|
| `getTableColumns($table)` | `SHOW COLUMNS FROM table` |
| `getTableKeys($table)` | `SHOW KEYS FROM table` (normalizes `pk_` prefix to `PRIMARY`) |
| `getForeignKeys($table)` | `PDO::cubrid_schema(CUBRID_SCH_IMPORTED_KEYS)` |
| `getTableList()` | `SHOW TABLES` |
| `getTableCreate($tables)` | `SHOW CREATE TABLE` |
| `tableExists($table)` | `SHOW TABLES LIKE` |
| `getPrimaryKey($table)` | `SHOW COLUMNS` filtered for `Key = PRI` |
| `getAutoIncrement($table)` | `db_serial` system table query |
| `getCollation()` | `SHOW VARIABLES` |
| `getEngine($table)` | Returns `'CUBRID'` (single storage engine) |

### CUBRID-Specific Naming

CUBRID uses `pk_tablename_colname` for primary keys (not `PRIMARY`). The driver normalizes to `PRIMARY` for framework consistency. Unique indexes use `u_tablename_colname` prefix.

### SHOW Differences from MySQL

- `SHOW COLUMNS` supported, `SHOW FULL COLUMNS` is not
- `SHOW FIELDS` not supported (use `SHOW COLUMNS`)
- `SHOW KEYS` does not support `WHERE` clause
- `SHOW ENGINES` not supported (single engine)
- `SHOW TABLE STATUS` not supported

### ENUM (Not Supported)

CUBRID has no ENUM type. The `getEnumValues()` and `addEnumValue()` methods parse `ENUM(...)` type strings from `SHOW COLUMNS` output — this handles legacy tables migrated from MySQL that retain ENUM syntax.

### Views

- `createOrReplaceView()` — `CREATE OR REPLACE VIEW` (native support)
- `dropView()` — `DROP VIEW IF EXISTS`
- `viewExists()`, `getViews()` — via `SHOW TABLES`

### Sequences (Emulated)

CUBRID has no native sequence support. The driver emulates sequences using a `_sequences` table:

- `createSequence()` — INSERT into `_sequences`
- `dropSequence()` — DELETE from `_sequences`
- `sequenceExists()` — COUNT from `_sequences`
- `nextSequenceValue()` — atomic increment using `SELECT FOR UPDATE` within transaction
- `currentSequenceValue()` — simple SELECT

### Feature Detection

| Method | Returns |
|--------|---------|
| `getEngine()` | `'CUBRID'` |
| `supportsSequences()` | true (emulated) |
| `supportsUnsigned()` | false |
| `autoIncrementIncludesPrimaryKey()` | false |

## Transaction Support

CUBRID does not support `START TRANSACTION` or `BEGIN` as SQL statements. The driver uses PDO's native `beginTransaction()`:

```
transactionStart():
  depth 0 → PDO::beginTransaction()
  depth N → SAVEPOINT SP_N

transactionCommit():
  depth 0 → PDO::commit()
  depth N → (no-op; CUBRID has no RELEASE SAVEPOINT)

transactionRollback():
  depth 0 → PDO::rollBack()
  depth N → ROLLBACK TO SAVEPOINT SP_N
```

Note: CUBRID does not support `RELEASE SAVEPOINT`. Savepoints are automatically released when the outer transaction commits.

`lockTable()` uses `LOCK TABLE ... IN EXCLUSIVE MODE`. `disableConstraints()` and `enableConstraints()` are no-ops — CUBRID has no `SET FOREIGN_KEY_CHECKS`.

## Workarounds

### PDO quote() Missing Surrounding Quotes

`PDO_CUBRID`'s `quote()` method only escapes internal characters — it does **not** add surrounding single quotes, unlike every other PDO driver. The driver's `escape()` method accounts for this by not stripping quotes that were never added.

### bindValue() Type Hint Bug

`PDO_CUBRID` fails with "Type conversion error" when `bindValue()` is called with a `PDO::PARAM_INT` type hint. The `bind()` method omits type hints entirely, letting PDO infer types from PHP values.

### DATETIME Millisecond Precision

CUBRID returns DATETIME values with `.000` millisecond suffix (e.g., `2024-06-15 10:30:00.000`). All fetch methods post-process results through `stripMilliseconds()` to remove the suffix for MySQL compatibility.

### PDO Constructor driver_options

Passing standard PDO options (e.g., `PDO::ATTR_ERRMODE`) in the constructor's fourth parameter triggers `-30019 Invalid connection string` because `PDO_CUBRID` appends numeric option keys into CCI URL parameters. The driver uses `setAttribute()` after connecting.

### No UNSIGNED Modifier

The `normalizeColumnType()` method strips `UNSIGNED` from all type definitions. `buildAlterColumnDefinition()` also strips it to prevent re-introduction.

### No FOREIGN_KEY_CHECKS

`disableConstraints()` and `enableConstraints()` are no-ops. Tables with foreign keys cannot be truncated without dropping constraints first.

### Single Storage Engine

`setTableEngine()` is a no-op. `getEngines()` returns a single `CUBRID` entry. `resolveEngine()` always returns null.

## Technical Notes: PDO_CUBRID PHP 8.3 Port

The stock PECL `PDO_CUBRID` 10.1.0.0004 does not compile on PHP 8.3. This is not a bug fix patch — it is a comprehensive API port adapting the extension to the PHP 8.3 PDO ABI.

### Problem

PHP 8.x introduced breaking changes to the internal PDO driver API:

- Function signatures changed (return types, parameter types)
- `pdo_parse_params()` switched from char pointers to `zend_string` types
- Hash table iteration APIs changed
- Column fetch callbacks must return `zval` (not populate pre-allocated buffers)
- `pdo_column_data::param_type` field was removed
- LOB stream refcount handling changed
- `PDO_PARAM_ZVAL` constant availability varies by version

Without the port, the extension fails to compile with dozens of type mismatch and missing symbol errors.

### Changes Applied

All changes target `cubrid_driver7.c` and `cubrid_statement7.c` in `PDO_CUBRID` 10.1.0.0004.

**cubrid_driver7.c:**
- Updated PDO DBH method signatures for PHP 8 PDO ABI
- Ported query parsing to `pdo_parse_params(stmt, zend_string*, zend_string**)`
- Updated hash-table iteration to PHP 8 APIs
- Updated `quote()` and `lastInsertId()` return types to `zend_string*`
- Updated array update calls to PHP 8 APIs

**cubrid_statement7.c:**
- Updated column fetch callback signature to return zvals
- Removed writes to removed `pdo_column_data::param_type` field
- Updated LOB stream refcount handling and stream callback signatures
- Added guards for `PDO_PARAM_ZVAL` where unavailable

### Build and Validation

Build environment:
- Ubuntu 24.04
- PHP 8.3.6
- CUBRID 11.x

The patched module was built from `/tmp/pdo_cubrid_build/PDO_CUBRID-10.1.0.0004/` and installed to `/usr/lib/php/20230831/pdo_cubrid.so`. A reusable patch file is saved at `tmp/pdo_cubrid-php83-port.patch`.

Validation:
```bash
php -d extension=pdo_cubrid.so -m | grep pdo_cubrid   # loads without errors
php -d extension=pdo_cubrid.so --ri pdo_cubrid         # shows version info
```

### Runtime Quirks That Remain

Even with the PHP 8.3 port, these `PDO_CUBRID` behaviors persist and are handled by driver-level workarounds:

- **`quote()` omits surrounding quotes** — escapes internal characters only; driver accounts for this in `escape()`
- **`bindValue()` with PARAM_INT fails** — "Type conversion error"; driver omits type hints
- **Constructor driver_options corruption** — numeric option keys appended to CCI URL; driver uses `setAttribute()` post-connect
- **DATETIME millisecond suffix** — `.000` appended to all DATETIME values; driver strips on fetch

## File Summary

| File | Purpose |
|------|---------|
| `CubridDriver.php` | Connection, PDO workarounds, SHOW-based introspection, sequence emulation, millisecond stripping, ENUM handling |
| `CubridSyntax.php` | DML, LIMIT pagination, ON DUPLICATE KEY upsert, INSERT IGNORE emulation, EXISTS rewriting, FULL OUTER JOIN emulation |
| `CubridGrammar.php` | DDL, CREATE TABLE (extends MysqlGrammar), UNSIGNED/display-width stripping, ENUM→VARCHAR, RENAME COLUMN |
| `BaseSqlDriver.php` | Abstract SQL contract, shared helpers, expression defaults |
| `BaseSqlSyntax.php` | Base query builder: clauses, bindings, joins, subqueries |
| `MysqlGrammar.php` | MySQL DDL compiler (parent of CubridGrammar): column types, modifiers, constraints |
