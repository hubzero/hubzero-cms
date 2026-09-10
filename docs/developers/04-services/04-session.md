<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/services/session
-->
# Session

A session holds a small amount of data for one visitor across several
requests: who they are logged in as, the form token that guards their
writes, the filters they left a list screen on. The `Session` facade
resolves [`Hubzero\Session\Manager`](../../../core/libraries/Hubzero/Session/Manager.php),
which wraps PHP's session with a namespace scheme and a token.

The manager is registered by `SessionServiceProvider` in the site and
administrator applications only. The storage handler comes from
`session_handler` in `app/config/session.php` — `database` (the default on
a hub), `file`, `memcache`, `memcached`, `redis`, `apc`, `xcache` or
`none` — and the lifetime from `lifetime`, which is in minutes and is
multiplied out to seconds when the manager is built.

## Storing and reading

```php
use Session;

Session::set('newsubmission.blog', true);

if (Session::get('newsubmission.blog'))
{
    // first entry this member has posted
}
```

| Method | Signature |
|---|---|
| `get` | `get($name, $default = null, $namespace = 'default')` |
| `set` | `set($name, $value = null, $namespace = 'default')` — returns the previous value |
| `has` | `has($name, $namespace = 'default')` |
| `clear` | `clear($name, $namespace = 'default')` — removes it and returns what it held |

The namespace is the third argument, not the second, and it keeps two
extensions from colliding over a common name. It is stored prefixed with
`__`, so `default` becomes `$_SESSION['__default']`.

```php
Session::set('cart', $cart, 'com_cart');

$cart = Session::get('cart', array(), 'com_cart');
```

> **Note:** `set()` with no value **deletes** the entry. `Session::set('x')`
> and `Session::set('x', null)` both unset `x`; they do not store a null.
> Components rely on this — `Session::set('newsubmission.blog')` is how the
> blog clears its own flag after using it — so do not write it by accident.

`get()` and `has()` return early when the session is not active, so a value
read outside a live session comes back as the default rather than raising.

## The form token

Every request that changes something must carry a token, and the check is
one line at the top of the task:

```php
// Throws a 403 if the token is missing or wrong
Request::checkToken();
```

`Request::checkToken($method = 'post')` delegates to
`Session::checkToken()`, which looks for a request variable whose *name* is
the token and whose value is anything truthy. `$method` may be `'post'`,
`'get'`, or a comma-separated list. Pass `true` as the second argument to
`Session::checkToken()` to get `false` back instead of an aborted request.

For a form, emit the hidden field with `Html::input('token')`. For a link
that performs an action, append the token by hand:

```php
$url = Route::url('index.php?option=com_blog&task=publish&id=' . $row->get('id')
    . '&' . Session::getFormToken() . '=1');
```

| Method | What it does |
|---|---|
| `getToken($forceNew = false)` | The raw session token, created on first use |
| `getFormToken($forceNew = false)` | `App::hash(user id . token)` — the name to use in a form or URL |
| `hasToken($tCheck, $forceExpire = true)` | Compare a token; expires the session on a mismatch |
| `checkToken($method = 'post', $capture = false)` | Static. Aborts with 403 unless a valid token is present |

`getFormToken()` mixes in the user id, so the token a guest sees and the
token that member sees after logging in are different values.

## The session's own state

| Method | Returns |
|---|---|
| `getId()` | The session id |
| `getName()` | The cookie name — a hash of the hub secret and the client |
| `getState()` | `active`, `expired`, `destroyed`, `error` |
| `getExpire()` | The lifetime in seconds |
| `isNew()` | Whether this request created the session |
| `restart()` / `fork()` / `reregister()` | Start over, or move the data to a fresh id |
| `destroy()` | Discard everything |
| `close()` | Write the session out and release the lock |

The provider puts two things into every new session: a `user` holding a
`Hubzero\User\User`, and a `registry` holding a
`Hubzero\Config\Registry`. The registry is what
[`User::getState()` and `User::setState()`](../05-basics/05-user.md) read and
write, and what `Request::getState()` uses to remember a list screen's
filters between requests. Use those rather than writing filter state into
the session yourself.

> **Warning:** With the `database` handler every value you store is
> serialised into a row that is written on each request. Keep what you put
> there small — an id, a flag, a short array. Rendered output belongs in the
> [cache](cache.md), and anything that must survive belongs in a table.
