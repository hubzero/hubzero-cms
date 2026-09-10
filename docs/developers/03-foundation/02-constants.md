<!--
status: rewritten
reviewed-against: 2.4-main @ d48e29db14
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/foundation/constants
-->
# Constants

Every request defines these before anything else runs, in
[`core/bootstrap/app.php`](../../../core/bootstrap/app.php). Extensions may
rely on them anywhere.

## Paths

All of these are absolute filesystem paths, not URLs, and none has a
trailing separator.

| Constant | Value |
|---|---|
| `PATH_ROOT` | The installation's root directory, the one holding `core/` and `app/`. |
| `PATH_CORE` | The platform: `PATH_ROOT/core`. Components, plugins, modules, templates, and the framework libraries the release ships. |
| `PATH_APP` | One hub's own directory: `PATH_ROOT/app`. Its configuration, its overriding extensions and templates, its uploads, cache, and logs. |
| `DS` | `DIRECTORY_SEPARATOR`, so `/` on every platform Hubzero supports. |

`PATH_ROOT`, `PATH_CORE`, and `PATH_APP` are only defined if nothing has
defined them already. The site entry point sets `PATH_ROOT` from the web
server's document root and reads `PATH_CORE` from the environment, which is
how a deployment can keep the platform outside the document root.

Write paths by joining with `DS`, and never assume a hub's files live under
the code:

```php
$path = PATH_APP . DS . $resourcePath . DS . 'content' . DS . $name;
```

The same join appears in `com_resources`, which builds every media path this
way rather than concatenating separators of its own:

<!--include: core/components/com_resources/helpers/hubpresenter.php:111-111-->

## Version

`HVERSION` is the platform version as a string, currently
<!--include: core/bootstrap/app.php:39-39-->

## Entry guard

`_HZEXEC_` is defined by the bootstrap and by nothing else. Files that must
never be requested directly over HTTP open with a guard that stops them when
it is absent:

```php
defined('_HZEXEC_') or die();
```

Every view template and every file outside a class carries it. A file that
only declares a namespaced class does not need it, because reaching it
directly produces no output.

## Legacy path aliases

The platform keeps an older set of path constants so that inherited code
still resolves. New code should use the `PATH_` constants
above; these are listed for reading old extensions.

| Alias | Same as |
|---|---|
| `JPATH_ROOT`, `JPATH_BASE`, `JPATH_SITE` | `PATH_ROOT` |
| `JPATH_CONFIGURATION` | `PATH_APP/config` |
| `JPATH_THEMES` | `PATH_APP/templates` |
| `JPATH_CACHE` | `PATH_APP/cache` |
| `JPATH_LIBRARIES` | `PATH_CORE/libraries` |
| `JPATH_PLUGINS` | `PATH_CORE/plugins` |
| `JPATH_MANIFESTS` | `PATH_CORE/manifests` |
| `JPATH_ADMINISTRATOR` | `PATH_ROOT/administrator` |
| `JPATH_API` | `PATH_ROOT/api` |
| `JPATH_INSTALLATION` | `PATH_ROOT/installation` |

> **Note:** Several of these name directories a current installation does not
> have. They are computed unconditionally, so a constant existing says
> nothing about the directory existing.

## Locale

The bootstrap fixes the process locale before any extension runs: the default
time zone is UTC and the internal encoding is UTF-8. Dates are stored and
compared in UTC and converted for display, so do not change the default time
zone. See [Dates](../05-basics/10-dates.md).
