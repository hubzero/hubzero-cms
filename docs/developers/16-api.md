<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/api
source-id: 3532
modified: 2015-10-01
imported: 2026-09-09
-->
# REST API

Every hub serves a REST API under `/api/`. It is a third face on the same
components that serve the site and the administrator interface: 246
endpoints across 31 components, each one a method on a controller in that
component's `api/` directory.

The [API reference](../reference/api/README.md) lists every endpoint, its
method, its URI and its parameters, generated from the source. This page is
about how the API works and how to add to it.

## The client

`/api` is not a directory. `ClientDetector` sees `api` as the first URL
segment and selects the API client, which loads its own service providers
and facade aliases from `core/bootstrap/Api/` — a different response object,
a different authentication provider, no template, no document. See
[Structure](foundation/structure.md#boot).

## Routing

`core/bootstrap/Api/routes.php` parses the request in four steps:

1. **Strip `api/`** from the front of the path.
2. **Determine the version.** From a `/v1.0/` segment, an `Accept` header, or
   a `v=` or `version=` query variable. It defaults to `1.0`, and the
   normalised value ends up in `version`, `version.major` and
   `version.minor`.
3. **Map the HTTP method to a task.** `POST` becomes `create`, `PUT` becomes
   `update`, `DELETE` becomes `delete`. `GET` is left alone, because the task
   might be `list` or `read` or something the component invented.
4. **Match the component** by the next segment, then hand the rest of the
   segments to that component's API router.

So `GET /api/blog/list` reaches `Components\Blog\Api\Controllers\Entriesv1_0::listTask()`.

## Controllers

An API controller lives in `{component}/api/controllers/`, is named
`{name}v{major}_{minor}.php`, and extends
[`Hubzero\Component\ApiController`](../../core/libraries/Hubzero/Component/ApiController.php).
Every public method ending in `Task` is a task the router can reach.

```php
namespace Components\Blog\Api\Controllers;

use Hubzero\Component\ApiController;
use Request;
use User;

class Entriesv1_0 extends ApiController
{
    public function listTask()
    {
        $limit = Request::getInt('limit', 25);

        // ...

        $this->send($response);
    }
}
```

The version is in the class name, which is how two versions of an endpoint
coexist: add `Entriesv2_0` beside `Entriesv1_0` and a request that asks for
`v2.0` gets the new one while everything else keeps working.

`send()` hands an object to
[`Hubzero\Api\Response`](../../core/libraries/Hubzero/Api/Response.php),
which serialises it according to the `format` query variable — `json` (the
default), `xml`, `html`, `xhtml`, `text`, `php` or `php_serialized`. An
unrecognised value falls back to `json`. `ResponseServiceProvider` also
forces `debug` off and disables output compression, because either would
corrupt the body.

Two response modifiers run as middleware over the result: `UriBase` turns
relative paths into absolute URLs, and `JsonpCallable` wraps the body in a
callback when one is asked for.

> **Note:** Two more modifiers are written but switched off —
> `Hubzero\Api\Response\DateFormatter` and
> `Hubzero\Api\Response\ObjectExpander`, which would normalise dates and
> inline related user and group objects. Both are commented out in
> [`core/bootstrap/Api/services.php`](../../core/bootstrap/Api/services.php).
> Do not write an endpoint that assumes either has run.

### Documenting an endpoint

Endpoint documentation is docblock tags on the task method, and they are not
decoration — two things read them. `Hubzero\Api\Doc\Generator` builds the
interactive explorer a hub serves at `/developer/api/docs`, and
`tools/docs/gen_api_reference.py` builds the
[API reference](../reference/api/README.md) in these pages.

```php
/**
 * Display a list of entries
 *
 * @apiMethod GET
 * @apiUri    /blog/list
 * @apiParameter {
 * 		"name":          "limit",
 * 		"description":   "Number of results to return.",
 * 		"type":          "integer",
 * 		"required":      false,
 * 		"default":       25
 * }
 * @return  void
 */
public function listTask()
```

`@apiParameter` takes a JSON object. An endpoint with no `@apiMethod` and
`@apiUri` appears in neither place, so a new endpoint is undocumented until
you add them.

## Authentication

The API authenticates with OAuth 2.0 bearer tokens.
[`Hubzero\Api\Guard`](../../core/libraries/Hubzero/Api/Guard.php) reads the
token, resolves it to a user, and rejects the request if the token is
missing, expired, or points at a user who no longer exists. Anonymous
requests are allowed where the endpoint permits them; the user is then the
guest.

Tokens come from `POST /api/developer/oauth/token`. A user creates and
manages application credentials at `/developer` on the hub, which is
`com_developer`.

### From JavaScript on the hub itself

Asking a logged-in user to authenticate to the same site they are already
logged in to would be silly, so there is a grant type that trades the
session cookie for a token. `Hubzero.initApi()` in
[`core/assets/js/hubzero.js`](../../core/assets/js/hubzero.js) does it:

```php
Html::behavior('core');
```

```javascript
jQuery(document).ready(function($) {
    Hubzero.initApi(function() {
        // every $.ajax call from here on carries the token
    });
});
```

It POSTs `grant_type=session` to `/api/developer/oauth/token`, sets the
returned token as an `Authorization: Bearer` header on every subsequent
jQuery request through `$.ajaxSetup()`, and schedules itself to run again
when the token expires. `Html::behavior('core')` is what puts `hubzero.js`
on the page; without it `Hubzero` is undefined.

## Rate limiting

[`Hubzero\Api\RateLimit\RateLimitService`](../../core/libraries/Hubzero/Api/RateLimit)
is registered with the other API providers and enforces two windows,
configured in `app/config/rate_limit.php`:

```php
return array(
    'short' => array('period' => '5',    'limit' => '500'),
    'long'  => array('period' => '1440', 'limit' => '10000'),
);
```

`period` is in minutes. Counts are kept in the database.

## Adding an endpoint

1. Create `{component}/api/controllers/{name}v1_0.php` with a class
   extending `ApiController`.
2. Add a `{task}Task()` method, and give it `@apiMethod`, `@apiUri` and an
   `@apiParameter` for each parameter.
3. Check authorisation explicitly. An API controller has no menu item and no
   component parameters merged into it; nothing is checked for you.
4. Return data with `$this->send()`. Do not echo.
5. Regenerate the reference: `python3 tools/docs/gen_api_reference.py`.
