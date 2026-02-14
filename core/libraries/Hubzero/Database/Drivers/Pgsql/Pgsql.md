# PostgreSQL Driver

## Architecture

```
Drivers/Pgsql/
  PgsqlDriver.php  extends BaseSqlDriver
  PgsqlSyntax.php  extends BaseSqlSyntax
  PgsqlGrammar.php extends BaseSchemaGrammar
```

`BaseSqlDriver` extends `BasePdoDriver` (PDO connection wrapper), which extends `Driver` (abstract connection lifecycle, query execution, result fetching). All 12 concrete drivers follow this same three-file pattern.

The base class defaults follow MySQL conventions. The PostgreSQL driver overrides identifier quoting, expression properties, the type map, and introspection queries to use PostgreSQL's catalog system.

## Expression Overrides

| Property | PostgreSQL | Base (MySQL) Default |
|----------|-----------|---------------------|
| `$randExpression` | `RANDOM()` | `RAND()` |
| `$ifNullFunction` | `COALESCE` | `IFNULL` |
| `$wrapper` | `"%s"` | `` `%s` `` |

`$nowExpression` and `$lengthFunction` are not overridden — PostgreSQL supports `NOW()` and `CHAR_LENGTH` natively.

## Type System

PostgreSQL has a rich native type system. The grammar maps abstract types to PostgreSQL-specific types:

| Abstract Type | PostgreSQL Type | Notes |
|---------------|----------------|-------|
| `boolean` | `BOOLEAN` | Native true/false |
| `tinyInteger` | `SMALLINT` | No TINYINT; smallest is SMALLINT |
| `smallInteger` | `SMALLINT` | |
| `mediumInteger` | `INTEGER` | No MEDIUMINT |
| `integer` | `INTEGER` | |
| `bigInteger` | `BIGINT` | |
| `string` | `VARCHAR` | Requires length parameter |
| `text` .. `longText` | `TEXT` | No size variants — all unlimited |
| `float` | `REAL` | |
| `double` | `DOUBLE PRECISION` | |
| `decimal` | `DECIMAL` | |
| `datetime`, `timestamp` | `TIMESTAMP` | |
| `timestampTz` | `TIMESTAMPTZ` | Native timezone support |
| `year` | `SMALLINT` | No YEAR type |
| `binary` | `BYTEA` | Not BLOB |
| `json` | `JSONB` | Binary JSON; indexed, faster queries |
| `uuid` | `UUID` | Native UUID type |
| `ipAddress` | `INET` | Native network address type |
| `macAddress` | `MACADDR` | Native MAC address type |

Notable differences from MySQL: native `BOOLEAN`, `UUID`, `INET`, `MACADDR`, and `JSONB` types. No `TINYINT`, `MEDIUMINT`, `MEDIUMTEXT`, `LONGTEXT`, or `YEAR`. Integer display widths (`INT(11)`) and `UNSIGNED` are stripped during type mapping.

### Auto-Increment via SERIAL

PostgreSQL uses SERIAL pseudo-types instead of AUTO_INCREMENT. The grammar has a serial type map:

| Abstract Type | Serial Type |
|---------------|-------------|
| `tinyInteger`, `smallInteger` | `SMALLSERIAL` |
| `integer` | `SERIAL` |
| `bigInteger` | `BIGSERIAL` |

SERIAL columns automatically create a backing sequence and are implicitly NOT NULL.

## Connection

The constructor builds a PDO DSN:

```
pgsql:host={host};port={port};dbname={database}
```

Post-connection configuration via SQL:

- **`search_path`** — set schema if provided in options
- **`timezone`** — set session timezone if provided
- **`client_encoding`** — `SET client_encoding = 'UTF8'` via `setUTF()`

PostgreSQL does not support switching databases on an existing connection — `select()` returns false.

## Query Building (PgsqlSyntax)

### INSERT / Upsert

PostgreSQL uses `ON CONFLICT` syntax instead of MySQL's keywords:

| Operation | PostgreSQL | MySQL |
|-----------|-----------|-------|
| Ignore duplicates | `INSERT INTO ... ON CONFLICT DO NOTHING` | `INSERT IGNORE INTO` |
| Replace on conflict | `INSERT INTO ... ON CONFLICT (key) DO UPDATE SET col = EXCLUDED.col` | `REPLACE INTO` |
| Upsert | `ON CONFLICT (key) DO UPDATE SET` | `ON DUPLICATE KEY UPDATE` |

