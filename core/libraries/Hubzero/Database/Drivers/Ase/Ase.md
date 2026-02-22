# ASE Driver

**This is a proof-of-concept driver.** It passed all tests when originally written but is not maintained on a regular testing cadence. It should not be considered production-ready without re-validation against the current test suite.

**This driver requires the `pdo_dblib` PHP extension** compiled with FreeTDS. PDO_DBLIB is a generic TDS (Tabular Data Stream) driver — the ASE driver targets SAP Adaptive Server Enterprise (formerly Sybase ASE) specifically, not Microsoft SQL Server.

## Architecture

```
Drivers/Ase/
  AseDriver.php  extends BaseSqlDriver
  AseSyntax.php  extends BaseSqlSyntax
  AseGrammar.php extends BaseSchemaGrammar
```

`BaseSqlDriver` extends `BasePdoDriver` (PDO connection wrapper), which extends `Driver` (abstract connection lifecycle, query execution, result fetching). All 12 concrete drivers follow this same three-file pattern.

The base class defaults follow MySQL conventions. The ASE driver overrides expression properties, type maps, identifier handling (square brackets), introspection queries (ASE system catalogs), and has extensive workarounds for PDO_DBLIB limitations and ASE SQL dialect differences.

## Expression Overrides

| Property | ASE | Base (MySQL) Default |
|----------|-----|---------------------|
| `$nowExpression` | `GETDATE()` | `NOW()` |
| `$randExpression` | `RAND()` | `RAND()` |
| `$lengthFunction` | `CHAR_LENGTH` | `CHAR_LENGTH` |
| `$ifNullFunction` | `ISNULL` | `IFNULL` |
| `$wrapper` | `[%s]` | `` ` `` |

## Type System

ASE uses `BIT` for booleans (cannot be NULL), `TEXT` for large strings, `IMAGE` for binary data, and `IDENTITY` for auto-increment columns:

| Abstract Type | ASE Type | Notes |
|---------------|----------|-------|
| `boolean` | `BIT` | Forced `NOT NULL DEFAULT 0` |
| `tinyInteger` | `TINYINT` | |
| `smallInteger` | `SMALLINT` | |
| `mediumInteger` | `INT` | |
| `integer` | `INT` | |
| `bigInteger` | `BIGINT` | |
| `string` | `VARCHAR` | Page size limits to ~4096 bytes |
| `char` | `CHAR` | |
| `tinyText` | `VARCHAR(255)` | |
| `text` .. `longText` | `TEXT` | |
| `float` | `FLOAT` | |
| `double` | `FLOAT` | No DOUBLE type |
| `decimal` | `DECIMAL` | |
| `date` | `DATETIME` | DATE returns non-ISO format via PDO_DBLIB |
| `time` | `TIME` | |
| `datetime`, `timestamp` | `DATETIME` | |
| `year` | `SMALLINT` | |
| `binary` | `VARBINARY(255)` | |
| `json` | `TEXT` | No native JSON support |
| `uuid` | `CHAR(36)` | |

No `UNSIGNED` modifier. No `ENUM` type (mapped to `VARCHAR(255)`). `BLOB` variants are mapped to `IMAGE`.

### Auto-Increment via IDENTITY

```sql
id INT IDENTITY PRIMARY KEY
id BIGINT IDENTITY PRIMARY KEY
```

IDENTITY columns are implicitly NOT NULL. ASE requires `SET IDENTITY_INSERT table ON` to insert explicit values into IDENTITY columns. The driver detects this automatically in `execute()`.

### BIT Columns

ASE BIT columns **cannot be NULL**. The grammar forces all BIT columns to `NOT NULL DEFAULT 0`, even if the schema definition requests nullable. This is enforced in both `compileColumn()` and `compileColumnFromDefinition()`.

### Implicit NOT NULL Defaults

ASE strictly enforces NOT NULL — it rejects INSERTs that omit columns without defaults. The grammar injects type-appropriate implicit defaults for NOT NULL columns (empty string for VARCHAR/CHAR, `0` for numeric types) to match MySQL's non-strict mode behavior.

## Connection

The constructor builds a PDO DSN:

```
dblib:host={host}:{port};dbname={database};charset={charset}
```

Post-connection configuration:

- **`SET QUOTED_IDENTIFIER ON`** — enables double-quoted identifiers
- **`SET DATEFORMAT ymd`** — ISO date format (YYYY-MM-DD) for consistent output

The syntax backend is forced to `'ase'` because PDO_DBLIB reports itself as `'dblib'`, which would resolve to the wrong syntax class.

Uses the `pdo_dblib` PHP extension with FreeTDS.

## Identifier Quoting

ASE uses square brackets for identifier quoting:

```sql
SELECT [name], [value] FROM [my_table] WHERE [id] = 1
```

The `quoteIdentifier()` method escapes closing brackets by doubling them: `]` → `]]`.

### Reserved Word Alias Quoting

The syntax class maintains a list of ASE reserved words (`count`, `sum`, `value`, `user`, `key`, `status`, `name`, etc.). Column aliases that match reserved words are automatically quoted with square brackets.

## Query Building (AseSyntax)

### Pagination

ASE uses `TOP n` in the SELECT clause. There is no `OFFSET` or `FETCH` syntax:

```sql
SELECT TOP 20 * FROM users ORDER BY id
```

When offset is requested, the driver uses a **client-side pagination** strategy:

1. Syntax emits `TOP (offset + limit)` to fetch enough rows
2. Driver stores the offset via `setPendingOffset()`
3. `loadObjectList()` / `loadAssocList()` / `loadColumn()` skip the first `offset` rows after fetching

For UNION queries, `TOP` only applies to the first SELECT, not the combined result. The driver falls back to fetching all rows and applying both offset and limit client-side via `setPendingLimit()`.

### INSERT / Upsert

ASE has no `MERGE` statement. Upserts use the `IF EXISTS ... UPDATE ... ELSE INSERT` pattern:

```sql
IF EXISTS (SELECT 1 FROM table WHERE key_col = ?)
  UPDATE table SET col1 = ?, col2 = ? WHERE key_col = ?
