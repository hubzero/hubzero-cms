<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/services/cache
-->
# Cache

The cache holds expensive results — rendered wiki text, a tag cloud, a
statistics query — so the next request does not have to compute them again.
It is a plain key/value store with a time to live, and nothing in the CMS
depends on a value being there.

Reach for it when a value is **expensive to compute, the same for everybody,
and tolerable when stale**. All three. An instrument-booking component might
cache the list of instruments and their opening hours, which changes twice a
year; it must not cache the day's free slots, which change as members book
them, and it must not cache anything that depends on who is looking.

> **Warning:** The commonest cache bug on a hub is a key that leaves out
> something the value depends on — usually the member. Cache a rendered
> panel under `bookings.sidebar` and the first member to load it decides
> what every other member sees, including whoever is logged in as an
> administrator. Nothing errors; the page simply shows somebody else's
> data. If a value varies by user, access level or language, put that in the
> key — or do not cache it.

## What the facade resolves to

Two keys are registered, and they are not the same object:

<!--include: core/bootstrap/Site/Providers/CacheServiceProvider.php:23-41-->

`cache` is the [`Hubzero\Cache\Manager`](../../../core/libraries/Hubzero/Cache/Manager.php),
which resolves and caches storage instances. `cache.store` is one storage
instance, chosen from the hub's configuration. **The `Cache` facade resolves
`cache.store`** — so a call on the facade lands on a
[`StorageInterface`](../../../core/libraries/Hubzero/Cache/Storage/StorageInterface.php)
implementation, not on the manager.

Note the two switches in the closure. With `caching` off in
`app/config/cache.php` the handler is forced to `none`, and in the
administrator it is forced to `none` unconditionally. Both `None` stores
accept every write and return `null` from every read, so code that caches
still runs; it just never gets a hit.

## Storing and reading

```php
use Cache;

// 15 is minutes, not seconds
Cache::put('wiki.r' . $revision->get('id'), $rendered, 15);

$rendered = Cache::get('wiki.r' . $revision->get('id'));
```

The third argument catches people out. `Cache::put($key, $value, 3600)`
means two and a half **days**, not an hour.

That is the shape of nearly every use in the tree — write with a TTL, read
back, recompute on a miss:

<!--include: core/components/com_wiki/site/controllers/pages.php:239-249-->

| Method | What it does |
|---|---|
| `get($key)` | The value, or `null` on a miss or an expired entry |
| `has($key)` | Whether a live entry exists |
| `put($key, $value, $minutes)` | Store, replacing anything already there |
| `add($key, $value, $minutes)` | Store only if `has()` is false; returns `false` otherwise |
| `forever($key, $value)` | `put()` with an expiry far in the future |
| `forget($key)` | Remove one entry |
| `clean($group = null)` | Remove one group, or everything |
| `gc($group = null)` | Remove entries that have expired |
| `all()` | A per-group summary of what is stored |

> **Note:** `get()` takes a key and nothing else. Writing
> `Cache::get('key', 'default')` is not an error — PHP discards the extra
> argument — but the default is silently ignored and you get `null` on a
> miss. Test the return value instead. `Hubzero\Cache\Manager::get()` does
> accept a default (and a closure), but the facade does not go through the
> manager; reach it with `App::get('cache')->get('key', $default)`.

Values are serialized, so anything `serialize()` can round-trip is a legal
value. Store the rendered string, not the model that produced it: a model
carries a database connection and a query object, and what comes back out of
the cache is a half-woken object that fails the first time something calls a
method on it.

## Groups

The key is dotted, and the first segment is the **group**. The file store
turns the group into a directory under `app/cache/<client>/` and hashes the
rest of the key into the filename. That is what makes selective clearing
work:

```php
// everything the wiki cached
Cache::clean('wiki');

// the whole cache
Cache::clean();
```

Components use their own name or a short label for the group — `wiki`,
`tags`, `groups`, `members`, `com_templates`, `_system`. Pick one and prefix
every key with it, or your entries end up in the root of the cache
directory where only a full `clean()` reaches them.

`all()` returns one [`Auditor`](../../../core/libraries/Hubzero/Cache/Auditor.php)
per group, carrying `group`, `count` and `size` (in kilobytes). The
administrator's cache screen is built from it.

## Drivers

The driver comes from `cache_handler` in `app/config/cache.php`. The classes
live in [`core/libraries/Hubzero/Cache/Storage/`](../../../core/libraries/Hubzero/Cache/Storage/):
`File` (the default), `Memcache`, `Memcached`, `Apc`, `WinCache`, `XCache`,
`Memory` (per-request only) and `None`. Each has a static `isAvailable()`,
and `Hubzero\Cache\Manager::getStores()` returns the lowercased names of the
ones this server can actually use.

To add one, implement `StorageInterface` and register a resolver on the
**manager**:

```php
App::get('cache')->extend('example', function($config)
{
    return new ExampleStore($config);
});
```

The name matches a `cache_handler` value; the closure receives the merged
configuration array, which always carries `hash` and `cachebase`.

> **Warning:** The cache is not storage. A file store can be emptied by any
> administrator from the Cache screen, a memcached instance can evict an
> entry at any moment, and the administrator application never caches at
> all. Never put anything in it that you cannot recompute.
