# Oracle Driver

**This is a proof-of-concept driver.** It passed all tests when originally written but is not maintained on a regular testing cadence. It should not be considered production-ready without re-validation against the current test suite.

## Architecture

```
Drivers/Oci/
  OciDriver.php  extends BaseSqlDriver
  OciSyntax.php  extends BaseSqlSyntax
  OciGrammar.php extends BaseSchemaGrammar
```

`BaseSqlDriver` extends `BasePdoDriver` (PDO connection wrapper), which extends `Driver` (abstract connection lifecycle, query execution, result fetching). All 12 concrete drivers follow this same three-file pattern.

The base class defaults follow MySQL conventions. The Oracle driver overrides nearly everything — expression properties, type maps, identifier quoting (uppercase + double-quote), introspection queries (Oracle data dictionary), and parameter binding (to work around PDO_OCI limitations).

## Expression Overrides

| Property | Oracle | Base (MySQL) Default |
|----------|--------|---------------------|
| `$nowExpression` | `SYSDATE` | `NOW()` |
| `$randExpression` | `DBMS_RANDOM.VALUE` | `RAND()` |
| `$lengthFunction` | `LENGTH` | `CHAR_LENGTH` |
| `$ifNullFunction` | `NVL` | `IFNULL` |
| `$wrapper` | `"` (double-quote) | `` ` `` |

## Type System

Oracle uses `NUMBER(precision)` for all integer types and `CLOB` for all text variants:

| Abstract Type | Oracle Type | Notes |
|---------------|------------|-------|
| `boolean` | `NUMBER(1)` | `1`/`0` values |
| `tinyInteger` | `NUMBER(3)` | |
| `smallInteger` | `NUMBER(5)` | |
| `mediumInteger` | `NUMBER(7)` | |
| `integer` | `NUMBER(10)` | |
| `bigInteger` | `NUMBER(19)` | |
| `string` | `VARCHAR2` | Max 4000 bytes |
| `char` | `CHAR` | |
| `tinyText` | `VARCHAR2(255)` | Avoids CLOB overhead for small text |
| `text` .. `longText` | `CLOB` | Requires LOB binding for >4000 bytes |
| `float` | `FLOAT` | |
| `double` | `DOUBLE PRECISION` | |
| `decimal` | `NUMBER` | With precision/scale |
| `date` | `DATE` | Includes time component in Oracle |
| `time`, `datetime`, `timestamp` | `TIMESTAMP` | |
| `timestampTz` | `TIMESTAMP WITH TIME ZONE` | |
| `year` | `NUMBER(4)` | |
| `binary` | `BLOB` | |
| `json` | `CLOB` | No native JSON type before 21c |
| `uuid` | `CHAR(36)` | |

## Connection

The constructor supports three DSN formats:

- Easy Connect: `oci:dbname=//{host}:{port}/{service}?charset=UTF8`
- TNS Name: `oci:dbname={tns_name}?charset=UTF8`
- Custom PDO DSN

After connection, NLS session parameters are configured:

```sql
ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'
ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'
ALTER SESSION SET NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS TZH:TZM'
```

Column names are forced to lowercase via `PDO::ATTR_CASE = CASE_LOWER` for ORM compatibility.

`select($database)` uses `ALTER SESSION SET CURRENT_SCHEMA` — Oracle connects to a schema, not a database.

## Identifier Quoting

Oracle stores unquoted identifiers as **uppercase**. The driver uppercases all identifiers before quoting with double quotes:

```
quoteName('users')  →  "USERS"
quoteName('email')  →  "EMAIL"
```

Oracle has many reserved words (`ACCESS`, `COMMENT`, `LEVEL`, `SIZE`, etc.) that require quoting. The syntax class quotes all column references in SELECT clauses to avoid `ORA-00936` errors.

**Table aliases use space, not AS** — Oracle prior to 23ai does not support `AS` for table aliases:

```sql
SELECT * FROM "USERS" "U"    -- correct
SELECT * FROM "USERS" AS "U" -- ORA-00933 in Oracle <23ai
```

## Query Building (OciSyntax)

### INSERT / Upsert

