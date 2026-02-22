# Informix Driver

**This is a proof-of-concept driver.** It passed all tests when originally written but is not maintained on a regular testing cadence. It should not be considered production-ready without re-validation against the current test suite.

**This driver requires a patched `pdo_informix` extension.** The stock PECL `PDO_INFORMIX` 1.3.7 has multiple bugs in its LOB handling and cursor management that cause data corruption and false errors. Three patches were applied to `informix_statement.c` to fix: (1) NUL byte injection at 8KB boundaries in TEXT/CLOB reads, (2) NULL vs empty string collapse for TEXT columns, and (3) false `-11031` cursor-state exceptions after DDL. See the [Technical Notes](#technical-notes-pdo_informix-patches) section for details.

## Architecture

```
Drivers/Informix/
  InformixDriver.php  extends BaseSqlDriver
  InformixSyntax.php  extends BaseSqlSyntax
  InformixGrammar.php extends BaseSchemaGrammar
```

`BaseSqlDriver` extends `BasePdoDriver` (PDO connection wrapper), which extends `Driver` (abstract connection lifecycle, query execution, result fetching). All 12 concrete drivers follow this same three-file pattern.

The base class defaults follow MySQL conventions. The Informix driver overrides expression properties, type maps, identifier handling (no quoting), introspection queries (Informix system catalogs), and has extensive workarounds for PDO_INFORMIX limitations.

## Expression Overrides

| Property | Informix | Base (MySQL) Default |
|----------|----------|---------------------|
| `$nowExpression` | `CURRENT` | `NOW()` |
| `$randExpression` | `RANDOM()` | `RAND()` |
| `$lengthFunction` | `LENGTH` | `CHAR_LENGTH` |
| `$ifNullFunction` | `NVL` | `IFNULL` |
| `$wrapper` | `%s` (no quoting) | `` ` `` |

## Type System

Informix uses `SMALLINT` for booleans, `CLOB` for text, and has unique types like `SERIAL`/`SERIAL8` for auto-increment and `LVARCHAR` for large variable-length strings:

| Abstract Type | Informix Type | Notes |
|---------------|--------------|-------|
| `boolean` | `SMALLINT` | `1`/`0` values |
| `tinyInteger` | `SMALLINT` | No TINYINT |
| `smallInteger` | `SMALLINT` | |
| `mediumInteger` | `INTEGER` | |
| `integer` | `INTEGER` | |
| `bigInteger` | `INT8` | |
| `string` | `VARCHAR` | `LVARCHAR` if > 255 |
| `char` | `CHAR` | |
| `tinyText` | `LVARCHAR(255)` | |
| `text` .. `longText` | `CLOB` | Supports BTS fulltext |
| `float` | `SMALLFLOAT` | |
| `double` | `FLOAT` | |
| `decimal` | `DECIMAL` | |
| `date` | `DATE` | |
| `time` | `DATETIME HOUR TO SECOND` | |
| `datetime`, `timestamp` | `DATETIME YEAR TO SECOND` | |
| `year` | `SMALLINT` | |
| `binary` | `BYTE` | |
| `json` | `LVARCHAR(8192)` | Bounded to avoid row-size limits |
| `uuid` | `CHAR(36)` | |

No `UNSIGNED` modifier. No `ENUM` type.

### Auto-Increment via SERIAL

```sql
id SERIAL PRIMARY KEY        -- 32-bit
id SERIAL8 PRIMARY KEY       -- 64-bit
```

SERIAL columns are implicitly NOT NULL and PRIMARY KEY. They cannot have a DEFAULT clause. SERIAL columns cannot be updated (Informix error `-232`).

## Connection

The constructor builds a PDO DSN:

```
informix:host={host};service={port};database={database};server={server};protocol={protocol}
```

Alternative via ODBC DSN name:

```
informix:DSN={odbc_dsn_name}
```

Post-connection configuration:

- **`PDO::ATTR_CASE = CASE_LOWER`** — Informix returns UPPERCASE column names by default
- **`PDO::ATTR_EMULATE_PREPARES = true`** — required for CLOB stability (native ODBC prepared execution fails with smart-LOB locator errors, SQLSTATE HY000 / -12014)
- **`PDO::ATTR_STRINGIFY_FETCHES = true`** — force LOB values as strings, not PHP streams
- **`SET LOCK MODE TO WAIT`** — wait for locks instead of immediate failure

Uses the `pdo_informix` PHP extension.

## Identifier Quoting

Informix identifiers are case-insensitive and stored in lowercase. The driver does **not** quote identifiers — they are passed through as-is. Double-quoted identifiers would require setting `DELIMIDENT=Y` and would make identifiers case-sensitive, breaking Informix conventions.

## Query Building (InformixSyntax)

### Pagination

Informix uses `SKIP n FIRST m` in the SELECT clause (not a trailing LIMIT):

```sql
SELECT SKIP 10 FIRST 20 * FROM users
```

SKIP must appear before FIRST.

### INSERT / Upsert

Informix uses `MERGE` statements for upsert (11.50+), same pattern as Oracle:

```sql
MERGE INTO table t
USING (SELECT val1 AS col1, val2 AS col2
       FROM sysmaster:sysdual) s
ON (t.key = s.key)
WHEN MATCHED THEN UPDATE SET t.col1 = s.col1
WHEN NOT MATCHED THEN INSERT (col1, col2)
    VALUES (s.col1, s.col2)
```

Values in the USING SELECT must be inlined as literals (not `?` placeholders) due to a PDO_INFORMIX limitation.

### Bulk INSERT

Informix does not support multi-row `INSERT ... VALUES` syntax. Bulk inserts fall back to individual INSERT per row.

### JSON Support (Limited)

Informix has no native JSON type. JSON is stored in `LVARCHAR(8192)`. No JSON path extraction, containment, or length functions are available through the driver.

### Date Filtering

```php
// EXTEND(column, YEAR TO DAY) = '2024-01-15'
$query->setDateWhere('created', '=', '2024-01-15', 'date');

// YEAR(column) = 2024
$query->setDateWhere('created', '=', 2024, 'year');
```

### Function Translation

| MySQL | Informix |
|-------|----------|
| `CONCAT(a, b, c)` | `(a \|\| b \|\| c)` |
| `IFNULL(a, b)` | `NVL(a, b)` |
| `NOW()` | `CURRENT` |
| `YEAR(col)` | `YEAR(col)` (native) |
| `DATE_FORMAT(col, fmt)` | `TO_CHAR(col, fmt)` |
| `SUBSTRING(s, p, n)` | `SUBSTR(s, p, n)` |
| `NEXTVAL(seq)` | `seq.NEXTVAL` |
| `REGEXP` | `MATCHES` operator |

### String Concatenation

Informix uses the `||` operator. `CONCAT()` is not available.

### TRUNCATE

Standard syntax:

```sql
TRUNCATE TABLE table
```

## DDL Generation (InformixGrammar)

### CREATE TABLE

Informix does not support inline indexes. `compileCreate()` returns an array: the CREATE TABLE statement plus separate `CREATE INDEX` statements.

```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(100)
)
```

No ENGINE, CHARSET, or COLLATION clauses.

### ALTER TABLE

Each operation is a separate statement (cannot be combined with commas like MySQL):

- **ADD COLUMN** — `ALTER TABLE t ADD col type`
- **DROP COLUMN** — `ALTER TABLE t DROP col`
- **MODIFY COLUMN** — `ALTER TABLE t MODIFY col type`
- **RENAME COLUMN** — `RENAME COLUMN t.old TO new`
- **DROP INDEX** — `DROP INDEX name`
- **ADD INDEX** — `CREATE [UNIQUE] INDEX name ON t (cols)`
- **DROP PRIMARY KEY** — looks up constraint name, then `ALTER TABLE t DROP CONSTRAINT name`
- **ADD PRIMARY KEY** — `ALTER TABLE t ADD CONSTRAINT PRIMARY KEY (cols) CONSTRAINT name`
- **DROP FOREIGN KEY** — `ALTER TABLE t DROP CONSTRAINT name`
- **ADD FOREIGN KEY** — `ALTER TABLE t ADD CONSTRAINT FOREIGN KEY (cols) REFERENCES ... CONSTRAINT name`

Note: Informix puts the `CONSTRAINT name` at the end, not the beginning.

### Table and Column Rename

```sql
RENAME TABLE old_table TO new_table
RENAME COLUMN table.old_col TO new_col
```

### Column Positioning

Informix does **not** support AFTER/FIRST/BEFORE. All position methods throw `RuntimeException`.

### Fulltext Indexes

Informix uses BTS (Bayesian Text Search) when available:

```sql
CREATE INDEX ft_name ON table (col bts_clob_ops) USING bts
```

Operator classes vary by column type (`bts_clob_ops`, `bts_varchar_ops`, `bts_lvarchar_ops`). Falls back to a regular index if BTS is not installed.

## Schema Introspection

Informix uses system catalog tables (`systables`, `syscolumns`, `sysindexes`, `sysconstraints`, `sysreferences`):

| Method | Implementation |
|--------|---------------|
| `getTableColumns($table)` | `syscolumns` + `systables` with numeric type codes |
| `getTableKeys($table)` | `sysindexes` + `sysconstraints` |
| `getForeignKeys($table)` | `sysconstraints` + `sysreferences` |
| `getTableList()` | `systables WHERE tabtype = 'T' AND tabid > 99` |
| `getTableCreate($tables)` | Reconstructed from system catalogs |
| `tableExists($table)` | `systables` count |
| `getPrimaryKey($table)` | `sysconstraints WHERE constrtype = 'P'` |
| `getAutoIncrement($table)` | `MAX(serial_col) + 1` |
| `getCollation()` | `DBINFO('dblocale')` |
| `getCharacterSet()` | `DBINFO('dbcodeset')` |
| `getVersion()` | `DBINFO('version','full')` from `sysmaster:sysdual` |
| `getDatabaseNames()` | `sysdatabases` |

### Numeric Type Codes

Informix stores column types as numeric codes in `syscolumns.coltype`. Codes >= 256 indicate NOT NULL (base type = code % 256):

| Code | Type |
|------|------|
| 0 | CHAR |
| 1 | SMALLINT |
| 2 | INTEGER |
| 5 | DECIMAL |
| 6 | SERIAL |
| 7 | DATE |
| 10 | DATETIME |
| 12 | TEXT |
| 13 | VARCHAR |
| 17 | INT8 |
| 18 | SERIAL8 |
| 40 | LVARCHAR |
| 43 | CLOB |
| 45 | BOOLEAN |
| 52 | BIGINT |
| 53 | BIGSERIAL |

### ENUM (Not Supported)

Informix has no ENUM type.

### Views

- `createOrReplaceView()` — drops then creates (no `CREATE OR REPLACE VIEW`)
- `dropView()`, `viewExists()`, `getViews()` — queries `systables WHERE tabtype = 'V'`

### Sequences (Native)

Informix supports native sequences:

- `createSequence()` — `CREATE SEQUENCE` with START, INCREMENT, MINVALUE, MAXVALUE, CYCLE
- `dropSequence()` — `DROP SEQUENCE`
- `sequenceExists()` — queries `systables WHERE tabtype = 'Q'`
- `nextSequenceValue()` — `SELECT seq.NEXTVAL FROM sysmaster:sysdual`
- `currentSequenceValue()` — `SELECT seq.CURRVAL FROM sysmaster:sysdual`

### Feature Detection

| Method | Returns |
|--------|---------|
| `getEngine()` | `'informix'` |
| `supportsSequences()` | true |
| `supportsWindowFunctions()` | true (12.10+) |
| `supportsCTE()` | true |
| `supportsIfNotExists()` | false |
| `supportsDropColumn()` | true |
| `supportsRenameColumn()` | true |

## Transaction Support

Informix transaction support depends on the database logging mode. Non-logging databases return error `-256` ("Transaction not available"). The driver detects this on first use and falls back to transaction emulation.

### SQL Transactions (Logging Databases)

```
transactionStart():
  depth 0 → BEGIN WORK
  depth N → SAVEPOINT SP_N

