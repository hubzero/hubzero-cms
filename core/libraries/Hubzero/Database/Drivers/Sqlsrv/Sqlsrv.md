# SQL Server Driver

**This is a proof-of-concept driver.** It passed all tests when originally written but is not maintained on a regular testing cadence. It should not be considered production-ready without re-validation against the current test suite.

## Architecture

```
Drivers/Sqlsrv/
  SqlsrvDriver.php  extends BaseSqlDriver
  SqlsrvSyntax.php  extends BaseSqlSyntax
  SqlsrvGrammar.php extends BaseSchemaGrammar
```

`BaseSqlDriver` extends `BasePdoDriver` (PDO connection wrapper), which extends `Driver` (abstract connection lifecycle, query execution, result fetching). All 12 concrete drivers follow this same three-file pattern.

The base class defaults follow MySQL conventions. The SQL Server driver overrides expression properties, type maps, identifier quoting (square brackets), introspection queries (`sys.*` and `INFORMATION_SCHEMA`), and the execute path (to handle IDENTITY_INSERT automatically).

## Expression Overrides

| Property | SQL Server | Base (MySQL) Default |
|----------|-----------|---------------------|
| `$nowExpression` | `GETDATE()` | `NOW()` |
| `$randExpression` | `NEWID()` | `RAND()` |
| `$lengthFunction` | `LEN` | `CHAR_LENGTH` |
| `$ifNullFunction` | `ISNULL` | `IFNULL` |
| `$nameQuote` | `['[', ']']` | `` ` `` |

## Type System

SQL Server uses Unicode types by default (`NVARCHAR` instead of `VARCHAR`) and `BIT` for booleans:

| Abstract Type | SQL Server Type | Notes |
|---------------|----------------|-------|
| `boolean` | `BIT` | `1`/`0` values |
| `tinyInteger` | `TINYINT` | |
| `smallInteger` | `SMALLINT` | |
| `mediumInteger` | `INT` | No MEDIUMINT |
| `integer` | `INT` | |
| `bigInteger` | `BIGINT` | |
| `string` | `NVARCHAR` | Unicode; requires length |
| `char` | `NCHAR` | Unicode |
| `tinyText` | `NVARCHAR(255)` | |
| `text` .. `longText` | `NVARCHAR(MAX)` | ~2 GB max; all sizes collapse |
| `float` | `REAL` | |
| `double` | `FLOAT` | |
| `decimal` | `DECIMAL` | |
| `datetime`, `timestamp` | `DATETIME2(0)` | Higher precision than DATETIME |
| `timestampTz` | `DATETIMEOFFSET` | Native timezone support |
| `year` | `SMALLINT` | No YEAR type |
| `binary` | `VARBINARY(MAX)` | |
| `json` | `NVARCHAR(MAX)` | No native JSON type; functions since 2016 |
| `uuid` | `CHAR(36)` | |

No `UNSIGNED` modifier — SQL Server integer types are always signed. No `ENUM` type — use CHECK constraints instead.

## Connection

The constructor builds a pdo_sqlsrv DSN:

```
sqlsrv:Server={host},{port};Database={database}
```

- Connection pooling enabled by default: `ConnectionPooling=1`
- Optional `trust_server_certificate` for self-signed certs
- Uses `pdo_sqlsrv` PHP extension

## Query Building (SqlsrvSyntax)

### Pagination

SQL Server has no `LIMIT` clause. Two strategies are used:

**TOP** — for simple limits without offset:
```sql
SELECT TOP 20 * FROM [users]
```

**OFFSET/FETCH** — when offset is needed (SQL Server 2012+):
```sql
SELECT * FROM [users]
ORDER BY [id]
OFFSET 10 ROWS FETCH NEXT 20 ROWS ONLY
```

OFFSET/FETCH requires an ORDER BY clause. If none is present, the syntax class injects a dummy: `ORDER BY (SELECT NULL)` (or `ORDER BY 1` for UNION queries).

TOP cannot be used with UNION — the driver detects this and falls back to OFFSET/FETCH with `OFFSET 0`.

### INSERT / Upsert

SQL Server uses `MERGE` statements for upsert, same pattern as Oracle:

```sql
MERGE INTO [table] AS target
USING (SELECT ? AS [col1], ? AS [col2]) AS source
ON (target.[key] = source.[key])
WHEN MATCHED THEN UPDATE SET target.[col1] = source.[col1]
WHEN NOT MATCHED THEN INSERT ([col1], [col2])
    VALUES (source.[col1], source.[col2]);