Oracle uses `MERGE` statements for upsert operations:

```sql
MERGE INTO "TABLE" t
USING (SELECT ? AS "COL1", ? AS "COL2" FROM DUAL) s
ON (t."KEY" = s."KEY")
WHEN MATCHED THEN UPDATE SET t."COL1" = s."COL1"
WHEN NOT MATCHED THEN INSERT ("COL1", "COL2")
    VALUES (s."COL1", s."COL2")
```

Multi-row upsert uses `UNION ALL ... FROM DUAL` in the USING clause.

### Bulk INSERT

Oracle uses `INSERT ALL` for multi-row inserts:

```sql
INSERT ALL
    INTO "TABLE" ("COL1", "COL2") VALUES (?, ?)
    INTO "TABLE" ("COL1", "COL2") VALUES (?, ?)
SELECT 1 FROM DUAL
```

**Limitation:** `INSERT ALL` generates the same identity value for all rows when the table has an identity column. The driver detects this via `user_tab_identity_cols` and falls back to row-by-row inserts.

### Pagination

Oracle 12c+ uses SQL:2008 syntax:

```sql
OFFSET 10 ROWS FETCH FIRST 20 ROWS ONLY
```

### UPDATE with JOIN

Oracle does not support `UPDATE ... JOIN`. The syntax class rewrites it as a ROWID subquery:

```sql
UPDATE "TABLE" SET "COL" = ?
WHERE ROWID IN (
    SELECT "TABLE".ROWID FROM "TABLE"
    JOIN "OTHER" ON ...
    WHERE ...
)
```

### FROM DUAL

Oracle requires `FROM DUAL` for SELECT statements without a table:

```sql
SELECT SYSDATE FROM DUAL
```

The syntax class detects bare SELECTs and appends `FROM DUAL` automatically.

### JSON Queries (Oracle 12c+)

```php
// JSON_VALUE(column, '$.path') = value
$query->setJsonPathWhere('metadata', 'user.role', '=', 'admin');

// JSON_EXISTS(column, '$.path?(@ == $v)' PASSING ? AS "v")
$query->setJsonContainsWhere('metadata', 'admin', 'roles');

// (SELECT COUNT(*) FROM JSON_TABLE(...)) > 5
$query->setJsonLengthWhere('metadata', '>', 5, 'items');
```

JSON length uses a `JSON_TABLE` subquery with COUNT since Oracle has no `json_array_length()`.

### Date Filtering

```php
// TRUNC(column) = TO_DATE(?, 'YYYY-MM-DD')
$query->setDateWhere('created', '=', '2024-01-15', 'date');

// EXTRACT(YEAR FROM column) = 2024
$query->setDateWhere('created', '=', 2024, 'year');
```

### Function Translation

| MySQL | Oracle |
|-------|--------|
| `CONCAT(a, b, c)` | `(a \|\| b \|\| c)` (Oracle CONCAT takes only 2 args) |
| `IFNULL(a, b)` | `NVL(a, b)` |
| `NOW()` | `SYSTIMESTAMP` |
| `CURRENT_DATE` | `SYSDATE` |
| `YEAR(col)` | `EXTRACT(YEAR FROM col)` |
| `DATE_FORMAT(col, fmt)` | `TO_CHAR(col, fmt)` with format conversion |
| `SUBSTRING(s, p, n)` | `SUBSTR(s, p, n)` |
| `UNIX_TIMESTAMP(col)` | `(col - TO_DATE('1970-01-01', 'YYYY-MM-DD')) * 86400` |
| `NEXTVAL(seq)` | `seq.NEXTVAL` |
| `REGEXP` | `REGEXP_LIKE(col, pattern)` |

### String Concatenation

Oracle uses the `||` operator. `CONCAT()` is available but only accepts two arguments — the syntax class uses `||` for multi-argument concatenation.

## DDL Generation (OciGrammar)

### CREATE TABLE

Oracle does not support inline indexes. `compileCreate()` returns the CREATE TABLE statement plus separate `CREATE INDEX` statements:

```sql
CREATE TABLE "USERS" (
    "ID" NUMBER(10) GENERATED BY DEFAULT AS IDENTITY,
    "EMAIL" VARCHAR2(255) NOT NULL,
    "NAME" VARCHAR2(100),
    PRIMARY KEY ("ID"),
    CONSTRAINT "FK_USERS_ORG" FOREIGN KEY ("ORG_ID")
        REFERENCES "ORGS" ("ID") ON DELETE CASCADE
)
```

**Foreign key limitation:** Oracle only supports `ON DELETE` actions. `ON UPDATE` is not available — the grammar silently omits it.

### DROP TABLE

```sql
DROP TABLE "TABLE" CASCADE CONSTRAINTS PURGE
```

`CASCADE CONSTRAINTS` drops dependent foreign keys. `PURGE` bypasses the recycle bin. Oracle prior to 23c has no `DROP TABLE IF EXISTS`.

### Auto-Increment via IDENTITY (Oracle 12c+)

```sql
"ID" NUMBER(10) GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY
```

For tables created before 12c, conventional sequences with triggers are used instead. The driver also supports standalone sequences for explicit use.

### ALTER TABLE

`compileAlterTable()` generates statements per operation:

- **ADD COLUMN** — `ALTER TABLE ... ADD ("COL" TYPE)`
- **DROP COLUMN** — `ALTER TABLE ... DROP ("COL1", "COL2")`
- **MODIFY COLUMN** — `ALTER TABLE ... MODIFY ("COL" TYPE)`
- **RENAME COLUMN** — `ALTER TABLE ... RENAME COLUMN "OLD" TO "NEW"`
- **DROP PRIMARY KEY** — `ALTER TABLE ... DROP PRIMARY KEY CASCADE`
- **ADD PRIMARY KEY** — `ALTER TABLE ... ADD PRIMARY KEY ("COLS")`
- **DROP INDEX** — `DROP INDEX "NAME"`
- **ADD INDEX** — `CREATE [UNIQUE] INDEX "NAME" ON "TABLE" ("COLS")`
- **DROP FOREIGN KEY** — `ALTER TABLE ... DROP CONSTRAINT "NAME"`
- **ADD FOREIGN KEY** — `ALTER TABLE ... ADD CONSTRAINT ... FOREIGN KEY ...`

### Column Positioning

Oracle does not support AFTER/FIRST/BEFORE. All column position methods delegate to the basic operation — columns are appended at the end.

### Fulltext Indexes

Oracle Text (`CTXSYS.CONTEXT`) is used when available:

```sql
CREATE INDEX "FT_NAME" ON "TABLE" ("COL")
    INDEXTYPE IS CTXSYS.CONTEXT
```

Falls back to a regular index if CTXSYS is not installed.

## Schema Introspection

Oracle uses data dictionary views (`user_*` prefix for current schema):

| Method | Implementation |
|--------|---------------|
| `getTableColumns($table)` | `user_tab_columns` with type formatting |
| `getTableKeys($table)` | `user_indexes` + `user_ind_columns` + `user_constraints` |
| `getForeignKeys($table)` | `user_constraints` where `constraint_type = 'R'` + `user_cons_columns` |
| `getTableList()` | `user_tables` |
| `tableExists($table)` | `user_tables` count |
| `getPrimaryKey($table)` | `user_constraints` where `constraint_type = 'P'` |
| `getAutoIncrement($table)` | `user_tab_identity_cols` or sequence naming convention |
| `setAutoIncrement($table, $val)` | `ALTER SEQUENCE ... RESTART` or identity modification |
| `getCollation()` | `v$nls_parameters WHERE parameter = 'NLS_SORT'` |
| `getCharacterSet()` | `nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'` |
| `getCheckConstraints($table)` | `user_constraints WHERE constraint_type = 'C'` |
| `getVersion()` | `PRODUCT_COMPONENT_VERSION` or PDO attribute |
| `getUptime()` | `v$instance` startup_time calculation |
| `getGlobalVariables()` | `v$parameter` |
| `getDatabaseNames()` | `all_users` (schemas = databases in Oracle) |

All table and column names must be uppercased in queries since Oracle stores unquoted identifiers as uppercase.

