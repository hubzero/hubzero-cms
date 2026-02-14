# MariaDB Driver

MariaDB is a community-developed fork of MySQL. This driver inherits all MySQL behaviors — the grammar class is an empty subclass with no overrides, and the syntax class adds only sequence function expressions. The driver class adds support for MariaDB-specific storage engines, native sequences, system-versioned tables, enforced CHECK constraints, invisible columns, Galera Cluster status, plugin management, and MaxScale proxy detection.

## Architecture

```
Drivers/Mariadb/
  MariadbDriver.php  extends MysqlDriver
  MariadbSyntax.php  extends MysqlSyntax   (minimal overrides)
  MariadbGrammar.php extends MysqlGrammar  (no overrides)
```

All DDL generation, type mappings, introspection, pagination, upsert, and transaction handling are identical to the MySQL driver.

## Differences from MySQL

### Additional Storage Engines

`setEngine()` accepts MariaDB-specific engines alongside the standard MySQL set:

| Engine | Notes |
|--------|-------|
| `aria` | Crash-safe MyISAM replacement; default for system tables |
| `columnstore` | Columnar storage for analytics (formerly InfiniDB) |
| `spider` | Sharding engine for table partitioning across servers |
| `s3` | Store tables on S3-compatible storage (10.5+) |
| `mroonga` | Full-text search with CJK support |
| `connect` | Access external data sources (CSV, XML, ODBC) |
| `sequence` | Engine backing `CREATE SEQUENCE` objects |
| `rocksdb` | LSM-tree storage engine |
| `tokudb` | Fractal tree indexing (deprecated) |
| `oqgraph` | Open Query Graph for hierarchical data |
| `sphinx` | SphinxSE for Sphinx full-text search integration |
| `blackhole` | Accepts data but stores nothing (replication use) |

`hasEngine()` uses case-insensitive comparison against `SHOW ENGINES` output.

### Native Sequences (10.3+)

MariaDB 10.3+ supports `CREATE SEQUENCE` natively, unlike MySQL which requires table-based emulation. `supportsSequences()` checks the server version.

| Method | SQL |
|--------|-----|
| `createSequence()` | `CREATE SEQUENCE IF NOT EXISTS ...` |
| `dropSequence()` | `DROP SEQUENCE [IF EXISTS] ...` |
| `nextSequenceValue()` | `SELECT NEXTVAL(seq)` |
| `currentSequenceValue()` | `SELECT LASTVAL(seq)` |
| `setSequenceValue()` | `SELECT SETVAL(seq, n)` |
| `sequenceExists()` | `INFORMATION_SCHEMA.TABLES` where `TABLE_TYPE = 'SEQUENCE'` |
| `getSequences()` | `INFORMATION_SCHEMA.TABLES` where `TABLE_TYPE = 'SEQUENCE'` |

The syntax class overrides `buildFunctionExpression()` to emit native `NEXTVAL()` and `LASTVAL()` for sequence expressions when the server supports them, falling back to the MySQL table-based emulation otherwise.

### System-Versioned Tables (10.3.4+)

MariaDB supports temporal tables that automatically track row history:

| Method | SQL |
|--------|-----|
| `addSystemVersioning()` | `ALTER TABLE t ADD SYSTEM VERSIONING` |
| `dropSystemVersioning()` | `ALTER TABLE t DROP SYSTEM VERSIONING` |
| `hasSystemVersioning()` | Checks for `GENERATED ALWAYS AS ROW` in column extras |

### CHECK Constraints (Enforced)

Unlike MySQL (which parses but silently ignores CHECK constraints), MariaDB enforces them:

| Method | SQL |
|--------|-----|
| `addCheckConstraint()` | `ALTER TABLE t ADD CONSTRAINT name CHECK (expr)` |
| `dropCheckConstraint()` | `ALTER TABLE t DROP CONSTRAINT name` |
| `getCheckConstraints()` | `INFORMATION_SCHEMA.CHECK_CONSTRAINTS` |

### Invisible Columns (10.3.3+)

Columns can be excluded from `SELECT *`:

| Method | Effect |
|--------|--------|
| `makeColumnInvisible()` | `ALTER TABLE t MODIFY COLUMN col ... INVISIBLE` |
| `makeColumnVisible()` | `ALTER TABLE t MODIFY COLUMN col ... VISIBLE` |

### CAST Keyword

`getIntegerCastKeyword()` returns `INTEGER` instead of the MySQL default `SIGNED`.

### Galera Cluster Status

`getGaleraStatus()` queries `SHOW STATUS LIKE 'wsrep_%'` to return cluster status: size, state UUID, node status, connected/ready state, flow control pause ratio. `isGaleraCluster()` returns a boolean.

### MaxScale Proxy Detection

`isMaxScaleConnection()` checks for `@@maxscale_version` to detect if the connection is routed through MariaDB MaxScale.

### Plugin Management

| Method | SQL |
|--------|-----|
| `getPlugins()` | `SHOW PLUGINS` |
| `hasPlugin()` | Checks plugin status is `ACTIVE` |
| `installPlugin()` | `INSTALL SONAME` (MariaDB registers all plugins from the library) |
| `installSoname()` | `INSTALL SONAME` (MariaDB-specific, no plugin name needed) |
| `uninstallPlugin()` | `UNINSTALL PLUGIN` |
| `uninstallSoname()` | `UNINSTALL SONAME` (removes all plugins from a library) |

### Server Info

`getServerInfo()` parses `SHOW VARIABLES LIKE '%version%'` and returns MariaDB-specific version fields (e.g., `10.5.12-MariaDB`). `getMajorVersion()` extracts the major.minor version as a float.

### JSON Table (10.6+)

`sqlJsonTable()` generates `JSON_TABLE()` SQL for converting JSON data to relational format.

## File Summary

| File | Purpose |
|------|---------|
| `MariadbDriver.php` | Storage engine validation, native sequences, system versioning, CHECK constraints, invisible columns, Galera status, MaxScale detection, plugin management |
| `MariadbSyntax.php` | Sequence function expressions (`NEXTVAL`/`LASTVAL`) with version-gated fallback to MySQL emulation |
| `MariadbGrammar.php` | Empty subclass — inherits all MySQL DDL behavior |