```

The MERGE statement requires a trailing semicolon.

Multi-row upsert uses `UNION ALL` in the USING clause:

```sql
MERGE INTO [table] AS target
USING (
    SELECT ? AS [col1], ? AS [col2]
    UNION ALL SELECT ? AS [col1], ? AS [col2]
) AS source ON (...)
WHEN MATCHED THEN UPDATE SET ...
WHEN NOT MATCHED THEN INSERT (...) VALUES (...);
```

### JSON Queries (SQL Server 2016+)

```php
// JSON_VALUE(column, '$.path') = value
$query->setJsonPathWhere('metadata', 'user.role', '=', 'admin');

// EXISTS (SELECT 1 FROM OPENJSON(column) WHERE value = ?)
$query->setJsonContainsWhere('metadata', 'admin');

// (SELECT COUNT(*) FROM OPENJSON(column, '$.path')) > 5
$query->setJsonLengthWhere('metadata', '>', 5, 'items');
```

Uses `JSON_VALUE()` for scalar extraction, `JSON_QUERY()` for objects/arrays, and `OPENJSON()` for array membership and length counting.

### Date Filtering

```php
// CAST(column AS DATE) = '2024-01-15'
$query->setDateWhere('created', '=', '2024-01-15', 'date');

// YEAR(column) = 2024
$query->setDateWhere('created', '=', 2024, 'year');
```

Uses `CAST(col AS DATE/TIME)` for date/time parts and native `YEAR()`, `MONTH()`, `DAY()` functions for extraction.

### Function Translation

| MySQL | SQL Server |
|-------|-----------|
| `CONCAT(a, b)` | `CONCAT(a, b)` (native since 2012) |
| `LENGTH(s)` | `LEN(s)` |
| `IFNULL(a, b)` | `ISNULL(a, b)` |
| `NOW()` | `GETDATE()` |
| `YEAR(col)` | `DATEPART(YEAR, col)` |
| `DATE_FORMAT(col, fmt)` | `FORMAT(col, fmt)` (2012+) |
| `SUBSTRING(s, p, n)` | `SUBSTRING(s, p, n)` (native) |
| `UNIX_TIMESTAMP(col)` | `DATEDIFF(SECOND, '1970-01-01', col)` |
| `CEIL(n)` | `CEILING(n)` |
| `MOD(a, b)` | `a % b` |
| `NEXTVAL(seq)` | `NEXT VALUE FOR [seq]` |

### String Concatenation

SQL Server 2012+ supports multi-argument `CONCAT()` natively.

### TRUNCATE

Standard SQL Server syntax:

```sql
TRUNCATE TABLE [table]
```

## DDL Generation (SqlsrvGrammar)

### CREATE TABLE

SQL Server does not support inline regular indexes. `compileCreate()` returns the CREATE TABLE statement plus separate `CREATE INDEX` statements:

```sql
CREATE TABLE [users] (
    [id] INT IDENTITY(1,1) PRIMARY KEY,
    [email] NVARCHAR(255) NOT NULL,
    [name] NVARCHAR(100),
    FOREIGN KEY ([org_id]) REFERENCES [orgs] ([id])
        ON DELETE CASCADE ON UPDATE NO ACTION
)
```

No ENGINE or CHARSET clauses.

### Auto-Increment via IDENTITY

```sql
[id] INT IDENTITY(1,1) PRIMARY KEY
```

`IDENTITY(seed, increment)` — seed is the starting value, increment is the step. The column is automatically NOT NULL and PRIMARY KEY.

`getAutoIncrement()` uses `IDENT_CURRENT(table)`. `setAutoIncrement()` uses `DBCC CHECKIDENT(table, RESEED, value)` — note it sets the current value, so the next insert gets value+1 (the driver subtracts 1 to match MySQL's behavior).

### ALTER TABLE

`compileAlterTable()` generates statements per operation:

- **ADD COLUMN** — `ALTER TABLE [t] ADD [col] type`
- **DROP COLUMN** — `ALTER TABLE [t] DROP COLUMN [col]`
- **MODIFY COLUMN** — `ALTER TABLE [t] ALTER COLUMN [col] type`
- **RENAME COLUMN** — `EXEC sp_rename 'table.old', 'new', 'COLUMN'`
- **DROP INDEX** — `DROP INDEX [name] ON [table]`
- **ADD INDEX** — `CREATE [UNIQUE] INDEX [name] ON [table] ([cols])`
- **DROP PRIMARY KEY** — dynamic: finds constraint name, then `ALTER TABLE [t] DROP CONSTRAINT [name]`
- **ADD PRIMARY KEY** — first `ALTER COLUMN ... NOT NULL` on each column, then `ADD PRIMARY KEY ([cols])`
- **DROP FOREIGN KEY** — `ALTER TABLE [t] DROP CONSTRAINT [name]`
- **ADD FOREIGN KEY** — `ALTER TABLE [t] ADD CONSTRAINT [name] FOREIGN KEY ...`

### Table and Column Rename

SQL Server uses stored procedures for renaming:

```sql
EXEC sp_rename 'old_table', 'new_table'
EXEC sp_rename 'table.old_col', 'new_col', 'COLUMN'
```

### Column Positioning

SQL Server does not support AFTER/FIRST/BEFORE. All column position methods delegate to the basic operation — columns are appended at the end.

### Fulltext Indexes

SQL Server Full-Text Search requires a multi-step setup:

1. Create a fulltext catalog: `CREATE FULLTEXT CATALOG [ft_catalog_table]`
2. Find the table's unique index (PK preferred)
3. Create the fulltext index: `CREATE FULLTEXT INDEX ON [table] ([cols]) KEY INDEX [unique_idx] ON [catalog]`

Falls back to a regular index if Full-Text Search is not installed. `isFullTextInstalled()` checks via `SERVERPROPERTY('IsFullTextInstalled')`.

### Identifier Quoting

Square brackets with doubled-closing-bracket escaping:

```
wrap('users')    →  [users]
wrap('na]me')    →  [na]]me]
```

## Schema Introspection

SQL Server uses `INFORMATION_SCHEMA` and `sys.*` catalog views:

| Method | Implementation |
|--------|---------------|
| `getTableColumns($table)` | `INFORMATION_SCHEMA.COLUMNS` with PK constraint join |
| `getTableKeys($table)` | `sys.indexes` + `sys.index_columns` + `sys.columns` |
| `getForeignKeys($table)` | `sys.foreign_keys` + `sys.foreign_key_columns` |
| `getTableList()` | `INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'` |
| `tableExists($table)` | `INFORMATION_SCHEMA.TABLES` |
| `getPrimaryKey($table)` | `sys.indexes WHERE is_primary_key = 1` |
| `getAutoIncrement($table)` | `IDENT_CURRENT(table)` |
| `getCollation()` | `DATABASEPROPERTYEX(DB_NAME(), 'Collation')` |
| `getVersion()` | `SERVERPROPERTY('ProductVersion')` |
| `getCheckConstraints($table)` | `sys.check_constraints` + `sys.tables` |
| `getDatabaseNames()` | `sys.databases WHERE database_id > 4` (excludes system DBs) |
| `getViews()` | `INFORMATION_SCHEMA.VIEWS` |

