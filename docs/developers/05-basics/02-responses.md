<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/basics/responses
-->
# Responses

Everything a hub sends back is attached to one object. The `Response`
facade resolves [`Hubzero\Http\Response`](../../../core/libraries/Hubzero/Http/Response.php),
which extends Symfony's `Response` and adds output compression and a
chainable `header()`. The application creates it during boot, the template
fills its content, and the application sends it — so in the ordinary case
you never touch it.

> **Note:** For everything inherited — `setStatusCode()`, `setContent()`,
> `getContent()`, `headers`, `setCache()` — see the
> [Symfony HttpFoundation documentation](https://symfony.com/doc/current/components/http_foundation.html).
> This page covers what Hubzero adds and when reaching for it is right.

## Getting at it

```php
use Response;

Response::header('Content-Type', 'application/json');
```

or, equivalently, from the container:

```php
$response = App::get('response');
$response->header('Content-Type', 'application/json');
```

> **Warning:** `Response` is a facade name, and it is also a common model
> name — `Components\Answers\Models\Response`, for one. In a namespaced
> file, whichever you `use` is what the name means, and the two are not
> interchangeable. If a file needs both, alias one of them.

## Headers

`header($key, $values, $replace = true)` sets a header and returns the
response, so several can be chained:

```php
$response = App::get('response');

$response->header('Content-Type', 'application/json')
         ->header('X-Total-Count', $total)
         ->header('Cache-Control', 'no-store');
```

The most common use by far is switching the content type for an AJAX task,
then echoing the payload and ending the request:

```php
public function statusTask()
{
    Response::header('Content-Type', 'application/json');

    echo json_encode(array('state' => $row->get('state')));

    App::close();
}
```

`App::close()` calls `exit()`. Nothing after it runs, and nothing else gets
appended to the body.

## Content

`setContent($output)` replaces the body; it takes a string. `getContent()`
reads it back. The site template's output is set here at the end of the
render stage, so a system plugin listening on `onAfterRender` can read the
finished page, alter it, and set it back:

```php
public function onAfterRender()
{
    $body = App::get('response')->getContent();

    App::get('response')->setContent(str_replace($from, $to, $body));
}
```

## Compression and sending

`compress($value)` marks the response for gzip. The application turns it on
from the `gzip` global configuration option; when set, `send()` checks the
browser's `Accept-Encoding`, confirms `zlib` is loaded and that headers have
not gone out, and compresses the body on the way past. It is a no-op
otherwise, so nothing breaks on a server without zlib.

`send($flush = false)` writes the headers and then the body. With `$flush`
true it also closes the connection — `fastcgi_finish_request()` where that
exists — so slow work can carry on after the browser has the page. The
application calls this once, at the end of the request; a component calling
it is almost always a mistake.

Serving a file off disk is a separate path with its own headers and byte
ranges. Use [`Hubzero\Content\Server`](../04-services/06-server.md), not the
response object.

## Redirects

A redirect is a response too, of a different class.
[`Hubzero\Http\RedirectResponse`](../../../core/libraries/Hubzero/Http/RedirectResponse.php)
extends Symfony's, and its `send()` resolves the target first: an
`index.php`-relative URL is prefixed with `Request::base()`, line breaks
are stripped, and a URL with no scheme is made absolute against the current
scheme, host and path. That is why a component can redirect to a route
result without worrying whether it came back absolute.

`App::redirect()` builds one, attaches the request, optionally queues a
message, sends it, and exits:

```php
App::redirect(
    Route::url('index.php?option=com_support'),
    Lang::txt('COM_SUPPORT_TICKET_SAVED'),
    'success'
);
```

See [redirect](redirect.md) for the details, including the message types
and what happens to the code after the call.

## Status codes and errors

Do not set an error status on the response by hand. `App::abort($code,
$message)` throws the exception the error handler is looking for, and the
error template renders the right page:

| Code | Exception thrown |
|---|---|
| 403 | `Hubzero\Error\Exception\NotAuthorizedException` |
| 404 | `Hubzero\Error\Exception\NotFoundException` |
| 405 | `Hubzero\Error\Exception\MethodNotAllowedException` |
| anything else | `Hubzero\Error\Exception\RuntimeException` with that code |

```php
if (!$row->get('id'))
{
    App::abort(404, Lang::txt('COM_BLOG_ERROR_ENTRY_NOT_FOUND'));
}
```

Throwing one of those exceptions yourself has the same effect;
`App::abort()` is the shorter way to say it.
