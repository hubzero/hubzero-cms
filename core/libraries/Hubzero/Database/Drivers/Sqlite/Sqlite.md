# SQLite Driver

## Architecture

```
Drivers/Sqlite/
  SqliteDriver.php  extends BaseSqlDriver
  SqliteSyntax.php  extends BaseSqlSyntax
  SqliteGrammar.php extends BaseSchemaGrammar
```

`BaseSqlDriver` extends `BasePdoDriver` (PDO connection wrapper), which extends `Driver` (abstract connection lifecycle, query execution, result fetching). All 12 concrete drivers follow this same three-file pattern.

The base class defaults follow MySQL conventions. The SQLite driver overrides expression properties, the type map, and many methods to account for SQLite's simplified type system, limited ALTER TABLE support, and missing SQL features.

## Expression Overrides

SQLite overrides several base class defaults:

| Property | SQLite | Base (MySQL) Default |
|----------|--------|---------------------|
| `$nowExpression` | `datetime('now')` | `NOW()` |
| `$randExpression` | `RANDOM()` | `RAND()` |
| `$lengthFunction` | `LENGTH` | `CHAR_LENGTH` |
| `$wrapper` | `` `%s` `` | `` `%s` `` (same) |

## Type System

SQLite uses type affinity — every value is stored as one of four storage classes regardless of the declared column type. The type map collapses all abstract types down:

| Abstract Type | SQLite Type | Notes |
|---------------|------------|-------|
| `boolean` | `INTEGER` | `1`/`0` values, no native boolean |
| `tinyInteger` .. `bigInteger` | `INTEGER` | All integer sizes identical |
| `string`, `char` | `TEXT` | Length parameters accepted but ignored |
| `text`, `mediumText`, `longText` | `TEXT` | No size variants — all unlimited |
| `float`, `double`, `decimal` | `REAL` | No precision enforcement |
| `date`, `time`, `datetime`, `timestamp` | `TEXT` | Stored as ISO-8601 strings |
| `year` | `INTEGER` | |
| `json` | `TEXT` | JSON1 extension validates via functions |
| `uuid`, `ulid`, `ipAddress` | `TEXT` | |
| `binary` | `BLOB` | |

`requiresStringLength()` returns false — SQLite ignores length parameters like `VARCHAR(255)`.

## Connection

The constructor builds a simple PDO DSN:

```
sqlite:{database_path}
```

After connection, `configureSqlite()` sets PRAGMAs:

- **`foreign_keys = ON`** — enabled by default (SQLite disables them by default)
- **`journal_mode`** — WAL recommended for better concurrency (options: DELETE, TRUNCATE, PERSIST, MEMORY, WAL, OFF)
- **`busy_timeout = 5000`** — milliseconds to wait for file locks (default 5 seconds)
- **`synchronous`** — durability vs performance tradeoff (options: OFF, NORMAL, FULL, EXTRA)

## Query Building (SqliteSyntax)

### INSERT / Upsert

SQLite uses different conflict-handling keywords from MySQL:

| Operation | SQLite | MySQL |
|-----------|--------|-------|
| Ignore duplicates | `INSERT OR IGNORE INTO` | `INSERT IGNORE INTO` |
| Replace on conflict | `INSERT OR REPLACE INTO` | `REPLACE INTO` |
| Upsert | `ON CONFLICT (...) DO UPDATE SET` | `ON DUPLICATE KEY UPDATE` |

Upsert requires SQLite 3.24.0+ and uses the `ON CONFLICT` clause:

```sql
INSERT INTO table (cols) VALUES (vals)
ON CONFLICT (key_col) DO UPDATE SET col = excluded.col, ...
```

### Pagination

