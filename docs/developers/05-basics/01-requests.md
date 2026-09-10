<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/basics/requests
-->
# Requests

Everything a browser sends arrives through one object. The `Request` facade
resolves [`Hubzero\Http\Request`](../../../core/libraries/Hubzero/Http/Request.php),
which extends Symfony's `Request` and adds the typed accessors the CMS uses.

You reach for it for one reason: it hands you a value of the type you asked
for, or the default, with no branch of your own for "absent", "an array
arrived where a scalar was expected", or "somebody typed a word into a
number field".

```php
use Request;

$id = Request::getInt('id', 0);
```

That line cannot return anything but an integer.

## Never read the superglobals

`$_GET`, `$_POST` and `$_REQUEST` are not the request. Three concrete
things go wrong.

**The router writes into the request object, not into `$_GET`.** On a hub
with search-engine-friendly URLs, `/bookings/instrument/12` is parsed by the
router, which puts the result back with `setVar($key, $val, 'get')` —
Symfony's query bag. So:

<!--include: core/bootstrap/Site/Providers/RouterServiceProvider.php:68-71-->

```php
$view = $_GET['view'];                  // undefined index — the URL had no query string
$view = Request::getCmd('view', '');    // 'instrument'
```

Every menu-routed and SEF-routed page in the tree behaves this way. Code
reading `$_GET` works on the developer's machine with `index.php?option=…`
in the URL and breaks on the hub.

**`$_REQUEST` merges sources.** A value you meant to accept only from a
posted form can then be supplied in the query string, which is a link
someone can send to a member. The typed accessors take a `$hash` argument
precisely so you can refuse that.

**There is no superglobal outside the web.** A [muse](../12-muse.md)
command, a cron run, and a test all build a request object without a browser
behind it. Code that reads `$_POST` cannot run in any of them.

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
| `getFloat($key, $default = 0.0, $hash = 'input')` | A leading decimal number, or the default |
| `getBool($key, $default = null, $hash = 'input')` | The value cast to boolean |
| `getWord($key, $default = null, $hash = 'input')` | Letters and underscores only |
| `getCmd($key, $default = null, $hash = 'input')` | Letters, digits, `_`, `.`, `-`; leading dots stripped |
| `getString($key, $default = null, $hash = 'input')` | The value as a string |
| `getArray($key, $default = array(), $hash = 'input')` | The value as an array |
| `getSimpleArray($key, $default = array(), $hash = 'input')` | The same, with nested arrays dropped |
| `getVar($key, $default = null, $hash = 'input', ...)` | The raw value, unfiltered |

### Which of them to trust with what

`getInt()`, `getUInt()`, `getFloat()` and `getString()` are the four to
prefer. Each returns its declared type, and each returns the default you
gave when the key is absent.

`getInt()` and `getFloat()` match only a **leading** number. That is a
correction made in this repository, not inherited behaviour: the match used
to be unanchored, so `getInt()` scavenged digits out of the middle of
arbitrary text and an email address typed into a member-id field —
`jesus1993coral@gmail.com` — resolved to user `1993`. `getFloat()` was
anchored to match. Both now return the default rather than a number from the
middle of a string.

`getCmd()` is what task, view and layout names are read with, because its
filter is exactly the character set those may use. Two things about it and
`getWord()`:

- **A falsy default does not survive.** `Request::getCmd('layout', null)`
  returns `''`, not `null`, because the filter runs over `$result ?: ''`.
  Test against `''`.
- The filter strips rather than rejects. `getCmd('view', '')` on
  `view=../../etc` returns `etc` — the slashes are removed and the leading
  dots trimmed. Nothing was refused; a name came back that the caller never
  sent. It is a filter, not a validator, so compare the result against the
  names you accept.

`getBool()` is a plain `(bool)` cast of whatever arrived. A checkbox that
posts nothing when unticked works; a field carrying the string `"false"` or
`"off"` comes back **true**, because a non-empty string is truthy. For
tri-state values read a `getCmd()` or a `getInt()` and decide yourself.

`getArray()` casts, so a scalar submitted where your form expects an array
arrives as a one-element array rather than an error. Validate the shape
before you `set()` it on a model.

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
> `PATH` no longer exist, so a call that names one is silently unfiltered.
> Use the typed accessors above; reach for `getVar()` only where you
> genuinely want the value untouched, and escape it yourself.

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
look elsewhere.

Omitting it is the most common security fault in this tree. The task keeps
working, so nothing tells you: it just also works when a member is walked
onto a page that posts to it from somewhere else.

`Request::checkHoneypot()` is its companion for public forms: it validates
the hidden field emitted by `Hubzero\Spam\Honeypot` and returns `false` —
logging to the spam log — when the form was filled in too fast or by a
robot. Unlike `checkToken()` it returns rather than aborting, so test the
result.

## Remembering state

List screens keep their filters across requests, so that a member who opens
a booking, then goes back, finds the list as they left it. `getState()`
reads a value from the request if it is there and remembers it, and returns
the remembered value when it is not:

```php
$search = Request::getState(
    $this->_option . '.instruments.search',
    'search',
    ''
);
```

The first argument is the key it is stored under, the second the request
variable, then a default and an optional type (`int`, `word`, `cmd`,
`bool`, `float`, `string`, `array`). The store is the user's session
registry, so the value outlives the request.

Namespace the key with the component and the screen, as above. The registry
is shared by every extension on the hub, and two screens that both call
their key `search` will read each other's filter.

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
request. The router uses it, and so may a controller handing a value to a
view it is about to dispatch. Never use it as a way of passing data around
inside your own code: a value written there is visible to every later stage
of the request, including plugins you did not write.
