<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/basics/debugging
-->
# Debugging

Two things have to be switched on before a hub tells you anything useful:
the global **Debug System** setting, and the **System - Debug** plugin that
renders what it collects. Together they give you the queries that ran, how
long each stage took, which language strings were missing, and a place to
dump your own variables.

## On a hub you cannot restart

That is the usual case: the hub is running, other people are using it, and
you have an administrator login and perhaps a shell — not the ability to
edit `php.ini`, restart PHP, or deploy a build with a debugger in it.

Everything on this page works under those conditions.

| You have | What you get |
|---|---|
| An administrator login | Debug mode, the debug panel, and error reporting — all switched from a screen, all live on the **next request**. No restart of anything |
| An administrator login on a **public** hub | The same, restricted to your own account with the debug plugin's **Allowed Users** parameter, so nobody else sees the panel |
| A shell | The log files under `/var/log/hubzero`, which are written whether or not debug mode is on, and [muse](../12-muse.md) for running code outside the web request |
| Neither | Nothing. Ask for one |

The reason the settings take effect immediately is that Global
Configuration writes `app/config/*.php` and every request reads those files
fresh. There is nothing compiled and nothing cached across the change.

Two habits follow from working on a live hub. Use `dlog()` rather than
`dump()`, because it does not write into the page. And turn the panel back
off when you are done — it prints every query, with values, to whoever is
looking.

## Turning it on

In the administrator, **System → Global Configuration → System**, under
**Debug Settings**:

| Field | Effect |
|---|---|
| **Debug System** | Collects query and profiling data, and shows stack traces on error pages |
| **Profile System** | Records timing marks through the request |
| **Debug Language** | Marks untranslated strings on the page and stops the default language being loaded first |
| **Error Reporting** | `System Default`, `Maximum`, `Relaxed` or `None` |

Then enable **System - Debug** under **Extensions → Plugins** and open it.
Its parameters decide what the panel shows — **Show Profiling**, **Show
Queries**, **Show Memory Usage**, a theme, and separate switches for
language error files, missing files and unused strings.

> **Warning:** Debug output includes every query, with values. Do not leave
> it on for a production hub.

### Restricting who sees it

The debug plugin has two parameters for exactly this, and on any hub that is
reachable from outside you should set at least one:

- **Allowed Groups** — a user group picker; only members of the chosen
  groups see the panel.
- **Allowed Users** — a comma-separated list of usernames.

With both empty, everyone who loads the page gets the debug panel.

## Dumping variables

Three global functions are defined during bootstrap and are available
everywhere, unqualified, in namespaced files as well as plain ones:

| Function | What it does |
|---|---|
| `dump($var, ...)` | Renders each argument and echoes it inline |
| `ddie($var, ...)` | The same, then `die()` |
| `dlog($var, ...)` | Sends each argument to the debug panel instead of the page |

```php
dump($fields);

ddie($row->toArray(), $params->toArray());
```

They are thin wrappers over
[`Hubzero\Debug\Dumper`](../../../core/libraries/Hubzero/Debug/Dumper.php),
whose static `dump()`, `stop()` and `log()` do the same work if you would
rather name the class. The dumper collects variables and hands them to a
renderer, so output is formatted rather than a raw `print_r()`.

`dump()` is declared inside a `function_exists()` guard, so on a hub where
something else has already defined a global `dump()` you are calling that
one. `ddie()` and `dlog()` are not guarded. Name
`Hubzero\Debug\Dumper::dump()` where it matters.

`dlog()` is the one worth remembering, and on a live hub it is the only one
of the three that is safe. It writes nothing to the page, so it does not
disturb the layout, break a redirect — which sends headers — or corrupt a
JSON response. The values appear in the debug panel afterwards.

`dump()` inside a controller that then redirects prints into a body nobody
sees, and `dump()` inside an AJAX task makes the response fail to parse with
no clue as to why. Both look like the call did nothing.

`dlog()` needs debug mode and the debug plugin to be on; without them the
values go nowhere, silently. When you want a record regardless, write to the
log instead — see below.

> **Note:** There is no `Hubzero\Utility\Debug` class. Older documentation
> named one; the dumper is `Hubzero\Debug\Dumper`, and the three global
> functions above are the intended way to reach it.

## Profiling

`App::get('profiler')` is a
[`Hubzero\Debug\Profiler`](../../../core/libraries/Hubzero/Debug/Profiler.php).
`mark($label)` records a point in the request; `marks()`, `duration()`,
`memory()` and `summary()` read the result. The application marks its own
stages — `afterDispatch` and the rest — and the debug panel renders them.

```php
if ($profiler = App::get('profiler'))
{
    $profiler->mark('afterMyExpensiveThing');
}
```

> **Note:** The `profiler` binding always exists but resolves to `null`
> unless `debug` or `profile` is on, so `App::has('profiler')` is not the
> test to use. Check the resolved value, as the bootstrap providers do.

## Logs

`App::get('log')` is a
[`Hubzero\Log\Manager`](../../../core/libraries/Hubzero/Log/Manager.php)
with three loggers registered at boot, writing to `/var/log/hubzero` where
that exists and to the configured `log_path` otherwise:

<!--include: core/bootstrap/Site/Providers/LogServiceProvider.php:39-58-->

```php
App::get('log')->logger('debug')->info('released ' . $count . ' expired holds');
```

This is the tool for anything you cannot watch happen: a cron task, an
overnight import, a path that only fails for one member. The file is on disk
and needs no debug mode, no plugin, and no browser.

A logger accepts the PSR levels — `debug`, `info`, `notice`, `warning`,
`error`, `critical`, `alert`, `emergency` — and each call also fires an
`onLog` event, so a plugin can watch everything that is logged.

The `Log` facade adds two shorthands for the loggers that have a dedicated
file:

```php
Log::auth($message);   // cmsauth.log
Log::spam($message);   // cmsspam.log
```

`register($name, $settings)` adds a logger of your own, with `file`,
`level`, `format` and `dateFormat` keys.

## "Illegal variable ... passed to script"

> **Important:** Illegal variable \_files or \_env or \_get or \_post or
> \_cookie or \_server or \_session or globals passed to script.

This means a request variable had a purely numeric key — `$_POST[5]` — which
happens when a form has a numerically named field:

```html
<input type="text" name="5" />
```

Give the field a name with at least one non-numeric character:

```html
<input type="text" name="n5" />
<input type="text" name="field[5]" />
```

The second form is the usual fix, since the outer name is not numeric and
the index survives.