ELSE
  INSERT INTO table (key_col, col1, col2) VALUES (?, ?, ?)
```

When all columns are conflict columns (no update needed), it simplifies to:

```sql
IF NOT EXISTS (SELECT 1 FROM table WHERE key_col = ?)
  INSERT INTO table (key_col) VALUES (?)
```

### INSERT IGNORE

ASE has no `INSERT IGNORE` syntax. The syntax class sets `needsRowByRowInsertIgnore()` to return true for INSERT...SELECT with the ignore flag, which triggers row-by-row insertion with error suppression.

### Bulk INSERT

ASE does not support multi-row `INSERT ... VALUES` syntax. Bulk inserts use `INSERT...SELECT...UNION ALL`:

```sql
INSERT INTO table (col1, col2)
SELECT ?, ? UNION ALL SELECT ?, ?
```

If the target table has an IDENTITY column that is being explicitly inserted, the bulk path falls back to individual INSERT statements (IDENTITY_INSERT does not work with INSERT...SELECT...UNION ALL).

### FULL OUTER JOIN Emulation

ASE does not support `FULL OUTER JOIN`. The syntax class decomposes it into `LEFT JOIN UNION ALL RIGHT JOIN` using the framework's `expandFullJoinBranches()` method. During emulation, FROM/JOIN/WHERE/GROUP/HAVING/ORDER clauses from the parent query are suppressed and rebuilt per branch.

### Date Filtering

```php
// CONVERT(DATE, column) = ?
$query->setDateWhere('created', '=', '2024-01-15', 'date');