### ENUM (Not Supported)

SQL Server has no ENUM type. `getEnumValues()` returns an empty array.

### Views

- `createOrReplaceView()` — drops then creates (SQL Server has no `CREATE OR REPLACE VIEW`)
- `dropView()`, `viewExists()`, `getViews()` — queries `INFORMATION_SCHEMA.VIEWS`

### Sequences (SQL Server 2012+)

Native sequence support:

- `createSequence()` — `CREATE SEQUENCE` with START, INCREMENT, MIN, MAX, CYCLE, CACHE
- `dropSequence()` — `DROP SEQUENCE`
- `sequenceExists()` — queries `sys.sequences`
- `nextSequenceValue()` — `SELECT NEXT VALUE FOR [name]`
- `currentSequenceValue()` — `SELECT current_value FROM sys.sequences WHERE name = ?`
- `supportsSequences()` — returns true

### Feature Detection

| Method | Returns |
|--------|---------|
| `getEngine()` | `'SQL Server'` |
| `convertToCharset()` | no-op |
| `supportsIfNotExists()` | false |
| `supportsSequences()` | true |
| `supportsDropColumn()` | true |
| `supportsRenameColumn()` | true |

## Transaction Support

Nested transactions via savepoints:

```
transactionStart():
  depth 0 → connection->beginTransaction()
  depth N → SAVE TRANSACTION SP_N

transactionCommit():
  depth 0 → COMMIT
  depth N → (savepoints auto-release on commit)

transactionRollback():
  depth 0 → ROLLBACK
  depth N → ROLLBACK TRANSACTION SP_N
```