transactionCommit():
  depth 0 → COMMIT WORK
  depth N → RELEASE SAVEPOINT SP_N

transactionRollback():
  depth 0 → ROLLBACK WORK
  depth N → ROLLBACK TO SAVEPOINT SP_N
```

### Emulated Transactions (Non-Logging Databases)

When SQL transactions are unavailable, the driver buffers write statements (INSERT, UPDATE, DELETE, DDL) and replays them on commit. Rollback discards the buffer.

### DDL Auto-Commit

Informix does not auto-commit DDL statements. The driver detects CREATE, ALTER, DROP, TRUNCATE, and RENAME statements and issues an explicit commit after execution when outside an explicit transaction.

`lockTable()` uses `LOCK TABLE ... IN EXCLUSIVE MODE`.

## Workarounds

The Informix driver has the most PDO-level workarounds of any driver due to bugs and limitations in `pdo_informix`.

### PARAM_NULL Corrupts Parameter Count

`bindValue()` with `PDO::PARAM_NULL` corrupts the internal parameter count, causing `-11012` ("Wrong number of parameters") on execute. The `bind()` method uses `PDO::PARAM_STR` for null values instead.

### Placeholders Fail in SELECT Expressions

PDO_INFORMIX 1.3.x cannot handle `?` placeholders as function arguments in SELECT expressions (returns `-999` "Not implemented yet"). The syntax class inlines literal values for SELECT expression bindings via `quoteValue()`.

### Placeholders Fail in MERGE USING SELECT

Same limitation — `?` in MERGE USING SELECT returns `-201` syntax error. The `buildUpsert()` method inlines all values as literals in the source SELECT.

### SERIAL Columns Cannot Be Updated

Informix enforces this at the SQL PREPARE stage (error `-232`). The syntax class filters SERIAL columns from UPDATE SET clauses.

### DDL Cursor-State False Positive

Some PDO_INFORMIX builds raise a false `-11031` ("Invalid cursor state") error after successful DDL execution. The `execute()` and `exec()` methods catch and ignore this error specifically for DDL statements.

### Statement Handle Cleanup

PDO_INFORMIX has statement cleanup bugs that can corrupt the connection after fetching results. `freeResult()` explicitly nulls the statement handle to force immediate C-level cleanup.

### LOB Stream Handling

LOB values can be returned as PHP stream resources. `convertLobs()` intercepts fetch results and calls `stream_get_contents()` to convert streams to strings. `ATTR_STRINGIFY_FETCHES=true` is set at connection time to minimize this.

### SERIAL Counter Reset

Informix's SERIAL is a one-way high-water mark — it cannot be decremented directly. To reset the counter backward, `setAutoIncrement()` drops the table, recreates it with the new schema, and reinserts all data.

### Trace Logging

The driver includes built-in trace logging for debugging PDO_INFORMIX issues. Enable via environment variable `IFX_TRACE=1` (output to `IFX_TRACE_FILE` or `/tmp/ifx_trace.log`). Traces include SQL statements, bind parameters with types and sizes, LOB read diagnostics, and MD5 hashes for large payloads.

## Technical Notes: PDO_INFORMIX Patches

The stock PECL `PDO_INFORMIX` 1.3.7 extension has several bugs that required patching `informix_statement.c` before the driver could pass all tests. All patches are in the LOB handling and cursor management paths.

### Problems Before Patching

**1. TEXT/CLOB 8KB Boundary Corruption**

Reading TEXT or CLOB values near 8KB boundaries produced corrupted data. For a payload of exactly 8192 bytes, the driver returned 8193 bytes with a NUL (`\0`) byte injected at position 8191. The corruption was deterministic and reproduced at every `8191*n + 1` boundary (8192, 16383, 24574, ...).

Root cause: `lob_stream_read()` uses an 8192-byte buffer (`LOB_BUFFER_SIZE`). For `SQL_LONGVARCHAR`, it trims a trailing NUL terminator when `readBytes > count`, but failed to handle the `readBytes == count` boundary case where the terminator was still present.

This was reproduced with plain PDO probes outside the ORM, confirming it was a C-level driver bug, not an application issue.

**2. NULL vs Empty String Collapse for TEXT Columns**

TEXT NULL and TEXT empty string (`''`) both surfaced as empty string in PHP when `ATTR_STRINGIFY_FETCHES=true`. The LOB fetch path used a `NULL/0` read probe in `create_lob_stream()` that could not distinguish between the two states.

This broke ORM attribute casting for nullable JSON-like fields — a stored NULL came back as `''`, which failed JSON decode.

**3. False `-11031` Cursor-State Exceptions After DDL**

DDL statements like `CREATE INDEX ... USING bts` completed successfully (the index appeared in `sysindexes`), but PDO raised a `-11031` ("Invalid cursor state") exception during its post-execution fetch probe. The fetcher treated all `SQLFetchScroll` errors as fatal, including the expected "no result set" response from no-result DDL.

**4. CLOB Fetch Corruption**

The CLOB read path assumed all reads return a fixed-size LO (large object) locator, but some Informix ODBC flows return inline payload chunks via `SQLGetData`. This caused NULL or corrupted CLOB values depending on driver mode.

**5. LOB Stream Binding Corrupted Writes**

`stmt_parameter_post_execute()` treated negative `transfer_length` (normal for `SQL_LEN_DATA_AT_EXEC` markers) as empty/null output and mutated bound zvals, corrupting LOB stream inserts after prior LOB operations.

### Patches Applied

All patches target `informix_statement.c` in `PDO_INFORMIX` 1.3.7.

**Fix 1: TEXT Boundary Truncation** — Changed `lob_stream_read()` to treat `readBytes >= count` (not just `>`) as the truncation boundary for `SQL_LONGVARCHAR`, so the trailing NUL terminator is never exposed as payload data.

**Fix 2: NULL vs Empty TEXT Probe** — Moved NULL detection from `create_lob_stream()` into `informix_stmt_get_col()`. Before creating the LOB stream, a 1-byte `SQLGetData` probe checks the indicator: `SQL_NULL_DATA` returns PHP NULL, indicator `0` creates the stream for empty string. This preserves the semantic difference.

**Fix 3: Cursor Warning Suppression** — In `informix_stmt_fetcher()`, when `SQLFetchScroll` returns `SQL_ERROR`, the fix reads diagnostics via `SQLGetDiagRec`. If SQLSTATE is `24000` or `HY000` with native code `-11031`, it maps to `SQL_NO_DATA` instead of raising a fatal exception. All other errors continue through normal error handling.

**Fix 4: Hybrid CLOB Read Path** — After the first `SQLGetData` in the CLOB read path, if the indicator does not equal the locator size, the value is treated as inline CLOB payload and consumed via chunked `SQLGetData` reads instead of the LO API.

**Fix 5: Post-Execute Parameter Guard** — `stmt_parameter_post_execute()` now skips zval normalization unless the parameter is a true `INPUT_OUTPUT` parameter, preventing corruption of bound values during sequential LOB operations.

### Problems That Remain (Driver-Level Workarounds)

Even with the patches, these PDO_INFORMIX limitations persist and are handled by PHP-level workarounds in the driver:

- **PARAM_NULL bug** — `PDO::PARAM_NULL` corrupts parameter count; driver uses `PARAM_STR` for nulls
- **Placeholder limitations** — `?` fails in SELECT expressions and MERGE USING SELECT; driver inlines literal values
- **PARAM_LOB stream corruption** — upgrading large strings to `PARAM_LOB` introduced +1 byte corruption near 8KB boundaries; driver keeps all strings on plain `PARAM_STR` binding
- **Statement handle cleanup** — driver explicitly nulls statement handles after fetch to prevent connection corruption

### Environment

- PDO_INFORMIX: 1.3.7 (patched)
- Informix Client SDK: 4.50.FC12W1
- Informix Server: 14.10.FC7W1DE
- Database must be created `WITH LOG` for transaction support

## File Summary

| File | Purpose |
|------|---------|
| `InformixDriver.php` | Connection, PDO workarounds, introspection via system catalogs, sequences, trace logging |
| `InformixSyntax.php` | DML, SKIP/FIRST pagination, MERGE upsert, value inlining, function translation |
| `InformixGrammar.php` | DDL, CREATE TABLE, SERIAL types, BTS fulltext, type mapping |
| `BaseSqlDriver.php` | Abstract SQL contract, shared helpers, expression defaults |
| `BaseSqlSyntax.php` | Base query builder: clauses, bindings, joins, subqueries |
| `BaseSchemaGrammar.php` | Base DDL compiler: column types, modifiers, constraints |
