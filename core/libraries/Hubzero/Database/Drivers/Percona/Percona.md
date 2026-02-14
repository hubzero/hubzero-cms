# Percona Driver

Percona Server for MySQL is a drop-in replacement for MySQL. This driver inherits all MySQL behaviors — the syntax and grammar classes are empty subclasses with no overrides.

## Architecture

```
Drivers/Percona/
  PerconaDriver.php  extends MysqlDriver
  PerconaSyntax.php  extends MysqlSyntax   (no overrides)
  PerconaGrammar.php extends MysqlGrammar  (no overrides)
```

All query building, DDL generation, type mappings, introspection, and transaction handling are identical to the MySQL driver.

## Differences from MySQL

### Additional Storage Engines

`setEngine()` is overridden to accept Percona-specific engines alongside the standard MySQL set:

| Engine | Notes |
|--------|-------|
| `xtradb` | Enhanced InnoDB; maps to `InnoDB` in Percona 8.0+ (enhancements merged) |
| `tokudb` | Fractal tree indexing; deprecated in Percona 8.0 |
| `rocksdb` | LSM tree storage engine |
| `myrocks` | Alias for RocksDB |

`hasEngine()` accounts for XtraDB appearing as InnoDB in `SHOW ENGINES` output.

### CAST Keyword

`getIntegerCastKeyword()` returns `INTEGER` instead of the MySQL default `SIGNED`. This affects `CAST(value AS INTEGER)` expressions.

### Server Info

`getServerInfo()` parses `SHOW VARIABLES LIKE '%version%'` and returns Percona-specific version fields. `getMajorVersion()` extracts the major.minor version as a float (e.g., `8.0`).

### XtraDB Cluster Status

`getClusterStatus()` queries `SHOW STATUS LIKE 'wsrep_%'` to return Percona XtraDB Cluster (PXC) status: cluster size, node status, connected/ready state. Returns null if not in cluster mode.

### Query Response Time Statistics

`getQueryResponseTimeStats()` queries `INFORMATION_SCHEMA.QUERY_RESPONSE_TIME` for Percona's query response time plugin data. Returns null if the plugin is not installed.

## File Summary

| File | Purpose |
|------|---------|
| `PerconaDriver.php` | Storage engine validation (XtraDB, TokuDB, RocksDB), version info, cluster status, response time stats |
| `PerconaSyntax.php` | Empty subclass — inherits all MySQL DML behavior |
| `PerconaGrammar.php` | Empty subclass — inherits all MySQL DDL behavior |