Note: `pdo_sqlsrv` rejects raw `BEGIN TRANSACTION` via prepare/execute — the driver uses PDO's native `beginTransaction()` for the outer transaction.

## Workarounds

### Automatic IDENTITY_INSERT

SQL Server requires `SET IDENTITY_INSERT table ON` before inserting explicit values into an IDENTITY column, and `SET IDENTITY_INSERT table OFF` afterward.

The `execute()` method overrides the base class to detect INSERT statements targeting IDENTITY columns. It uses regex to match INSERT patterns, queries `sys.columns WHERE is_identity = 1` (cached in `$identityColumns`), and wraps the statement:

```sql
SET IDENTITY_INSERT [table] ON;
INSERT INTO [table] ([id], [name]) VALUES (?, ?);
SET IDENTITY_INSERT [table] OFF;
```

### INSERT...SELECT with IDENTITY_INSERT

When `IDENTITY_INSERT` is ON, SQL Server requires an explicit column list even for `INSERT INTO table SELECT ...`. The driver detects this pattern and injects the column list by querying `INFORMATION_SCHEMA.COLUMNS`, then re-prepares the statement.

### OFFSET/FETCH Requires ORDER BY

SQL Server's `OFFSET...FETCH` pagination requires an ORDER BY clause. When no ORDER BY is present, the syntax class injects `ORDER BY (SELECT NULL)` as a dummy ordering. For UNION queries, it uses `ORDER BY 1` (column ordinal) since subqueries are not allowed in UNION ORDER BY.

### DBCC CHECKIDENT for Auto-Increment Reset

`setAutoIncrement()` uses `DBCC CHECKIDENT(table, RESEED, value)`. RESEED sets the current identity value, so the next insert produces value+1. The driver subtracts 1 from the requested value to align with MySQL's `ALTER TABLE AUTO_INCREMENT = N` semantics where N is the next value to be generated.

### DROP PRIMARY KEY Requires Constraint Name

SQL Server requires the constraint name to drop a primary key. The driver dynamically looks up the PK constraint name from `sys.indexes WHERE is_primary_key = 1` before issuing `ALTER TABLE ... DROP CONSTRAINT`.

### ADD PRIMARY KEY Requires NOT NULL

SQL Server requires all primary key columns to be explicitly NOT NULL. `compileAlterTable()` generates `ALTER COLUMN ... NOT NULL` statements for each column before adding the primary key constraint.

## File Summary

| File | Purpose |
|------|---------|
| `SqlsrvDriver.php` | Connection, IDENTITY handling, introspection via sys.*, sequences |
| `SqlsrvSyntax.php` | DML, TOP/OFFSET pagination, MERGE upsert, JSON, function translation |
| `SqlsrvGrammar.php` | DDL, CREATE TABLE, IDENTITY columns, sp_rename, fulltext setup |
| `BaseSqlDriver.php` | Abstract SQL contract, shared helpers, expression defaults |
| `BaseSqlSyntax.php` | Base query builder: clauses, bindings, joins, subqueries |
| `BaseSchemaGrammar.php` | Base DDL compiler: column types, modifiers, constraints |
