# Shared Database Connection: Laravel + HubZero

## Problem

Every request creates **two independent MySQL connections** to the same database:

1. **Laravel** — `Illuminate\Database\MySqlConnection` creates a PDO via its
   connection factory, resolved through `config/database.php` + `.env`.
2. **HubZero** — `Hubzero\Database\Driver::getInstance()` creates a separate
   `PdoConnection` via `Bootstrap\Site\Providers\DatabaseServiceProvider`,
   resolved through `app/config/database.php`.

Both connect to the same host, database, user, and password. Two connections
means double the connection overhead, double the server-side session state, and
no shared transaction scope between the two systems.

## Goal

Share a single underlying `\PDO` instance between Laravel and HubZero so that:

- Only one MySQL connection is opened per request
- Transactions started by either system are visible to the other
- No changes are required to HubZero model/query code (`Relational`, `Table`,
  raw `$db->setQuery()` calls)
- No changes are required to Laravel model/query code (`DB::`, Eloquent)

## Architecture Overview

```
Request
  │
  ▼
Laravel Application boots
  │
  ├── Illuminate\Database\DatabaseServiceProvider
  │     └── Creates MySqlConnection (PDO created lazily on first query)
  │
  └── Bootstrap\Site\Providers\DatabaseServiceProvider
        └── Creates Hubzero\Database\Driver (currently creates its own PDO)
        └── CHANGE: Inject Laravel's PDO into HubZero's PdoConnection
```

**Laravel owns the PDO. HubZero borrows it.**

Laravel is the long-term framework — it should be the canonical connection
owner. HubZero's `PdoConnection` already supports lazy connection and has a
public `getPdo()` method. We add the ability to inject an external PDO.

## Implementation Plan

### Step 1: Add `PdoConnection::fromPdo()` factory method

**File:** `core/libraries/Hubzero/Database/Connection/PdoConnection.php`

Add a static factory that wraps an existing PDO instance:

```php
/**
 * Creates a PdoConnection from an existing PDO instance.
 *
 * The connection is marked as externally managed, meaning disconnect()
 * becomes a no-op — the caller that created the PDO is responsible for
 * its lifecycle.
 *
 * @param  \PDO  $pdo  An already-connected PDO instance
 * @return self
 */
public static function fromPdo(\PDO $pdo): self
{
    $instance = new self('', '', '', []);
    $instance->pdo = $pdo;
    $instance->externallyManaged = true;
    return $instance;
}
```

This requires a new property:

```php
/**
 * Whether the PDO instance is externally managed (injected, not created here).
 *
 * When true, disconnect() is a no-op — the owner of the PDO controls its
 * lifecycle. This prevents HubZero code from accidentally closing a
 * connection that Laravel is still using.
 *
 * @var bool
 */
protected $externallyManaged = false;
```

### Step 2: Guard `disconnect()` for externally managed connections

**File:** `core/libraries/Hubzero/Database/Connection/PdoConnection.php`

```php
public function disconnect(): void
{
    // Don't close a PDO we didn't create
    if ($this->externallyManaged) {
        return;
    }

    $this->pdo = null;
    gc_collect_cycles();
}
```

Also guard `connect()` to prevent re-creating a different PDO over the
injected one:

```php
public function connect(): void
{
    if ($this->pdo !== null) {
        return;
    }

    if ($this->externallyManaged) {
        throw new ConnectionFailedException(
            'Cannot reconnect an externally managed PDO connection. '
            . 'The owning framework must provide a new PDO instance.',
            500
        );
    }

    // ... existing PDO creation code ...
}
```

### Step 3: Add `isExternallyManaged()` accessor

```php
/**
 * Whether this connection wraps an externally provided PDO.
 *
 * @return bool
 */
public function isExternallyManaged(): bool
{
    return $this->externallyManaged;
}
```

### Step 4: Modify `DatabaseServiceProvider` to inject Laravel's PDO

**File:** `core/bootstrap/Site/Providers/DatabaseServiceProvider.php`

```php
use Hubzero\Database\Driver;
use Hubzero\Database\Connection\PdoConnection;
use Hubzero\Database\Relational;
use Hubzero\Database\Table;
use Hubzero\Base\ServiceProvider;
use Illuminate\Support\Facades\DB;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['db'] = function ($app) {
            $options = [
                'driver'   => 'pdo',
                'host'     => $app['config']->get('host'),
                'user'     => $app['config']->get('user'),
                'password' => $app['config']->get('password'),
                'database' => $app['config']->get('db'),
                'prefix'   => $app['config']->get('dbprefix'),
            ];

            // Create the HubZero driver (this builds a lazy PdoConnection
            // internally, but we'll replace it below)
            $driver = Driver::getInstance($options);

            // Grab Laravel's PDO — this triggers Laravel's lazy connection
            // if it hasn't connected yet
            $laravelPdo = DB::connection()->getPdo();

            // Replace HubZero's auto-created PdoConnection with one that
            // wraps Laravel's PDO instance
            $driver->setConnection(PdoConnection::fromPdo($laravelPdo));

            if ($app['config']->get('debug')) {
                $driver->enableDebugging();
            }

            return $driver;
        };
    }

    // boot() stays the same
}
```