SQLite uses `LIMIT x OFFSET y` (same as PostgreSQL, different from MySQL's `LIMIT y, x`):

```sql
LIMIT 20 OFFSET 10   -- return 20, skip 10
```

Supports `-1` as an unlimited limit.

### JSON Queries (SQLite 3.9.0+ with JSON1)

```php
// json_extract(column, '$.path') = value
$query->setJsonPathWhere('metadata', 'user.role', '=', 'admin');

// EXISTS (SELECT 1 FROM json_each(column) WHERE value = ?)
$query->setJsonContainsWhere('metadata', 'admin', 'roles');

// json_array_length(column, '$.path') > 5
$query->setJsonLengthWhere('metadata', '>', 5, 'items');
```

Note: `setJsonContainsWhere()` uses `json_each()` with a subquery since SQLite has no `JSON_CONTAINS()` function.

### Date Filtering

SQLite stores dates as TEXT and uses `strftime()` for extraction:

```php
// date(column) = '2024-01-15'
$query->setDateWhere('created', '=', '2024-01-15', 'date');

// CAST(strftime('%Y', column) AS INTEGER) = 2024
$query->setDateWhere('created', '=', 2024, 'year');
```

Supported parts: `date`, `time`, `year`, `month`, `day`. Year/month/day use `CAST(strftime(...) AS INTEGER)` to enable numeric comparison.

### String Concatenation

SQLite uses the `||` operator instead of MySQL's `CONCAT()` function. `CONCAT_WS(sep, ...)` is emulated by interleaving the separator between `||` operands.

### Function Translation

The syntax class translates common MySQL function calls to SQLite equivalents:

| MySQL | SQLite |
|-------|--------|
| `CONCAT(a, b)` | `a \|\| b` |
| `IFNULL(a, b)` | `COALESCE(a, b)` |
| `NOW()` | `datetime('now')` |
| `CURRENT_DATE` | `date('now')` |
| `YEAR(col)` | `CAST(strftime('%Y', col) AS INTEGER)` |
| `DATE_FORMAT(col, fmt)` | `strftime(fmt, col)` with format conversion |
| `SUBSTRING(s, p, n)` | `substr(s, p, n)` |
| `MOD(a, b)` | `a % b` |

### TRUNCATE

SQLite has no `TRUNCATE TABLE`. The syntax class emits two statements:

```sql
DELETE FROM table;
DELETE FROM sqlite_sequence WHERE name = 'table';
```

The second statement resets the auto-increment counter.

### REGEXP

SQLite has no built-in REGEXP operator. The driver registers a custom function via `PDO::sqliteCreateFunction` that uses PHP's `preg_match()`:

```php
$connection->sqliteCreateFunction('regexp', function ($pattern, $value) {
    return (int) preg_match('/' . $pattern . '/i', $value);
});
```

This enables `column REGEXP pattern` in WHERE clauses but runs in PHP — it cannot use indexes.

## DDL Generation (SqliteGrammar)

### CREATE TABLE

SQLite supports inline UNIQUE constraints but **not** inline regular indexes. `compileCreate()` returns an array: the CREATE TABLE statement plus separate `CREATE INDEX` statements.

```sql
CREATE TABLE `users` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `email` TEXT NOT NULL,
    `name` TEXT,
    UNIQUE (`email`),
    FOREIGN KEY (`org_id`) REFERENCES `orgs` (`id`) ON DELETE CASCADE
);
CREATE INDEX `idx_users_name` ON `users` (`name`);
```

No ENGINE, CHARSET, or COLLATION clauses — SQLite always uses UTF-8.

### Auto-Increment

SQLite's auto-increment syntax differs from MySQL:

- MySQL: `INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY`
- SQLite: `INTEGER PRIMARY KEY AUTOINCREMENT`

The column **must** be declared as `INTEGER` (not `INT`) and must be the `PRIMARY KEY`. `AUTOINCREMENT` is optional — without it, SQLite reuses deleted rowids.

### ALTER TABLE Limitations

SQLite's native ALTER TABLE only supports:

- `ADD COLUMN` (with restrictions: NOT NULL columns must have a DEFAULT)
- `RENAME TABLE`
- `RENAME COLUMN` (3.25.0+)
- `DROP COLUMN` (3.35.0+)

All other operations — MODIFY COLUMN, column repositioning, DROP/ADD PRIMARY KEY — require **table recreation**: create a new table with the desired schema, copy data, drop the old table, rename the new table. The grammar's `compileAlterTable()` detects when a rebuild is needed and handles it transparently.

The table rebuild process:

1. Create temporary table with new schema
2. Copy data from old table (mapping columns)
3. Drop old table
4. Rename temporary table to original name
5. Recreate all indexes

This runs inside a transaction for safety.

### Column Positioning

SQLite has no AFTER/FIRST/BEFORE syntax for column placement. The driver supports `addColumnAfter()`, `addColumnBefore()`, and `addColumnFirst()` transparently via table recreation — the column order is defined by the rebuilt CREATE TABLE statement.

### Fulltext Indexes

SQLite has no FULLTEXT index type. `addFulltextIndex()` creates a regular index instead. True full-text search requires SQLite's FTS5 virtual table extension, which is a separate feature.

## Schema Introspection

SQLite uses `PRAGMA` commands and `sqlite_master` for introspection:

| Method | Implementation |
|--------|---------------|
| `getTableColumns($table)` | `PRAGMA table_info()` |
| `getTableKeys($table)` | `PRAGMA index_list()` + `PRAGMA index_info()` |
| `getForeignKeys($table)` | `PRAGMA foreign_key_list()` |
| `getTableList()` | `SELECT name FROM sqlite_master WHERE type='table'` |
| `getTableCreate($tables)` | `SELECT sql FROM sqlite_master` |
| `tableExists($table)` | `SELECT COUNT(*) FROM sqlite_master` |
| `getPrimaryKey($table)` | First `pk != 0` from `PRAGMA table_info()` |
| `getPrimaryKeyColumns($table)` | All PK columns sorted by position |
| `getAutoIncrement($table)` | Reads `sqlite_sequence` table or `MAX(rowid)` |
| `setAutoIncrement($table, $val)` | Updates/inserts into `sqlite_sequence` |
| `getCharacterSet()` | `PRAGMA encoding` (always UTF-8) |
| `getIndexes($table)` | `PRAGMA index_list()` + `PRAGMA index_info()` |
| `getCheckConstraints($table)` | Parses CREATE TABLE SQL from `sqlite_master` |

### ENUM (No-Op)

SQLite has no ENUM type. `getEnumValues()` returns an empty array. `addEnumValue()` and `removeEnumValue()` are no-ops that return true. ENUM columns are stored as TEXT with no enforcement.

### Views

- `createOrReplaceView()` — drops then creates (SQLite has no `CREATE OR REPLACE VIEW`)
- `dropView()`, `viewExists()`, `getViews()` — queries `sqlite_master WHERE type='view'`

### Feature Detection

| Method | Returns | Notes |
|--------|---------|-------|
| `getEngine()` | `'SQLite'` | No storage engines |
| `convertToCharset()` | no-op | Always UTF-8 |
| `supportsDropColumn()` | true for 3.35.0+ | |
| `supportsRenameColumn()` | true for 3.25.0+ | |
| `supportsColumnPositioning()` | true | Via transparent table recreation |

## Transaction Support

Nested transactions are implemented via savepoints, same as MySQL:

```
transactionStart():
  depth 0 → BEGIN TRANSACTION
  depth N → SAVEPOINT SP_N

transactionCommit():
  depth 0 → COMMIT
  depth N → RELEASE SAVEPOINT SP_N

transactionRollback():
  depth 0 → ROLLBACK
  depth N → ROLLBACK TO SAVEPOINT SP_N
```

SQLite uses file-level locking. `lockTable()` and `unlockTables()` are no-ops.

## Workarounds and Emulations

SQLite requires more workarounds than any other supported driver due to its minimal SQL feature set.

### RIGHT JOIN Emulation

SQLite does not support `RIGHT JOIN`. The syntax class rewrites it by swapping the FROM table with the JOIN target and reversing the key order, then using a `LEFT JOIN`:

```sql
-- What the caller writes:
SELECT * FROM a RIGHT JOIN b ON a.id = b.a_id

-- What SQLite executes:
SELECT * FROM b LEFT JOIN a ON a.id = b.a_id
```

### FULL OUTER JOIN Emulation

Same approach as MySQL — rewrites as a UNION of a LEFT JOIN and a swapped LEFT JOIN (since RIGHT JOIN is also unavailable) with an IS NULL filter:

```sql
SELECT * FROM a LEFT JOIN b ON a.id = b.a_id
UNION ALL
SELECT * FROM b LEFT JOIN a ON a.id = b.a_id WHERE a.id IS NULL
```

The `$fullJoinUnionBuilt` flag suppresses duplicate clause output during UNION construction.

**Limitation:** Single FULL JOIN between two base tables only.

### UPDATE with JOIN

SQLite does not support `UPDATE ... JOIN` directly. The syntax class rewrites it as a subquery using `rowid`:

```sql
-- What the caller writes:
UPDATE a JOIN b ON ... SET a.col = value WHERE ...

-- What SQLite executes:
UPDATE a SET col = value WHERE rowid IN (
    SELECT a.rowid FROM a JOIN b ON ... WHERE ...
)
```

### Sequence Emulation

Same table-based emulation as MySQL, using a `_sequences` table created on first use. The atomic increment uses UPDATE + SELECT:

```sql
UPDATE _sequences
   SET current_value = current_value + increment_value
 WHERE name = ?;
SELECT current_value FROM _sequences WHERE name = ?;
```

SQLite's single-writer guarantee means no explicit row locking is needed — only one connection can write at a time. This is simpler than MySQL's `LAST_INSERT_ID(expr)` trick but has the same two-round-trip cost.

### ALTER TABLE via Table Recreation

Operations not natively supported (MODIFY COLUMN, column positioning, DROP COLUMN on < 3.35.0, DROP/ADD PRIMARY KEY) are handled by rebuilding the table:

1. `BEGIN TRANSACTION`
2. `CREATE TABLE _temp_table (...)`  — new schema
3. `INSERT INTO _temp_table SELECT ... FROM original` — copy data
4. `DROP TABLE original`
5. `ALTER TABLE _temp_table RENAME TO original`
6. Recreate all indexes
7. `COMMIT`

This is atomic within the transaction but involves a full table copy, which can be slow on large tables.

## File Summary

| File | Purpose |
|------|---------|
| `SqliteDriver.php` | Connection, PRAGMAs, introspection, table recreation, sequences |
| `SqliteSyntax.php` | DML, JOIN emulation, JSON, function translation, type affinity |
| `SqliteGrammar.php` | DDL, CREATE TABLE, table rebuild for ALTER, type mapping |
| `BaseSqlDriver.php` | Abstract SQL contract, shared helpers, expression defaults |
| `BaseSqlSyntax.php` | Base query builder: clauses, bindings, joins, subqueries |
| `BaseSchemaGrammar.php` | Base DDL compiler: column types, modifiers, constraints |