### ENUM (Emulated)

Oracle has no ENUM type. The type map converts ENUM to `VARCHAR2(255)`. `getEnumValues()` queries `user_constraints` for CHECK constraints that match the `IN (...)` pattern.

### Views

- `createOrReplaceView()` — `CREATE OR REPLACE VIEW` (native support)
- `dropView()` — checks existence first (no `IF EXISTS` before 23c)
- `viewExists()`, `getViews()` — queries `user_views`

### Sequences (Native)

Oracle has full native sequence support:

- `createSequence()` — `CREATE SEQUENCE` with START, INCREMENT, MIN, MAX, CYCLE, CACHE options
- `dropSequence()` — `DROP SEQUENCE`
- `sequenceExists()` — queries `user_sequences`
- `nextSequenceValue()` — `SELECT seq.NEXTVAL FROM DUAL`
- `currentSequenceValue()` — `SELECT seq.CURRVAL FROM DUAL`

## Transaction Support

Nested transactions via savepoints:

```
transactionStart():
  depth 0 → connection->beginTransaction()
  depth N → SAVEPOINT SP_N

transactionCommit():
  depth 0 → COMMIT
  depth N → (release is implicit in Oracle)

transactionRollback():
  depth 0 → ROLLBACK
  depth N → ROLLBACK TO SAVEPOINT SP_N
```

`lockTable()` uses `LOCK TABLE ... IN EXCLUSIVE MODE`.

## Workarounds

The Oracle driver has the most workarounds of any driver due to PDO_OCI limitations and Oracle's unique SQL dialect.

### Numeric Parameter Binding (PDO_OCI)

PDO_OCI sends all bound parameters as VARCHAR2, causing `ORA-00932` (inconsistent datatypes) when placeholders appear in numeric contexts (e.g., `COALESCE(number_col, ?)`).

The `bind()` method works around this by inlining numeric values directly as literals in the SQL string. It parses the SQL character-by-character to avoid replacing `?` inside string literals. Strings, nulls, and booleans continue using standard parameter binding.

### Large String Binding (>4000 bytes)

Oracle's VARCHAR2 has a 4000-byte limit. Strings exceeding this must be bound as LOB parameters with `PARAM_LOB` and an explicit length, or they trigger `ORA-01461` (can only bind a LONG value for insert into a LONG column).

`bindWithLobs()` detects strings over 4000 bytes and uses `bindParam()` with explicit length. References are stored in `$lobStreams` to prevent garbage collection during execution.

### CLOB Stream Conversion

Oracle PDO returns CLOB values as PHP stream resources instead of strings. `convertLobs()` intercepts fetch results and calls `stream_get_contents()` to convert streams to strings transparently. This is applied in `fetchObject()`, `fetchArray()`, and `fetchAssoc()`.

### Last Insert ID

PDO_OCI's `lastInsertId()` does not work with identity columns. The driver captures the table name from INSERT statements via regex, looks up the identity sequence name from `user_tab_identity_cols`, and calls `.CURRVAL` from DUAL.

### INSERT ALL Identity Conflict

`INSERT ALL` generates the same identity value for all rows when the table has an identity column. The driver queries `user_tab_identity_cols` to detect this and falls back to row-by-row inserts.

### Reserved Word Quoting

Oracle has an extensive reserved word list. The syntax class quotes all column references in SELECT clauses, not just known reserved words, to avoid runtime errors.

## File Summary

| File | Purpose |
|------|---------|
| `OciDriver.php` | Connection, NLS config, introspection via data dictionary, sequences, LOB handling |
| `OciSyntax.php` | DML, MERGE upsert, INSERT ALL, ROWID subqueries, function translation |
| `OciGrammar.php` | DDL, CREATE TABLE, NUMBER types, IDENTITY columns, separate indexes |
| `BaseSqlDriver.php` | Abstract SQL contract, shared helpers, expression defaults |
| `BaseSqlSyntax.php` | Base query builder: clauses, bindings, joins, subqueries |
| `BaseSchemaGrammar.php` | Base DDL compiler: column types, modifiers, constraints |