// DATEPART(yy, column) = ?
$query->setDateWhere('created', '=', 2024, 'year');
```

### Function Translation

| MySQL | ASE |
|-------|-----|
| `CONCAT(a, b, c)` | `(ISNULL(a, '') + ISNULL(b, '') + ISNULL(c, ''))` |
| `IFNULL(a, b)` | `ISNULL(a, b)` |
| `COALESCE(a, b)` | `ISNULL(a, b)` |
| `NOW()` | `GETDATE()` |
| `YEAR(col)` | `DATEPART(yy, col)` |
| `MONTH(col)` | `DATEPART(mm, col)` |
| `REPLACE(s, a, b)` | `STR_REPLACE(s, a, b)` |
| `MOD(a, b)` | `(a % b)` |
| `CEIL(x)` | `CEILING(x)` |
| `DATE_FORMAT(col, fmt)` | `CONVERT(VARCHAR(30), col, style)` |
| `UNIX_TIMESTAMP(col)` | `DATEDIFF(second, '1970-01-01', col)` |
| `REGEXP` | `PATINDEX` (SQL wildcards, not regex) |

### String Concatenation

ASE uses the `+` operator for string concatenation. Each operand is wrapped with `ISNULL(expr, '')` to prevent NULL propagation (NULL + string = NULL in ASE):

```sql
(ISNULL(first_name, '') + ' ' + ISNULL(last_name, ''))
```

### TRUNCATE

Standard syntax:

```sql
TRUNCATE TABLE table
```

Note: ASE's `TRUNCATE TABLE` does **not** reset the IDENTITY counter. For tables with IDENTITY columns, `truncateTable()` drops and recreates the table to reset the counter.

## DDL Generation (AseGrammar)

### CREATE TABLE

ASE does not support inline indexes. `compileCreate()` returns an array: the CREATE TABLE statement plus separate `CREATE INDEX` statements.

```sql
CREATE TABLE [users] (
  [id] INT IDENTITY PRIMARY KEY,
  [email] VARCHAR(255) NOT NULL DEFAULT '',
  [name] VARCHAR(100) NULL
)
```

No ENGINE, CHARSET, or COLLATION clauses. Fulltext indexes are not supported — they fall back to regular indexes.

### ALTER TABLE

Each column operation is a separate statement:

- **ADD COLUMN** — `ALTER TABLE t ADD col type`
- **DROP COLUMN** — `ALTER TABLE t DROP col`
- **MODIFY COLUMN** — `ALTER TABLE t MODIFY col type` (not `ALTER COLUMN`)
- **RENAME COLUMN** — `EXEC sp_rename 'table.old', 'new', 'column'`
- **DROP INDEX** — `DROP INDEX table.index_name`
- **ADD INDEX** — `CREATE [UNIQUE] INDEX name ON t (cols)`
- **DROP PRIMARY KEY** — looks up constraint name via `sysindexes`, then `ALTER TABLE t DROP CONSTRAINT name`
- **ADD PRIMARY KEY** — alters nullable PK columns to NOT NULL first, then `ALTER TABLE t ADD PRIMARY KEY (cols)`
- **DROP FOREIGN KEY** — `ALTER TABLE t DROP CONSTRAINT name`
- **ADD FOREIGN KEY** — `ALTER TABLE t ADD CONSTRAINT name FOREIGN KEY (col) REFERENCES ...`

### Table and Column Rename

ASE uses `sp_rename` stored procedure:

```sql
EXEC sp_rename 'old_table', 'new_table'
EXEC sp_rename 'table.old_col', 'new_col', 'column'
```

`sp_rename` returns result sets that must be fully consumed to avoid "results pending" errors on subsequent queries. The driver calls `consumeResultSets()` after every `sp_rename` execution.

### Column Positioning

ASE does **not** support AFTER/FIRST/BEFORE column positioning. All position methods (`addColumnAfter`, `addColumnFirst`, `modifyColumnAfter`, etc.) silently ignore the position argument and add/modify the column without repositioning.

### DROP TABLE IF EXISTS

ASE does not support `DROP TABLE IF EXISTS` syntax. The driver's `dropTable()` method checks `sysobjects` for table existence before executing `DROP TABLE`.

### Adding Auto-Increment PK to Existing Tables

`addAutoIncrementPrimaryKey()` handles two cases:

1. **Empty table** — simple `ALTER TABLE ADD col IDENTITY PRIMARY KEY`
2. **Populated table** — multi-step approach:
   - Add nullable column
   - Populate with sequential values via temp table (no ROW_NUMBER() in ASE)
   - Alter to NOT NULL
   - Add primary key constraint

### Foreign Key Referential Actions

ASE 16.x does not support `ON DELETE CASCADE`, `ON UPDATE SET NULL`, or other referential actions. The `transformSql()` method strips `ON DELETE NO ACTION` / `ON UPDATE NO ACTION` (the default behavior) and throws `RuntimeException` for any other action (`CASCADE`, `SET NULL`, `SET DEFAULT`, `RESTRICT`).

## Schema Introspection

ASE uses system catalog tables (`sysobjects`, `syscolumns`, `systypes`, `sysindexes`, `sysreferences`, `sysconstraints`, `syscomments`):

| Method | Implementation |
|--------|---------------|
| `getTableColumns($table)` | `syscolumns` + `systypes` with bitmask status flags |
| `getTableKeys($table)` | `sysindexes` with `status & 2048` (PK), `status & 2` (unique) |
| `getForeignKeys($table)` | `sysreferences` + `sysobjects` for constraint names |
| `getIndexes($table)` | `sysindexes` + `index_col()` function for column names |
| `getTableList()` | `sysobjects WHERE type = 'U'` |
| `getTableCreate($tables)` | Reconstructed from system catalogs |
| `tableExists($table)` | `sysobjects` lookup |
| `getPrimaryKey($table)` | `sysindexes WHERE status & 2048` + `index_col()` |
| `getAutoIncrement($table)` | `MAX(identity_col) + 1` |
| `getCollation()` | `@@sortorder` |
| `getCharacterSet()` | `@@client_csname` |
| `getVersion()` | `@@version` |
| `getDatabaseNames()` | `master..sysdatabases` |
| `getCheckConstraints($table)` | `sysobjects WHERE type = 'C'` + `syscomments` |
| `getColumnDefaults($table)` | `syscolumns` + `sysobjects WHERE type = 'D'` + `syscomments` |

### Column Status Bitmasks

ASE stores column attributes as bitmask flags in `syscolumns.status`:

| Bit | Meaning |
|-----|---------|
| `status & 8` | Column allows NULL |
| `status & 128` | Column is IDENTITY |

### Index Status Bitmasks

| Bit | Meaning |
|-----|---------|
| `status & 2` | UNIQUE index |
| `status & 2048` | Primary key index |

### ENUM (Not Supported)

ASE has no ENUM type. `getEnumValues()` returns empty array; `addEnumValue()` and `removeEnumValue()` are no-ops.

### Views

- `createOrReplaceView()` — drops then creates (no `CREATE OR REPLACE VIEW`)
- `dropView()`, `viewExists()` — queries `sysobjects WHERE type = 'V'`
- `getViews()` — queries `sysobjects WHERE type = 'V'`

### Sequences (Table-Based Emulation)

ASE has no native sequences. The driver emulates them via a `_sequences` table:

```sql
CREATE TABLE [_sequences] (
  [name] VARCHAR(255) NOT NULL,
  [current_value] NUMERIC(19,0) DEFAULT 0 NOT NULL,
  [increment_value] INT DEFAULT 1 NOT NULL,
  [table_name] VARCHAR(255) NULL,
  PRIMARY KEY ([name])
)
```

- `createSequence()` — inserts row with `current_value = start - increment` so first `nextSequenceValue()` returns `start`
- `dropSequence()` — deletes row from `_sequences`
- `sequenceExists()` — checks `_sequences` table
- `nextSequenceValue()` — atomic UPDATE + SELECT
- `currentSequenceValue()` — SELECT without incrementing
- `usesSequenceEmulation()` — returns `true`

The `_sequences` table is lazily created on first sequence operation. Sequence rows associated with a table are cleaned up when that table is dropped.

## Feature Detection

| Method | Returns |
|--------|---------|
| `getEngine()` | `'SAP ASE'` |
| `supportsSequences()` | true (emulated) |
| `supportsIfNotExists()` | false |
| `supportsIfNotExistsForIndex()` | false |
| `supportsDropColumn()` | true |
| `supportsReferentialActions()` | false |
| `supportsRegexp()` | false |

## Transaction Support

ASE supports nested transactions via savepoints:

```
transactionStart():
  depth 0 → PDO::beginTransaction()
  depth N → SAVE TRANSACTION SP_N

