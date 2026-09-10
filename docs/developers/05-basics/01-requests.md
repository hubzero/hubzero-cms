<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/basics/requests
-->
# Requests

Everything a browser sends arrives through one object. The `Request` facade
resolves [`Hubzero\Http\Request`](../../../core/libraries/Hubzero/Http/Request.php),
which extends Symfony's `Request` and adds the typed accessors the CMS uses.
Read input through it; never touch `$_GET`, `$_POST` or `$_REQUEST`
directly.

## Reading input

Ask for the type you want. Each accessor takes the key, a default, and
optionally which part of the request to look in:

```php
use Request;

$id     = Request::getInt('id', 0);
$alias  = Request::getCmd('alias', '');
$title  = Request::getString('title', '', 'post');
$fields = Request::getArray('fields', array(), 'post');
```

| Method | Returns |
|---|---|
| `getInt($key, $default = 0, $hash = 'input')` | A leading integer, or the default |
| `getUInt($key, $default = 0, $hash = 'input')` | The same, made positive |
| `getFloat($key, $default = 0.0, $hash = 'input')` | The first number in the value |
| `getBool($key, $default = null, $hash = 'input')` | The value cast to boolean |
| `getWord($key, $default = null, $hash = 'input')` | Letters and underscores only |
| `getCmd($key, $default = null, $hash = 'input')` | Letters, digits, `_`, `.`, `-`; leading dots stripped |
| `getString($key, $default = null, $hash = 'input')` | The value as a string |
| `getArray($key, $default = array(), $hash = 'input')` | The value as an array |
| `getSimpleArray($key, $default = array(), $hash = 'input')` | The same, with nested arrays dropped |
| `getVar($key, $default = null, $hash = 'input', ...)` | The raw value, unfiltered |

`getInt()` matches only a *leading* integer, so an email address typed into
an id field yields the default rather than a number scavenged from the
middle of it. `getCmd()` is what task, view and layout names are read with,
because its filter is exactly the character set those are allowed to use.

### Where to look

The `$hash` argument names the part of the request:

| `$hash` | Source |
|---|---|
| `input` (default) | The method's own bag, falling back to the query string |
| `post` | The request body |
| `get` | The query string |
| `cookie` | Cookies |
| `server` | Server variables |
| `files` | `$_FILES`, returned as the raw array |

Naming it narrows what an attacker can supply. A task that must be posted
should read its fields with `'post'`, so the same names in a query string
are ignored.

> **Warning:** `getVar()` applies no filtering. Its fourth argument was a
> filter name in older versions and is now dead: for the default `input`
> hash it is discarded outright, and for the others only the value `array`
> still means anything. Lists of filter types like `INT`, `BASE64` and
> `PATH` no longer exist. Use the typed accessors above; reach for
> `getVar()` only where you genuinely want the value untouched, and escape
> it yourself.

### Files, cookies, headers

```php
$file  = Request::file('upload');            // one uploaded file
$value = Request::cookie('name', $default);
$agent = Request::header('User-Agent');
$uri   = Request::server('REQUEST_URI');
```

`has($key)` reports whether a key is present and non-empty, and accepts
several keys at once, returning true only if all are present.

## The form token

Every task that changes something must check the token first:

```php
Request::checkToken();
```

It looks in POST by default, delegates to the
[session](../04-services/04-session.md), and aborts the request with a 403 when
the token is missing or wrong. Pass `'get'` or a comma-separated list to
look elsewhere. `Request::checkHoneypot()` is its companion for public
forms: it validates the hidden field emitted by `Hubzero\Spam\Honeypot` and
returns `false` — logging to the spam log — when the form was filled in too
fast or by a robot.

## Remembering state

List screens keep their filters across requests. `getState()` reads a value
from the request if it is there and remembers it, and returns the remembered
value when it is not:

```php
$search = Request::getState(
    $this->_option . '.entries.search',
    'search',
    ''
);
```

The first argument is the key it is stored under, the second the request
variable, then a default and an optional type (`int`, `word`, `cmd`,
`bool`, `float`, `string`, `array`). The store is the user's session
registry, so a member who leaves a list and comes back finds it as they
left it.

## What the request knows about itself

| Method | Returns |
|---|---|
| `method()` | `GET`, `POST`, … |
| `root($pathonly = false)` | The application root, with `administrator`/`api` trimmed off |
| `base($pathonly = false)` | The base URL; pass `true` for the path alone |
| `current($query = false)` | The current URL, without the query string unless asked |
| `path()` | The path portion, always starting with `/` |
| `segment($index, $default = null)` / `segments()` | Path segments, 1-based |
| `scheme()` / `host()` / `ip()` | Where the request came from |
| `secure()` | Whether it arrived over HTTPS |
| `ajax()` | Whether it is an `XMLHttpRequest` |

```php
if (Request::ajax())
{
    Response::header('Content-Type', 'application/json');

    echo json_encode($data);

    App::close();
}
```

`setVar($name, $value, $hash = 'method', $overwrite = true)` writes into the
request. Use it sparingly — a controller handing a value to a view it is
about to dispatch — and never as a way of passing data around inside your
own code.
