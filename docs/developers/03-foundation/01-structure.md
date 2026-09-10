<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/foundation/structure
source-id: 3438
modified: 2015-07-06
imported: 2026-09-09
-->
# Application structure

A Hubzero installation is two trees and one entry point. `core/` is the
platform as the release ships it. `app/` is everything that makes one hub
different from another. `index.php` is the only file a web request ever
reaches.

```
hubzero-cms/
    index.php       the single entry point
    core/           the platform
    app/            this hub
    docs/           this documentation
    gh-pages/       the documentation site builder
    tools/          linters and documentation generators
    docker/         database containers used by the test suite
    .github/        CI workflows
```

Only the first three matter at runtime. The rest is repository furniture.

## `core/`

The platform: the framework, every extension the release ships, and the
command-line tool. An upgrade replaces this tree wholesale, so nothing you
write belongs in it unless you are contributing the change back.

| Directory | What is in it |
|---|---|
| `bootstrap/` | Per-client service and facade lists, routes, and the shared language files. |
| `libraries/Hubzero/` | The framework. One directory per subsystem: `Base`, `Database`, `Config`, `Language`, `Routing`, `Session`, `Form`, `View`, and the rest. |
| `components/` | `com_blog`, `com_groups`, `com_resources` … the shipped components. |
| `plugins/` | Grouped by what they extend: `plugins/authentication/`, `plugins/content/`, `plugins/members/`. |
| `modules/` | `mod_login`, `mod_menu` … the shipped modules. |
| `templates/` | `kimera` (site), `kameleon` (administrator), plus `system`, `lucent` and `welcome`. |
| `migrations/` | Platform migrations, the ones not owned by a single extension. |
| `assets/` | Shared CSS, JavaScript and images, including `assets/js/hubzero.js`. |
| `bin/` | `muse`, `composer`, `lessc`. |
| `vendor/` | Composer dependencies. Not in the repository; `composer install` creates it. |

Refer to paths under here with the `PATH_CORE` constant, never a literal
`/core`. See [Constants](02-constants.md).

## `app/`

One hub's own directory: its configuration, its uploads, its cache and logs,
and any extension it adds or overrides. It is **not in the repository** —
`app/**` is the first line of `.gitignore` — because it is a running hub's
local state, not shipped code. `muse install` creates it, and everything
inside it is that installation's.

| Directory | What is in it |
|---|---|
| `config/` | The configuration, one PHP file per group: `database.php`, `mail.php`, `session.php`, `cache.php`, `app.php`. Created mode `0770`. |
| `components/`, `modules/`, `plugins/`, `templates/` | This hub's own extensions, and its replacements for core ones. |
| `bootstrap/` | Optional. Extra service providers and facade aliases, and the language overrides. |
| `site/` | Uploaded content — group files, media, project repositories. |
| `cache/` | Generated content, sub-divided by client: `admin`, `site`, `api`, `cli`. Nothing in it is precious. |
| `logs/` | The hub's logs. |

Only `config/` is created at install time. The others appear when something
needs them, so a healthy hub can be missing most of this list.

Refer to paths under here with `PATH_APP`.

### Overriding a core extension

The loaders check `app/` before `core/` and take the first directory they
find. Put `app/components/com_blog` on disk and nothing under
`core/components/com_blog` is used again — it is a whole-extension
replacement, not a merge, and the copy will not receive upgrades. To change
a few files, use a [template override](../templates/overrides.md) instead.

[`Hubzero\Base\ClassLoader`](../../../core/libraries/Hubzero/Base/ClassLoader.php)
applies the same rule to classes. It maps the extension namespaces to
directories in both trees:

| Namespace | Looked for in |
|---|---|
| `Components\Blog\Models\Entry` | `{app,core}/components/com_blog/Models/Entry.php` |
| `Modules\Menu\Helper` | `{app,core}/modules/mod_menu/Helper.php` |
| `Plugins\System\Debug\Helper` | `{app,core}/plugins/system/debug/Helper.php` |
| `Templates\Kimera\Helper` | `{app,core}/templates/kimera/Helper.php` |