transactionCommit():
  depth 0 → PDO::commit()
  depth N → (depth decremented, savepoint released implicitly)

transactionRollback():
  depth 0 → PDO::rollBack()
  depth N → ROLLBACK TRANSACTION SP_N
```

`lockTable()` and `unlockTables()` are no-ops — ASE uses table hints for locking rather than explicit lock statements.

## Workarounds

The ASE driver has extensive workarounds for PDO_DBLIB limitations and ASE SQL dialect differences.

### PDO_DBLIB Quotes All Bindings as Strings

PDO_DBLIB with emulated prepares quotes ALL bound values as strings, ignoring `PDO::PARAM_INT` hints. ASE does not allow implicit `VARCHAR→INT` conversion, causing silent data loss or errors.

Two complementary fixes:

1. **`bind()`** — intercepts numeric bindings (PHP int, float, and numeric strings in non-INSERT contexts) and inlines them directly into the SQL as raw numeric literals, replacing their `?` placeholders.

2. **`substituteBindings()`** — runs before every `execute()`, replacing ALL remaining `?` placeholders with properly typed inline literals. This completely bypasses PDO's broken quoting by rebuilding the SQL with inline values.

### @@identity for lastInsertId

`PDO::lastInsertId()` returns `'0'` for PDO_DBLIB. The `insertid()` method queries `SELECT @@identity` instead.

### IDENTITY_INSERT Auto-Detection

When an INSERT statement includes a value for an IDENTITY column, ASE requires `SET IDENTITY_INSERT table ON` before the INSERT and OFF afterward. The `execute()` method automatically detects this pattern and toggles the setting transparently.

The detection handles two forms:
- `INSERT INTO table (col_list) VALUES (...)` — checks if IDENTITY column is in the column list
- `INSERT INTO table SELECT ...` — determines positional column mapping and rewrites to add an explicit column list

### Async DDL Retry

ASE DDL operations (ALTER TABLE, etc.) can be asynchronous. Subsequent queries on the same table may fail with "ALTER TABLE operation is in progress... Retry your query later." The `executeWithRetry()` method retries with exponential backoff (50ms, 100ms, 200ms, ..., cap at 2s) up to a configurable timeout (default: 30 seconds).

### PDO_DBLIB Error Mode Switching

PDO_DBLIB with `ERRMODE_EXCEPTION` throws on ASE informational messages (severity < 10), like "Changed database context to..." which are not real errors. The `execRawSilent()` helper temporarily switches to `ERRMODE_SILENT` for session SET commands. The `exec()` method routes through `setQuery()`/`execute()` instead of `PDO::exec()` to avoid this issue.

### PDO_DBLIB Error Detection

PDO_DBLIB's `execute()` returns `true` even on SQL errors. The `executeWithRetry()` method explicitly checks `errorInfo()` after every execution and only treats errors with severity >= 16 as real failures.

### Cursor Cleanup After Non-SELECT Statements

PDO_DBLIB leaves pending result sets after every statement execution. Non-SELECT statements must have their cursor closed via `closeCursor()` to consume pending results. Without this, subsequent queries silently fail with "results pending" errors.

### Result Set Consumption for sp_rename

ASE's `sp_rename` stored procedure returns multiple result sets. These must be fully consumed via `nextRowset()` iteration before the next query can execute.

### SQL Transform Pipeline

The `transformSql()` method runs before every `execute()` to fix ASE-incompatible SQL constructs:

- **REPLACE() → STR_REPLACE()** — ASE uses `STR_REPLACE` for string replacement
- **ORDER BY in derived tables** — stripped because ASE forbids ORDER BY in subqueries used as FROM sources
- **FK referential actions** — `ON DELETE/UPDATE NO ACTION` stripped (default behavior), other actions throw RuntimeException

### TRUNCATE and IDENTITY Reset

ASE's `TRUNCATE TABLE` does not reset the IDENTITY counter. For tables with IDENTITY columns, `truncateTable()` captures the CREATE TABLE DDL and indexes, drops the table, recreates it, and recreates all non-PK indexes.

### Quote Override for Numeric Types

The `quote()` method returns raw numeric strings (without single quotes) for PHP int and float values. ASE does not allow implicit `VARCHAR→INT/DECIMAL` conversion, so numeric values must not be quoted.

### Client-Side OFFSET for Subqueries

ASE forbids ORDER BY in derived tables and has no OFFSET clause. `materializeOffsetSubquery()` executes the subquery into a session temp table (`#ase_sub_*`), deletes the first N rows via `SET ROWCOUNT`, and returns a simple SELECT from the temp table.

## File Summary

| File | Purpose |
|------|---------|
| `AseDriver.php` | Connection, PDO_DBLIB workarounds, binding substitution, IDENTITY_INSERT detection, introspection via system catalogs, sequence emulation, client-side pagination |
| `AseSyntax.php` | DML, TOP pagination, IF EXISTS upsert, FULL JOIN emulation, INSERT...SELECT...UNION ALL, function translation, reserved word alias quoting |
| `AseGrammar.php` | DDL, CREATE TABLE, IDENTITY columns, type mapping, sp_rename, BIT NOT NULL enforcement, implicit defaults |
| `BaseSqlDriver.php` | Abstract SQL contract, shared helpers, expression defaults |
| `BaseSqlSyntax.php` | Base query builder: clauses, bindings, joins, subqueries |
| `BaseSchemaGrammar.php` | Base DDL compiler: column types, modifiers, constraints |
