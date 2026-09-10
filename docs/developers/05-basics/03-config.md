<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/basics/config
-->
# Config

Four kinds of configuration exist on a hub, and each is reached a different
way: the global settings, and the parameters belonging to a component, a
plugin, or a module. All four end up as a
[`Hubzero\Config\Registry`](../../../core/libraries/Hubzero/Config/Registry.php)
with the same `get()` on it.

Which one you want depends on who is meant to set the value:

| The value is | Where it belongs | How to read it |
|---|---|---|
| The same for the whole hub — the site name, the time zone, the mail server | Global configuration | `Config::get('offset')` |
| A setting for your component that an administrator changes once — how many bookings a page lists | The component's `config/config.xml` | `Component::params('com_bookings')` |
| A setting for one instance of a plugin | The plugin's manifest | `$this->params` inside the plugin |
| A setting for one placement of a module on one page | The module instance | `$this->params` inside the module |

A value that a *member* sets is none of these. That is a user parameter —
`User::getParam()`, see [users](05-user.md) — or a column on one of your own
tables.

> **Warning:** A parameter that is not declared in the extension's XML can
> still be read, and comes back as your default. Nothing reports that the
> field does not exist, so a typo in a parameter name looks exactly like an
> administrator who has not set it yet. `com_tags` has three of these:
> `show_sizes`, `show_tag_count` and `show_tags_sort` are read by its layout
> and declared in no manifest, so the tag cloud takes its defaults on every
> hub. Check the name against the XML when a setting appears to have no
> effect.

## Global configuration

The site settings live in `app/config/`, one PHP file per group, each
returning a plain array:

```php
// app/config/app.php
return array(
    'secret'        => '26a6751ffda14c...',
    'sitename'      => 'myhub',
    'debug'         => '0',
    'offset'        => 'America/New_York',
    'site_template' => 'hubzero',
);
```

`Hubzero\Base\Application` builds a
[`Hubzero\Config\Repository`](../../../core/libraries/Hubzero/Config/Repository.php)
for the current client during boot — before any service provider runs,
because the providers read it — and binds it as `config`. Its file loader
globs every file in `app/config/` and keys the result by filename. Read it
through the facade:

```php
use Config;

$name   = Config::get('sitename');
$offset = Config::get('offset');
$limit  = Config::get('list_limit', 25);
```

The repository flattens the groups on read. `cache_handler` lives in
`app/config/cache.php` and `sitename` in `app/config/app.php`, but both are
reached by their bare name — `Config::get()` scans the groups for the key
before falling back to dotted-path lookup. That is why nothing in the tree
writes `Config::get('cache.cache_handler')`.

`App::get('config')` returns the same object, which also implements
`ArrayAccess`, `Countable` and `IteratorAggregate`, so `$config['sitename']`
and `foreach` work on it.

> **Note:** Configuration is read-only from an extension's point of view.
> `set()` exists on the registry, but it changes the in-memory copy for the
> rest of this request and nothing else. The administrator's Global
> Configuration screen is what writes the files back.

The [configuration reference](../../reference/configuration/README.md) lists
every option the tree declares.

## Component configuration

A component's options come from its `config/config.xml` and are stored in
the `#__extensions` row. Read them through the `Component` facade:

```php
use Component;

$params = Component::params('com_bookings');

$limit  = (int) $params->get('display_limit', 25);
$access = $params->get('access-view');
```

`Component::params($option, $strict = false)` returns a `Registry`. Inside a
controller extending `Hubzero\Component\SiteController` or
`AdminController` the same object is already on `$this->config`, so use that
rather than calling the facade again.

A menu item can override component parameters for the page it points at.
Where that matters, controllers merge the menu item's params over the
component's; the merged result is what the view should read.

## Plugin configuration

A plugin's own parameters are on `$this->params`, populated before any event
method runs:

```php
class plgSystemExample extends \Hubzero\Plugin\Plugin
{
    public function onAfterRoute()
    {
        $timeout = $this->params->get('timeout', 30);
    }
}
```

From outside the plugin, ask the `Plugin` facade for them by group and
name:

```php
use Plugin;

$params = Plugin::params('authentication', 'facebook');

$appid  = $params->get('app_id');
```

`Plugin::byType($type, $plugin = null)` returns the plugin records
themselves — `id`, `name`, `type`, `params` — when you need more than the
parameters. Note the argument order: the group first (`authentication`,
`content`, `cron`), the individual plugin second.

## Module configuration

A module's parameters are on `$this->params` in the module class, and are
the values an administrator entered on that module instance:

```php
namespace Modules\Featured;

use Hubzero\Module\Module;

class Featured extends Module
{
    public function display()
    {
        $limit = $this->params->get('limit', 5);

        require $this->getLayoutPath();
    }
}
```

The same object is available inside the module's layout as `$this->params`,
because the layout is included from within the module object.

## Reading a Registry

All four return the same class, so the same methods apply:

| Method | What it does |
|---|---|
| `get($path, $default = null)` | Read a value, dotted paths supported |
| `has($path)` | Whether the path is set |
| `set($path, $value)` | Set it, in memory |
| `def($key, $default = '')` | Set only if not already set |
| `merge($source, $recursive = false)` | Merge another registry or array over this one |
| `toArray()` / `toObject()` | Convert |
| `toString($format = 'json')` | Serialise; `json`, `ini`, `xml`, `php`, `yaml` |

```php
$params = Component::params('com_bookings');
$params->merge($menuParams);

if ($params->has('feed_email'))
{
    // ...
}
```

A value read out of a `Registry` is whatever was stored — usually a string,
even for a number or a checkbox. Cast it where the type matters:

```php
$limit = (int) $params->get('limit', 25);
```

`get('limit')` returns `"25"`, not `25`. It behaves as a number in
arithmetic and in a query, so the fault only surfaces where the type is
checked: a strict `=== 25` is false, `in_array($v, [1, 2], true)` is false,
and a parameter typed `int` on a method signature raises a `TypeError` under
`strict_types`. Cast on the way out of the registry, once.
