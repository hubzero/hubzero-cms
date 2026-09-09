<!--
status: rewritten
reviewed-against: 2.4-main @ d48e29db14
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/foundation/facades
-->
# Facades

## Overview

Facades serve as "static proxies" to underlying classes in the service container. This provides flexibility over traditional static methods with the benefit of terser syntax.

## Use

In the context of a hub, a facade is a class that provides access to an object from the container. For this to work, all facades extend the base `Hubzero\Facades\Facade` class.

A facade class only needs to implement a single method: `getAccessor`, which defines what to resolve from the container. The base Facade class makes use of the `__callStatic()` magic-method to defer calls from the facade to the resolved object.

Below is the facade for the Filesystem wherein the `getAccessor()` method returns the string 'filesystem', which is the key that the Filesystem service is registered with on the application.

```php
class Filesystem extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'filesystem';
    }
}
```

In the example below, a call is made to Filesystem to check that a file exists. Looking quickly at the code, one might assume that the static method `exists()` is being called on the `Filesystem` class:

```php
<?php

namespace Components\Blog\Site\Controllers;

use Hubzero\Component\Site\Controller;
use Filesystem;
use App;

class Media extends SiteController
{
    public function downloadTask()
    {
        //...

        if ( ! Filesystem::exists($file))
        {
            App::abort(404, 'File not found');
        }

        //...
    }
}
```

This facade serves as a proxy to accessing the underlying implementation of the `Hubzero\Filesystem\Filesystem` interface. So, when any static method on the facade is referenced, the application resolves the binding from the service container and runs the requested method against that object. In short, any calls made using the facade will be passed to the underlying instance of the filesystem service.

## Class Reference

Below is a list of every facade, its underlying class, and the service container binding key where applicable.

| Facade | Class | Service Key | Client |
|---|---|---|---|
| App | Hubzero\\Base\\Application | app | all |
| Auth | Hubzero\\Auth\\Manager | auth | admin, site, api |
| Cache | Hubzero\\Cache\\Manager | cache | admin, site |
| Component | Hubzero\\Component\\Loader | component | admin, site, api |
| Config | Hubzero\\Config\\Repository | config | all |
| Date | Hubzero\\Utility\\Date |  | all |
| Document | Hubzero\\Document\\Manager | document | admin, site |
| Event | Hubzero\\Events\\Dispatcher | dispatcher | all |
| Filesystem | Hubzero\\Filesystem\\Filesystem | filesystem | all |
| Html | Hubzero\\Html\\Builder | html.builder | admin, site |
| Lang | Hubzero\\Language\\Translator | language | all |
| Log | Hubzero\\Log\\Writer | log.debug | all |
| Module | Hubzero\\Module\\Loader | module | admin, site |
| Notify | Hubzero\\Notification\\Handler | notification | admin, site |
| Pathway | Hubzero\\Pathway\\Trail | pathway | site |
| Plugin | Hubzero\\Plugin\\Loader | plugin | all |
| Request | Hubzero\\Http\\Request | request | all |
| Response | Hubzero\\Http\\Response | response | all |
| Router | Hubzero\\Routing\\Router | router | all |
| Session | Hubzero\\Session\\Manager | session | admin, site |
| Toolbar | Hubzero\\Html\\Toolbar | toolbar | admin |
| Submenu | Hubzero\\Html\\Toolbar | submenu | admin |
| User | Hubzero\\User\\Manager | user | all |

## Importing a facade

This is the one thing to get right. The facades are registered as aliases in
the **root** namespace, in `core/bootstrap/<client>/aliases.php`. Almost every
file you write is namespaced, and inside a namespace an unqualified name
resolves against that namespace first:

```php
namespace Components\Blog\Site\Controllers;

class Entries extends SiteController
{
    public function displayTask()
    {
        // resolves to Components\Blog\Site\Controllers\Route, which does not
        // exist: "Class not found", fatal, the moment this line runs
        $url = Route::url('index.php?option=com_blog');
    }
}
```

Import each facade you use, and the name resolves to the alias:

```php
namespace Components\Blog\Site\Controllers;

use Route;
use App;
use Lang;
```

Writing `\Route::url(...)` works too, but the import is the house style.

> **Warning:** Nothing catches a missing import before the line runs. The file
> parses, autoloading has nothing to complain about, and an error path that is
> rarely taken can carry the fault for years.
> `php tools/lint/missing-facade-imports.php` finds them; it runs in
> continuous integration on every push.

## The facades

| Facade | Container key | Notes |
|---|---|---|
| `App` | `app` | `App::get('db')`, `App::abort()`, `App::redirect()` |
| `Cache` | `cache.store` |  |
| `Component` | `component` | `Component::params('com_blog')` |
| `Config` | `config` | `Config::get('sitename')` |
| `Date` | `date` | `Date::of('now')->toSql()` |
| `Document` | `document` | titles, stylesheets, scripts, feeds |
| `Event` | `dispatcher` | `Event::trigger('content.onContentSave', [...])` |
| `Filesystem` | `filesystem` |  |
| `Html` | `html.builder` |  |
| `Lang` | `language` | `Lang::txt('COM_BLOG_TITLE')` |
| `Log` | `log` |  |
| `Module` | `module` |  |
| `Notify` | `notification` | `Notify::success()`, `Notify::error()` |
| `Pathway` | `pathway` |  |
| `Plugin` | `plugin` |  |
| `Request` | `request` | `Request::getInt('id', 0)` |
| `Response` | `response` |  |
| `Route` | `router` | `Route::url('index.php?option=com_blog')` |
| `Session` | `session` |  |
| `Submenu` | `submenu` | administrator only |
| `Toolbar` | `toolbar` | administrator only |
| `User` | `user` | `User::get('id')`, `User::isGuest()`, `User::authorise()` |

Not every client registers every one. The site and administrator applications
register the full set above; the API and command-line applications register
fewer, so `Toolbar` and `Submenu` are meaningless outside the administrator.
Each client's list is its own `aliases.php`.

## Testing

Because a facade resolves through the container, a test can put a double in
the container under the same key and the facade will return it. That is the
practical reason to prefer a facade over calling the underlying class
directly.
