<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
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

## When to write one

A component gets an API controller when something outside the page needs the
data: a script that loads a term's instrument bookings overnight, a lab's own
front end, a mobile client, or the component's own JavaScript fetching a
month of the calendar without reloading the page. If the only caller is a
form on a page the component already renders, a site controller task is
less work and gives you the session, the document and the template for free.

The three controller kinds are the same class hierarchy with different
surroundings, so the split is about what the caller is, not about what the
code does. Where the logic is the same for both, put it in a model or a
helper and have the site controller and the API controller both call it.
Duplicating it is how the two faces start disagreeing.

## The client

`/api` is not a directory. `ClientDetector` sees `api` as the first URL
segment and selects the API client, which loads its own service providers
and facade aliases from `core/bootstrap/Api/` — a different response object,
a different authentication provider, no template, no document. See
[Structure](03-foundation/01-structure.md#boot).

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

### The smallest working endpoint

An API controller that declares no tasks at all is not empty.
`ApiController` ships `listTask()`, `readTask()`, `createTask()`,
`updateTask()` and `deleteTask()`, each of which resolves a
[`Relational`](../../core/libraries/Hubzero/Database/Relational.php) model
from the controller's own name — singularised, in
`Components\[Name]\Models\` — and does the obvious thing with it. So a
`Bookingsv1_0` controller in an instrument-booking component gets CRUD over
`Components\Bookings\Models\Booking` for free, and you override only the
tasks whose behaviour has to differ. Set `protected $_model` if the class
name is not the singular of the controller name; no core controller does.

That inheritance is also a trap. When the model cannot be resolved the
request aborts — 404 if the derived file path does not exist, 500 if it is
unreadable or defines a different class — and nothing says the task you meant
to write is missing rather than broken. And these five tasks are documented
on the base class, with `{component}` and `{controller}` placeholders in
`@apiUri`, so the explorer lists them for every controller. The generated
reference reads only files under `core/components/*/api/controllers/`, so it
does not: an endpoint you inherited appears on a hub's `/developer/api/docs`
and not in the [API reference](../reference/api/README.md).

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

<!--include: core/components/com_blog/api/controllers/entriesv1_0.php:29-40-->

More `@apiParameter` blocks follow, one per parameter, and then
`public function listTask()`.

The rules, and what breaks when you get them wrong:

| Tag | What it does | If it is missing |
|---|---|---|
| The first line of the docblock | The endpoint's summary | The explorer **drops the endpoint** and records *Missing docblock for method* in its error list. The reference still lists it, with a blank summary |
| `@apiMethod` | The HTTP verb | The reference assumes `GET`. Nothing routes off this tag, so a `POST` endpoint documented without it reads as a `GET` and the caller's first request fails |
| `@apiUri` | The path the caller requests | The reference invents `/{component}/{task}`, which is right only by luck |
| `@apiParameter` | One JSON object per parameter | The parameter is simply undocumented; the caller finds it by reading your controller |

An endpoint with **neither** `@apiMethod` nor `@apiUri` is skipped outright
by the reference generator, so a new task is invisible until you write them.

`@apiParameter` takes a JSON object, one per tag, with `name`, `description`,
`type`, `required` and `default`; `allowedValues` is used where a parameter
is an enumeration. Both readers parse it as JSON, so a trailing comma or a
missing quote costs you the parameter — the explorer records *Unable to parse
parameter info*, and the reference generator falls back to a loose regular
expression that may pick up some fields and not others. Neither says anything
on the page itself.

> **Note:** The two readers are separate implementations of the same
> convention. `Generator` uses `phpDocumentor`'s reflection over the loaded
> class; `gen_api_reference.py` matches the docblock with a regular
> expression that requires `public function <name>Task(`. A task declared
> `protected`, or a docblock separated from its signature by anything else,
> is in one and not the other.

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
5. Regenerate the reference: `python3 tools/docs/gen_api_reference.py`. The
   documentation workflow regenerates it too and fails if your committed copy
   differs.

What the mistakes look like:

- **The class name does not match the file.** The router builds
  `Components\[Name]\Api\Controllers\[Controller]v[Major]_[Minor]` from
  the request; get a letter wrong and the request 404s with nothing in the
  log to say why.
- **You echoed instead of sending.** The output lands ahead of the serialised
  body and the caller's JSON parser fails on the first character.
- **You skipped the authorisation check.** It works, which is the problem.
  Nothing upstream of an API controller checks anything for you.