The ignore suffix is appended after VALUES, not as a keyword prefix. Upsert uses the `EXCLUDED` pseudo-table to reference new values (MySQL uses `VALUES(col)`):

```sql
INSERT INTO table (cols) VALUES (vals)
ON CONFLICT (key_col) DO UPDATE SET col = EXCLUDED.col
```

Requires specifying the conflict columns (defaults to first column if not specified).

### Pagination

PostgreSQL uses `LIMIT x OFFSET y` (same as SQLite):

```sql
LIMIT 20 OFFSET 10   -- return 20, skip 10
```

Uses `LIMIT ALL` for unlimited results.

### JSON Queries (JSONB)

PostgreSQL uses operators instead of functions for JSON operations:

```php
// column #>> '{user,name}' = value  (text extraction at path)
$query->setJsonPathWhere('metadata', 'user.name', '=', 'admin');

// column::jsonb @> '"admin"'::jsonb  (containment operator)
$query->setJsonContainsWhere('metadata', 'admin');

// jsonb_array_length(column #> '{items}') > 5
$query->setJsonLengthWhere('metadata', '>', 5, 'items');
```

Dot-notation paths are converted to PostgreSQL's array format: `user.name` becomes `{user,name}`. The `@>` containment operator requires `::jsonb` type casts.

### Date Filtering

PostgreSQL uses `EXTRACT()` and type casting:

```php
// column::date = '2024-01-15'
$query->setDateWhere('created', '=', '2024-01-15', 'date');

// EXTRACT(YEAR FROM column) = 2024
$query->setDateWhere('created', '=', 2024, 'year');
```

Supported parts: `date` (cast), `time` (cast), `year`, `month`, `day` (all via EXTRACT).

### Function Translation

The syntax class translates common MySQL function calls to PostgreSQL equivalents:

| MySQL | PostgreSQL |
|-------|-----------|
| `CONCAT(a, b)` | `concat(a, b)` |
| `LENGTH(s)` | `char_length(s)` |
| `IFNULL(a, b)` | `COALESCE(a, b)` |
| `NOW()` | `NOW()` (native) |
| `YEAR(col)` | `EXTRACT(YEAR FROM col)` |
| `DATE_FORMAT(col, fmt)` | `TO_CHAR(col, fmt)` with format conversion |
| `SUBSTRING(s, p, n)` | `SUBSTRING(s FROM p FOR n)` |
| `UNIX_TIMESTAMP(col)` | `EXTRACT(EPOCH FROM col)::INTEGER` |
| `DATE_ADD(d, n unit)` | `d + INTERVAL 'n unit'` |
| `NEXTVAL(seq)` | `nextval('seq')` |

Date format codes are translated: `%Y` → `YYYY`, `%m` → `MM`, `%d` → `DD`, `%H` → `HH24`, `%i` → `MI`, `%s` → `SS`.

### Regular Expressions

PostgreSQL has native regex operators — no custom function registration needed:

| Operator | Meaning |
|----------|---------|
| `~` | Case-sensitive match |
| `~*` | Case-insensitive match |
| `!~` | Case-sensitive non-match |
| `!~*` | Case-insensitive non-match |

### String Concatenation

PostgreSQL supports both `CONCAT()` function calls and the `||` operator. `CONCAT_WS(sep, ...)` is also native.

### TRUNCATE

PostgreSQL supports native TRUNCATE with sequence reset:

```sql
TRUNCATE TABLE table RESTART IDENTITY
```

`RESTART IDENTITY` resets any associated sequences back to their start value.

## DDL Generation (PgsqlGrammar)

### CREATE TABLE

PostgreSQL does **not** support inline regular indexes. `compileCreate()` returns an array of statements: the CREATE TABLE statement plus separate `CREATE INDEX` statements.

```sql
CREATE TABLE "users" (
    "id" SERIAL PRIMARY KEY,
    "email" VARCHAR(255) NOT NULL,
    "name" VARCHAR(100),
    CONSTRAINT "users_email_unique" UNIQUE ("email"),
    FOREIGN KEY ("org_id") REFERENCES "orgs" ("id")
        ON DELETE CASCADE
);
CREATE INDEX "idx_users_name" ON "users" ("name");
```

No ENGINE, CHARSET, or COLLATION clauses.