Composer's PSR-4 map covers only `Hubzero\` and `Bootstrap\`; every
extension class comes through this loader, which also tries a lowercase
variant of each path so that the older lowercase filenames still resolve.

> **Note:** There are no `/administrator` and `/api` directories. Earlier
> releases had one entry point per client; this one has a single `index.php`
> and works the client out from the URL. The `JPATH_ADMINISTRATOR` and
> `JPATH_API` constants still exist and still compute, but they name
> directories that are not there.

## The request lifecycle

Every request, whatever the URL, runs
[`index.php`](../../../index.php):

<!--include: index.php:9-22-->

That is the whole entry point. `PATH_ROOT` comes from the web server's
document root and `PATH_CORE` from the environment, so the platform can live
outside the document root. Composer's autoloader pulls in
[`core/bootstrap/app.php`](../../../core/bootstrap/app.php) — it is listed
under `autoload.files` — which defines the constants, registers the class
loader, and declares the `app()`, `config()` and `dump()` helpers. Then one
object is built and `run()` is called on it.

### Boot

[`Hubzero\Base\Application`](../../../core/libraries/Hubzero/Base/Application.php)
is the container and the application both; it extends
`Hubzero\Container\Container`. Its constructor builds the `request` and
`response` objects and binds itself under `app`, so a facade can resolve the
application out of its own container.

`run()` then calls `boot()`, which calls `load()`, which does four things in
order:

1. **Detect the client.**
   [`ClientDetector`](../../../core/libraries/Hubzero/Base/ClientDetector.php)
   picks one of `site`, `administrator`, `api`, `cli`, `install` or `files`.
   A command-line invocation is `cli`. A web request with no
   `app/config/database.*` on disk is `install` — that is what puts the
   installer in front of a fresh checkout. Otherwise the first URL segment
   decides: `/administrator/…` is the administrator interface, `/api/…` the
   REST API, and anything else the site.

2. **Load the configuration.** A
   [`Hubzero\Config\Repository`](../../../core/libraries/Hubzero/Config/Repository.php)
   is built for that client and bound as `config`. It has to come first,
   because the service providers read it.

3. **Register the service providers.** The list is read from up to three
   files and merged in order, so a hub can add its own without touching the
   platform:

   ```
   core/bootstrap/<client>/services.php
   core/bootstrap/<Client>/services.php
   app/bootstrap/<client>/services.php
   ```

   Both spellings of the core path are tried because the directories are
   capitalised — `core/bootstrap/Site/` — while the client name is not. Each
   provider is registered, not booted. See
   [Service providers](03-providers.md).

4. **Register the facades.** The alias lists are read from the matching
   `aliases.php` files and merged the same way, then registered as
   root-namespace class aliases. See [Facades](04-facades.md).

`boot()` then calls `boot()` on every provider that has one, and `run()`
starts the error handlers and fires `system.onAfterInitialise`.

### Handle

The providers that are also middleware are collected and the request is sent
through them as a stack:

```php
$this['stack'] = new Stack($this);

$this['stack']
    ->send($this['request'])
    ->through($this->middleware($this->serviceProviders))
    ->then(function($request, $response)
    {
        $response->prepare($request);
        $response->send();
    });
```

A provider joins the stack by extending
[`Hubzero\Base\Middleware`](../../../core/libraries/Hubzero/Base/Middleware.php)
rather than `ServiceProvider`. On the site three do: the router, the
component dispatcher, and the document — which is where the template renders.
Each may act on the request, hand it onward, and act on the response coming
back, so the component that owns the URL runs part-way down the stack and the
template wraps its output on the way out. The other twenty-two providers only
register services.

> **Note:** Every provider is registered on every request. Registration is
> cheap — it assigns a closure to a container key — and the *service* is
> lazy, but there is no mechanism for skipping a provider whose service is
> never asked for.
