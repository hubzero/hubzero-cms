<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/services/server
-->
# Server

[`Hubzero\Content\Server`](../../../core/libraries/Hubzero/Content/Server.php)
sends a file off disk to the browser. It handles the headers a download
needs — content type, length, disposition, byte ranges — and streams the
body, so a controller that serves an attachment does not have to.

You need it whenever a file must not sit under the document root: a
calibration report attached to an instrument booking, a dataset only the
lab's members may read. The file lives somewhere the web server will not
serve, your controller decides whether this member may have it, and `Server`
does the rest. Byte ranges are why you should not roll your own — without
them a member cannot resume an interrupted download, and audio and video
will not seek.

There is no facade for it. Construct one where you need it:

```php
use Hubzero\Content\Server;
```

## Serving a file

<!--include: core/components/com_blog/site/controllers/media.php:77-90-->

That is the whole pattern: set the file, say how it should be presented,
call `serve()`, and stop. `serve()` returns `false` if it could not send
the file, and by the time it returns `true` the response body is already
written, so nothing may be echoed afterwards.

> **Note:** `Server` writes headers and content itself rather than going
> through the [response object](../05-basics/02-responses.md). Any output already
> buffered will be sent ahead of the file and corrupt it, and any output
> after it appends to the download. End the request immediately.

## The setters

Each setter is also its own getter: called with a value it sets and returns
it, called with none it returns what is set.

| Method | Notes |
|---|---|
| `filename($filename = null)` | Absolute path of the file to send |
| `saveas($saveas = null)` | Name the browser should save it under. Run through `basename()` |
| `disposition($disposition = null)` | `attachment` or `inline`. Anything else becomes `inline` |
| `acceptranges($acceptranges = null)` | Whether to advertise byte-range support |
| `setContentType($contentType = null)` | Override the type instead of detecting it |
| `allowXsendFile()` | Hand the transfer to Apache when possible |

The constructor sets the disposition to `inline`, so a viewer only has to
set `filename()`. `attachment` asks the browser to save rather than
display, and is what `saveas()` names:

```php
$server = new Server();
$server->filename(PATH_APP . DS . 'site' . DS . 'bookings' . DS . $booking->get('id') . DS . $file);
$server->disposition('attachment');
$server->saveas('calibration.pdf');
$server->serve();
```

Two convenience methods skip the setters:

| Method | Equivalent to |
|---|---|
| `serve_attachment($filename, $saveas = null, $acceptranges = true)` | disposition `attachment`, then `serve()` |
| `serve_inline($filename, $acceptranges = true)` | disposition `inline`, no save-as, then `serve()` |

## X-Sendfile

`allowXsendFile()` turns on delegation to `mod_xsendfile`: instead of PHP
reading the file and echoing it, the server sends an `X-Sendfile` header
and Apache streams it. It takes effect only when `apache_get_modules()`
reports the module **and** `allow_xsendfile` is set to `1` in the global
configuration, so calling it on a hub without either is harmless.

The file service provider — the path that serves everything under the
hub's file area — turns it on:

<!--include: core/bootstrap/Files/Providers/FileServiceProvider.php:39-47-->

## Checking a path

`Server::valid($filename)` is static and answers whether a path is safe to
serve. It rejects anything that looks like an absolute URL, a call back
into `index.php`, a Windows drive letter, a backslash, or a path containing
`..`. It is a filter on user-supplied names, not a check that the file
exists or that the caller may read it.

```php
if (!Server::valid($file))
{
    App::abort(404, Lang::txt('File not found'));
}
```

Confirm the file exists and that the current user is entitled to it before
serving. `Server` does neither; give it a path you have already resolved
against a directory you control, never one assembled straight from the
request.

The order to write it in:

1. Load the record the file belongs to, by id, from your own table.
2. Check the current user may see that record.
3. Build the path from the record — the directory from `PATH_APP` and the
   record's id, the name from the record's own column.
4. `Filesystem::exists()`.
5. Serve.

Taking the filename from the request and validating it with
`Server::valid()` is not step 3. `valid()` stops a path escaping upwards;
it has nothing to say about whether this member may read this file, so a
member who swaps one booking id for another in the URL gets somebody else's
report and no error.