All three inline index compilers return null:
- `compileInlineIndex()` → null
- `compileInlineUniqueIndex()` → null
- `compileInlineFulltextIndex()` → null

### Fulltext Indexes

PostgreSQL uses GIN indexes on `to_tsvector()` for full-text search:

```sql
CREATE INDEX "ft_articles_body" ON "articles"
    USING GIN (to_tsvector('english', "body"))
```

Multi-column fulltext indexes concatenate with COALESCE for NULL safety:

```sql
USING GIN (to_tsvector('english',
    COALESCE("title", '') || ' ' || COALESCE("body", '')))
```

The language is configurable via index options (default: `'english'`).

### ALTER TABLE

`compileAlterTable()` generates appropriate statements per operation:

- **ADD COLUMN** — `ALTER TABLE ... ADD COLUMN`
- **DROP COLUMN** — `ALTER TABLE ... DROP COLUMN`
- **RENAME COLUMN** — `ALTER TABLE ... RENAME COLUMN old TO new`
- **MODIFY COLUMN** — separate statements for each aspect:
  ```sql
  ALTER TABLE t ALTER COLUMN c TYPE new_type;
  ALTER TABLE t ALTER COLUMN c SET NOT NULL;
  ALTER TABLE t ALTER COLUMN c SET DEFAULT value;
  ```
- **DROP INDEX** — `DROP INDEX IF EXISTS` (schema-scoped, not via ALTER TABLE)
- **ADD INDEX** — separate `CREATE [UNIQUE] INDEX`
- **ADD FULLTEXT** — `CREATE INDEX ... USING GIN (to_tsvector(...))`
- **DROP PRIMARY KEY** — `ALTER TABLE ... DROP CONSTRAINT table_pkey`
- **ADD PRIMARY KEY** — `ALTER TABLE ... ADD PRIMARY KEY (cols)`

### Column Positioning

PostgreSQL does **not** support AFTER/FIRST/BEFORE for column placement. `addColumnAfter()`, `addColumnBefore()`, and `addColumnFirst()` all delegate to `addColumn()` — the column is appended at the end.

### Generated Columns (PostgreSQL 12+)

```sql
ALTER TABLE t ADD COLUMN col type
    GENERATED ALWAYS AS (expression) STORED
```

PostgreSQL only supports STORED generated columns, not VIRTUAL.

## Schema Introspection

PostgreSQL uses `information_schema`, `pg_catalog`, and `pg_*` system views:

| Method | Implementation |
|--------|---------------|
| `getTableColumns($table)` | `information_schema.columns` |
| `getTableKeys($table)` | `pg_index` + `pg_class` + `pg_attribute` with `LATERAL unnest()` |
| `getForeignKeys($table)` | `information_schema.table_constraints` + `key_column_usage` + `referential_constraints` |
| `getTableList()` | `information_schema.tables WHERE table_schema = 'public'` |
| `getTableCreate($tables)` | Reconstructed from column metadata (no SHOW CREATE TABLE) |
| `tableExists($table)` | `information_schema.tables` count |
| `getCollation()` | `pg_database WHERE datname = current_database()` |
| `getPrimaryKey($table)` | `information_schema.table_constraints WHERE constraint_type = 'PRIMARY KEY'` |
| `getPrimaryKeyName($table)` | `pg_constraint WHERE contype = 'p'` |
| `getAutoIncrement($table)` | `pg_get_serial_sequence()` + sequence `last_value` |
| `setAutoIncrement($table, $val)` | `ALTER SEQUENCE ... RESTART WITH` |
| `getCharacterSet()` | `SHOW server_encoding` |
| `getIndexes($table)` | `pg_index` + `pg_class` with `array_agg(attname)` |
| `getCheckConstraints($table)` | `pg_constraint WHERE contype = 'c'` + `pg_get_constraintdef()` |
| `getServerInfo()` | `SELECT version()` with regex parse |
| `getVersion()` | `SHOW server_version` |
| `getUptime()` | `EXTRACT(EPOCH FROM (NOW() - pg_postmaster_start_time()))` |
| `getGlobalVariables()` | `SELECT name, setting FROM pg_settings` |

**Limitation:** `getTableCreate()` reconstructs basic DDL from metadata. It does not include indexes, constraints, or sequence definitions — PostgreSQL has no equivalent of MySQL's `SHOW CREATE TABLE`.

### ENUM Handling

PostgreSQL ENUMs are custom types created with `CREATE TYPE`:

- `getEnumValues()` — queries `pg_enum` joined with `pg_type` and `pg_attribute`
- `addEnumValue()` — `ALTER TYPE typename ADD VALUE 'value'`
- `removeEnumValue()` — returns false (PostgreSQL does not support removing ENUM values; requires recreating the type)

### Views

- `createOrReplaceView()` — `CREATE OR REPLACE VIEW` (native support)
- `dropView()`, `viewExists()`, `getViews()` — queries `information_schema.views`

### Sequences (Native)

PostgreSQL has full native sequence support:

- `createSequence()` — `CREATE SEQUENCE name START WITH ... INCREMENT BY ...` with optional `minvalue`, `maxvalue`, `cycle`
- `dropSequence()` — `DROP SEQUENCE IF EXISTS`
- `sequenceExists()` — queries `information_schema.sequences`
- `nextSequenceValue()` — `SELECT nextval('name')`
- `currentSequenceValue()` — `SELECT last_value FROM name`
- `supportsSequences()` — returns true

### Feature Detection

| Method | Returns |
|--------|---------|
| `getEngine()` | `'PostgreSQL'` |
| `convertToCharset()` | no-op (encoding is database-level) |
| `supportsSequences()` | true |
| `supportsGeneratedColumns()` | true (12+) |
| `supportsJson()` | true |
| `supportsWindowFunctions()` | true |
| `supportsCTE()` | true |
| `supportsDropColumn()` | true |
| `supportsRenameColumn()` | true |

## Transaction Support

Nested transactions via savepoints, same pattern as MySQL and SQLite:

```
transactionStart():
  depth 0 → BEGIN
  depth N → SAVEPOINT SP_N

transactionCommit():
  depth 0 → COMMIT
  depth N → RELEASE SAVEPOINT SP_N

transactionRollback():
  depth 0 → ROLLBACK
  depth N → ROLLBACK TO SAVEPOINT SP_N
```

`lockTable()` uses `LOCK TABLE ... IN ACCESS EXCLUSIVE MODE`. `unlockTables()` is a no-op — PostgreSQL releases all locks on COMMIT or ROLLBACK.

## Workarounds and Special Handling

PostgreSQL is the most feature-complete driver and requires the fewest workarounds.

### lastval() Exception

PostgreSQL's `lastval()` throws `SQLSTATE[55000]` when no sequence has been used in the current session. The `insertid()` method catches this and returns 0, which handles cases where rows are inserted with explicit ID values rather than relying on a SERIAL column.

### No Column Positioning

PostgreSQL does not support AFTER/FIRST/BEFORE in ALTER TABLE ADD COLUMN. All column position methods silently append the column at the end. Unlike SQLite, there is no table recreation workaround — the column order is fixed once created.

### No ENUM Value Removal

`removeEnumValue()` returns false. PostgreSQL ENUM types do not support dropping values. The workaround would be to recreate the type, which requires updating all columns that use it.

### Expression Indexes in Introspection

`getTableKeys()` uses `LATERAL unnest()` with a LEFT JOIN to handle expression indexes (e.g., GIN indexes on `to_tsvector()`). Expression indexes have `indkey` entries of 0 with no corresponding `pg_attribute` row, so the LEFT JOIN prevents them from being silently dropped.

### No SHOW CREATE TABLE

PostgreSQL has no `SHOW CREATE TABLE` equivalent. `getTableCreate()` reconstructs basic DDL from `information_schema.columns` metadata, but the result does not include indexes, foreign keys, or sequence definitions.

### Strict Type Checking

PostgreSQL is stricter about types than MySQL. The syntax class uses `NULL` instead of empty string for empty IN() sets to avoid type coercion errors when comparing against integer columns.

## File Summary

| File | Purpose |
|------|---------|
| `PgsqlDriver.php` | Connection, introspection via pg_catalog, sequences, transactions |
| `PgsqlSyntax.php` | DML, ON CONFLICT upsert, JSONB operators, function translation |
| `PgsqlGrammar.php` | DDL, CREATE TABLE, separate index statements, SERIAL types |
| `BaseSqlDriver.php` | Abstract SQL contract, shared helpers, expression defaults |
| `BaseSqlSyntax.php` | Base query builder: clauses, bindings, joins, subqueries |
| `BaseSchemaGrammar.php` | Base DDL compiler: column types, modifiers, constraints |