### Step 5: Remove the duplicate HubZero database config (optional, later)

Once the shared connection is stable, the legacy `app/config/database.php`
credentials become redundant — HubZero no longer opens its own connection.
The `Driver::getInstance()` options are still needed for the table prefix
and driver type, but the host/user/password could be sourced from Laravel's
config instead. This cleanup can happen later.

## Considerations

### Transaction sharing

This is the primary *benefit* but also requires awareness:

- If Laravel starts a transaction (`DB::beginTransaction()`), HubZero queries
  execute inside it. If HubZero code calls `$db->getConnection()->rollBack()`,
  it rolls back Laravel's transaction too.
- This is correct behavior for most cases — it means saves that touch both
  systems are atomic.
- **Risk:** HubZero code that calls `beginTransaction()` /
  `commit()` / `rollBack()` directly on the connection will affect
  Laravel's transaction nesting counter. Laravel tracks nesting depth via
  `$this->transactions` on the Connection object. Direct PDO calls bypass
  this counter. Mitigation: audit HubZero code for direct transaction calls
  (there are very few).

### PDO attribute conflicts

Both systems set `PDO::ATTR_ERRMODE` to `ERRMODE_EXCEPTION` — no conflict.

HubZero's `PdoConnection` calls `setExceptionMode()` after connect. Since
we're injecting an already-connected PDO that Laravel already set to exception
mode, this is harmless. The `connect()` method won't be called on an
externally managed connection, so `setExceptionMode()` won't fire.

If HubZero code calls `setSilentMode()` anywhere, it would affect Laravel
too. Audit for this (unlikely to exist in practice).

### Connection lifecycle

| Event | Before (2 connections) | After (shared) |
|---|---|---|
| Request starts | 0 connections | 0 connections |
| First Laravel query | Laravel connects (1) | Laravel connects (1) |
| First HubZero query | HubZero connects (2) | Uses Laravel's PDO (1) |
| Request ends | Both close | Laravel closes (1) |
| HubZero `disconnect()` | Closes HubZero PDO | No-op (externally managed) |

### Driver pool (`Driver::$instances`)

`Driver::getInstance()` pools by `md5(serialize($options))`. The injected
PDO replaces the connection *after* the driver is pooled. This is fine —
the pool caches the `Driver` object, not the PDO. On subsequent calls with
the same options, `getInstance()` returns the cached driver which already
has the shared PDO.

However, if `getInstance()` is called before `DatabaseServiceProvider`
resolves (e.g., during early boot), a HubZero driver with its own PDO could
be pooled. The service provider's `setConnection()` call replaces it, so
this is safe — but worth being aware of.

### Long-running processes / workers

For Artisan commands or queue workers, Laravel's `DatabaseManager` handles
reconnection on stale connections. If the shared PDO goes stale, HubZero
queries will fail. Mitigation: in worker contexts, either:

- Use `DB::reconnect()` then re-inject the new PDO into HubZero's driver
- Or let HubZero fall back to its own connection for worker contexts

### Testing

Verification steps:

1. **Connection count:** Run `SHOW STATUS LIKE 'Threads_connected'` before
   and after a request. Should be 1 fewer with shared connection.
2. **Transaction atomicity:** Start a Laravel transaction, insert via HubZero,
   rollback via Laravel — HubZero insert should be rolled back.
3. **HubZero queries still work:** Run the full component test suite (blog,
   wiki, answers, etc.) — all existing queries should work identically.
4. **Disconnect safety:** Call `app('db')->getConnection()->disconnect()` —
   subsequent queries from both systems should still work (Laravel reconnects,
   HubZero's disconnect is a no-op).

## Files to modify

| File | Change |
|---|---|
| `core/libraries/Hubzero/Database/Connection/PdoConnection.php` | Add `$externallyManaged`, `fromPdo()`, guard `disconnect()`/`connect()` |
| `core/bootstrap/Site/Providers/DatabaseServiceProvider.php` | Inject Laravel's PDO via `PdoConnection::fromPdo()` |

## Files to audit

| File | Why |
|---|---|
| Any HubZero code calling `beginTransaction()` / `commit()` / `rollBack()` | Direct PDO transaction calls bypass Laravel's nesting counter |
| Any HubZero code calling `setSilentMode()` | Would change error mode for Laravel too |
| Any HubZero code calling `disconnect()` | Now a no-op, verify no code depends on reconnection side effects |
