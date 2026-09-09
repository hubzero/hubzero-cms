<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/basics/redirect
-->
# Redirect

A task that changes something should not render a page. It should redirect,
so that reloading the browser does not repeat the change. `App::redirect()`
is how, and it is the last line of nearly every `save`, `delete`, `publish`
and `state` task in the tree.

```php
use App;
use Route;
use Lang;

App::redirect(
    Route::url('index.php?option=com_support&controller=tickets'),
    Lang::txt('COM_SUPPORT_TICKET_SAVED'),
    'success'
);
```

## The arguments

`App::redirect($url, $message = null, $type = 'success')`

| Argument | Notes |
|---|---|
| `$url` | Where to go. Build it with `Route::url()` rather than by hand |
| `$message` | Optional. Shown to the member on the page they land on |
| `$type` | `success`, `error`, `warning` or `info` |

The message is queued through the `notification` service, which stores it in
the session; the template on the next page renders and clears it. That is
why the message survives the redirect. On a client with no notification
service — the API, the command line — the message is dropped and only the
redirect happens.

The types match the [`Notify`](../foundation/facades.md) facade's methods,
so these two are equivalent:

```php
App::redirect($url, $msg, 'error');
```

```php
Notify::error($msg);

App::redirect($url);
```

Use the second form when you have several messages to queue, or when the
redirect target is decided later.

## It does not return

`App::redirect()` builds a `Hubzero\Http\RedirectResponse`, sends it, and
calls `App::close()`, which is `exit()`. Nothing after the call runs:

```php
App::redirect(Route::url('index.php?option=com_support'));

// never reached
$this->doSomethingElse();
```

That makes it safe to use as an early exit, and it makes `return` after it
redundant — though `return` is written in a lot of places anyway, and does
no harm.

> **Warning:** Because it exits, nothing after it gets to clean up. Close
> files, commit or roll back a transaction, and finish writing rows
> **before** you redirect, not after.

## What happens to the URL

`RedirectResponse::send()` normalises the target before sending it:

- A URL starting `index.php` or `index2.php` gets `Request::base()`
  prepended.
- Everything after the first line break is discarded, which is what stops a
  header injection through a redirect parameter.
- A URL with no scheme is made absolute: one starting `/` against the
  current scheme, user info and host; anything else against the current
  path as well.

So a relative route result reaches the browser as an absolute URL, and you
do not have to build one.

> **Warning:** Never redirect straight to a URL taken from the request. A
> `return` parameter is an open redirect unless you check it first —
> confirm it is internal, or decode and match it against routes you
> recognise, before passing it in.

## Redirecting from a controller

Controllers extending `Hubzero\Component\SiteController` or
`AdminController` also carry `setRedirect($url, $msg = null, $type = null)`,
which stores the target, and `redirect($url = null, $msg = null, $type =
null)`, which sends whatever was stored. Both are marked `@deprecated` and
both end up in `App::redirect()`; you will meet them in older controllers,
but write `App::redirect()` in new code.

## Sending one yourself

The rare case — a middleware, or a redirect that must carry extra headers —
constructs the response directly:

```php
$redirect = new Hubzero\Http\RedirectResponse($url);
$redirect->setRequest(App::get('request'));
$redirect->header('X-Reason', 'moved');
$redirect->send();

App::close();
```

`setRequest()` is what enables the URL normalisation above; without it the
target is sent exactly as given.
