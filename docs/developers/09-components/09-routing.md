<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/components/routing
-->
# Routing

Every component is reachable by query string: `option` names it, and
everything else is a request variable.

```
https://yourhub.org/index.php?option=com_kb&task=category&categoryAlias=printing
```

With search engine friendly URLs turned on the same request looks like this:

```
https://yourhub.org/kb/printing
```

The first path segment identifies the component; if no component matches, the
application falls back to matching an article, category, or page alias. The
remaining segments are the component's to interpret, and a `router.php` is
what interprets them. A component without one still works — it just has ugly
URLs.

## Where the router goes

`Component::router()` looks for a class named
`Components\{Name}\{Client}\Router`, so `com_kb`'s site router is
`Components\Kb\Site\Router`. If the class is not already loaded it tries these
files, in order, and includes the first that exists:

1. `{component}/{client}/routerv{version}.php`, when a version was requested;
2. `{component}/{client}/router.php`;
3. `{component}/router.php`.

Each client gets its own. `com_kb` has one for the site and one for the API,
and they parse quite different URLs.

The class must implement
[`Hubzero\Component\Router\RouterInterface`](../../../core/libraries/Hubzero/Component/Router/RouterInterface.php) —
this is checked by reflection, and a class that does not is ignored without
comment. Extending
[`Hubzero\Component\Router\Base`](../../../core/libraries/Hubzero/Component/Router/Base.php)
satisfies the interface and supplies a pass-through `preprocess()`, leaving you
two methods to write.

## `build()`

`build(&$query)` is called by `Route::url()`. It receives the query as an
array, minus `option`, and returns the path segments to put after the
component name, **in the order they should appear**. Anything it consumes it
must `unset()` from `$query`; whatever is left over is appended as a query
string.

Here is `com_kb`'s:

<!--include: core/components/com_kb/site/router.php:19-93-->

Read it as a sequence of optional slots: an optional task, then section,
category, alias, id, and vote. `task=article` is dropped entirely, because an
article URL is just its path. `controller` is dropped because `com_kb` has
only one. So:

```php
Route::url('index.php?option=com_kb&category=printing&alias=duplex');
// -> /kb/printing/duplex
```

## `parse()`

`parse(&$segments)` is the inverse, and it runs on every page view of the
component. It receives the path segments after the component name and returns
the request variables they mean.

<!--include: core/components/com_kb/site/router.php:101-121-->

`com_kb` switches on the number of segments rather than reading them
positionally, because one segment means a category, two may mean either a
sub-category or an article, and three or four may end in `comments.rss`.

> **Note:** Position is everything. `example/view/123/rss` and
> `example/rss/view/123` are different URLs and a positional parser will read
> the second as nonsense. Where two shapes are genuinely ambiguous — as
> `com_kb`'s two-segment case is — the router has to look the value up to
> decide, and `com_kb` runs a category query to do exactly that.

> **Warning:** `parse()` runs on every request to the component, including
> ones that will 404. A database query inside it is a query on every page load.
> Keep it cheap, and never let it throw — an exception in `parse()` happens
> before the component is loaded, so there is nothing sensible to show.

## `preprocess()`

`preprocess($query)` runs on every URL built for the component, whether SEF is
on or not. It exists to complete a query — supplying a missing `Itemid`,
forcing a language. `Base` returns the query untouched, which is what nearly
every component wants.

## What happens without a router

`Component::router()` always returns something. When no router class is found
it falls back to one of two generic implementations:

- [`DefaultRouter`](../../../core/libraries/Hubzero/Component/Router/DefaultRouter.php),
  for a component with no `{name}.php` entry point and no
  `controllers/{name}.php`. It maps the first three segments to `controller`,
  `task`, and `id`, and builds them back in that order.
- [`Legacy`](../../../core/libraries/Hubzero/Component/Router/Legacy.php),
  for everything else. It looks for global functions named
  `{Name}BuildRoute()` and `{Name}ParseRoute()` — the pre-namespace convention —
  and returns an empty array when they do not exist, which is what leaves a
  component with query-string URLs.

## The API router

An API router is the same interface against a different URL space. `com_kb`'s
names a default controller and reads a numeric first segment as a record id:

<!--include: core/components/com_kb/api/router.php:48-76-->

`/api/kb/list` reaches `listTask()`; `/api/kb/42` reaches `readTask()` with
`id=42` on a GET. The controller name it produces — `entries` — is what the
API loader turns into `entriesv1_0.php`. See
[Controllers](controllers.md#api-controllers).

## Building URLs

Never assemble a component URL by hand. `Route::url()` runs `preprocess()`,
then `build()`, then adds the base path and whatever is left of the query:

```php
$url = Route::url('index.php?option=com_kb&category=' . $category . '&alias=' . $alias);
```

Pass `false` as the second argument for an unencoded URL — the administrator
uses that form when the result goes into a redirect rather than into markup.
